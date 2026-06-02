<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    private const SUGAR_OPTIONS = ['0%', '25%', '50%', '75%', '100%'];
    private const SIZE_OPTIONS = ['Small', 'Medium', 'Large'];

    public function index()
    {
        $categories = Product::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $selectedCategory = request('category');
        $products = Product::query()
            ->when($selectedCategory, fn ($query) => $query->where('category', $selectedCategory))
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        $productsByCategory = $products->groupBy(fn ($product) => $product->category ?: 'Uncategorized');

        return view('pos.index', compact('products', 'productsByCategory', 'categories', 'selectedCategory'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $quantity = max(1, (int) $request->quantity);
        $sugar = null;
        $validated = $request->validate([
            'size' => ['required', 'in:' . implode(',', self::SIZE_OPTIONS)],
        ]);
        $size = $validated['size'];
        $price = $this->priceForSize($product, $size);

        if ($quantity > $product->stock) {
            return redirect()->back()->with('error', 'Insufficient stock for ' . $product->name);
        }

        if ($this->isCoffeeProduct($product)) {
            $validatedSugar = $request->validate([
                'sugar' => ['required', 'in:' . implode(',', self::SUGAR_OPTIONS)],
            ]);
            $sugar = $validatedSugar['sugar'];
        }

        $cart = session()->get('cart', []);

        $cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => $quantity,
            'size' => $size,
            'sugar' => $sugar,
        ];

        session()->put('cart', $cart);

        return redirect()->back();
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(function ($i) {
            return $i['price'] * $i['quantity'];
        });
        $promoCode = session()->get('promo_code');
        $discountAmount = session()->get('discount_amount', 0);
        $finalTotal = $total - $discountAmount;

        return view('pos.checkout', [
            'cart' => $cart,
            'total' => $total,
            'promoCode' => $promoCode,
            'discountAmount' => $discountAmount,
            'finalTotal' => $finalTotal,
        ]);
    }

    public function cancelCart()
    {
        session()->forget('cart');

        return redirect()->route('pos.index')->with('success', 'Cart cleared.');
    }

    public function removeCartItem(int $index)
    {
        $cart = session()->get('cart', []);

        if (! array_key_exists($index, $cart)) {
            return redirect()->back()->with('error', 'Cart item not found.');
        }

        unset($cart[$index]);
        session()->put('cart', array_values($cart));

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty');
        }

        $total = collect($cart)->sum(function ($i) {
            return $i['price'] * $i['quantity'];
        });

        // Handle promo code
        $promoCode = $request->input('promo_code', session()->get('promo_code'));
        $promo = null;
        $discountAmount = 0;

        if ($promoCode) {
            $promo = Promo::where('code', strtoupper($promoCode))->first();
            if ($promo && $promo->isValid()) {
                if (!$promo->min_order_amount || $total >= $promo->min_order_amount) {
                    $discountAmount = $promo->calculateDiscount($total);
                }
            }
        }

        $finalTotal = $total - $discountAmount;

        try {
            $order = DB::transaction(function () use ($cart, $total, $finalTotal, $promo, $discountAmount) {
                foreach ($cart as $item) {
                    $product = Product::whereKey($item['product_id'])->firstOrFail();

                    if ($product->stock < $item['quantity']) {
                        throw new \RuntimeException('Insufficient stock for ' . $product->name);
                    }

                    $product->decrement('stock', (int) $item['quantity']);
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total_amount' => $finalTotal,
                    'discount_amount' => $discountAmount,
                    'promo_id' => $promo?->id,
                    'status' => 'pending',
                ]);

                foreach ($cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'size' => $item['size'] ?? null,
                        'sugar' => $item['sugar'] ?? null,
                        'unit_price' => $item['price'],
                        'line_total' => $item['price'] * $item['quantity'],
                    ]);
                }

                // Increment promo usage
                if ($promo) {
                    $promo->increment('times_used');
                }

                return $order;
            }, 3);
        } catch (\RuntimeException $exception) {
            return redirect()->route('pos.index')->with('error', $exception->getMessage());
        }

        session()->forget('cart');
        session()->forget('promo_code');
        session()->forget('discount_amount');

        return redirect()->route('pos.receipt', ['id' => $order->id]);
    }

    public function receipt($id)
    {
        $order = Order::with('items.product', 'payments', 'promo')->findOrFail($id);
        return view('pos.receipt', compact('order'));
    }

    public function applyPromo(Request $request)
    {
        $code = $request->input('code');
        $cartTotal = $request->input('total');

        $promo = Promo::where('code', strtoupper($code))->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        if (!$promo->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code is expired or no longer valid',
            ], 422);
        }

        if ($promo->min_order_amount && $cartTotal < $promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount is $" . $promo->min_order_amount,
            ], 422);
        }

        $discount = $promo->calculateDiscount($cartTotal);

        session()->put('promo_code', $promo->code);
        session()->put('discount_amount', $discount);

        return response()->json([
            'success' => true,
            'message' => "Discount of $" . $discount . " applied!",
            'discount' => $discount,
            'final_total' => $cartTotal - $discount,
        ]);
    }

    private function isCoffeeProduct(Product $product): bool
    {
        return in_array(strtolower((string) $product->category), ['coffee', 'caffee', 'cafe'], true);
    }

    private function priceForSize(Product $product, string $selectedSize): float
    {
        $column = strtolower($selectedSize) . '_price';
        $price = $product->{$column} ?? $product->price;

        return round(max(0.01, $price), 2);
    }
}
