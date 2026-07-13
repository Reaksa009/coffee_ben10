<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\ShopSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\LoyaltyService;
use App\Services\ActivityLogger;
use App\Services\TelegramNotificationService;

class POSController extends Controller
{
    private const SUGAR_OPTIONS = ['0%', '25%', '50%', '75%', '100%'];
    private const SIZE_OPTIONS = ['Small', 'Medium', 'Large'];

    public function index()
    {
        $this->updateCustomerDisplayState();

        $categories = Product::categoryOptions();
        $selectedCategory = request('category');
        $selectedCategoryIds = Category::idsForName($selectedCategory);
        $products = Product::query()
            ->with('category')
            ->when($selectedCategory, function ($query) use ($selectedCategoryIds) {
                return $selectedCategoryIds->isEmpty()
                    ? $query->whereKey('__missing_category__')
                    : $query->whereIn('category_id', $selectedCategoryIds->all());
            })
            ->orderBy('name')
            ->get();
        $productsByCategory = $products->groupBy(fn ($product) => $product->category_name ?: 'Uncategorized');

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

        if ($quantity > (int) $product->stock) {
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

        $this->updateCustomerDisplayState();

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
            'pointValue' => LoyaltyService::POINT_VALUE,
            'pointsPerDollar' => LoyaltyService::POINTS_PER_DOLLAR,
        ]);
    }

    public function lookupCustomer(Request $request, LoyaltyService $loyalty)
    {
        $phone = $loyalty->normalizePhone($request->query('phone'));
        $amount = max(0, (float) $request->query('amount', 0));

        if (! $phone) {
            return response()->json([
                'found' => false,
                'message' => 'Enter a phone number to check loyalty points.',
            ]);
        }

        $customer = $loyalty->findByPhone($phone);

        if (! $customer) {
            return response()->json([
                'found' => false,
                'phone' => $phone,
                'points_balance' => 0,
                'redeemable_discount' => 0,
                'message' => 'New customer. Points will start after payment.',
            ]);
        }

        return response()->json([
            'found' => true,
            'name' => $customer->name,
            'phone' => $customer->phone,
            'points_balance' => (int) $customer->points_balance,
            'redeemable_discount' => $loyalty->redeemableDiscount($customer, $amount),
            'message' => 'Customer found.',
        ]);
    }

    public function cancelCart()
    {
        session()->forget('cart');

        $this->updateCustomerDisplayState();

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

        $this->updateCustomerDisplayState();

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function placeOrder(Request $request, LoyaltyService $loyalty)
    {
        $customerInput = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'redeem_points' => ['nullable', 'boolean'],
            'promo_code' => ['nullable', 'string', 'max:80'],
            'order_type' => ['nullable', 'in:dine_in,takeaway,delivery'],
            'table_number' => ['nullable', 'required_if:order_type,dine_in', 'string', 'max:40'],
            'pickup_name' => ['nullable', 'string', 'max:120'],
        ]);
        $customerInput['order_type'] = $customerInput['order_type'] ?? 'takeaway';

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty');
        }

        $total = collect($cart)->sum(function ($i) {
            return $i['price'] * $i['quantity'];
        });

        // Handle promo code
        $promoCode = $customerInput['promo_code'] ?? session()->get('promo_code');
        $promo = null;
        $promoDiscountAmount = 0;

        if ($promoCode) {
            $promo = Promo::where('code', strtoupper($promoCode))->first();
            if ($promo && $promo->isValid()) {
                if (!$promo->min_order_amount || $total >= $promo->min_order_amount) {
                    $promoDiscountAmount = $promo->calculateDiscount($total);
                }
            }
        }

        $redeemPoints = $request->boolean('redeem_points');

        try {
            $order = DB::transaction(function () use ($cart, $total, $promo, $promoDiscountAmount, $loyalty, $customerInput, $redeemPoints) {
                $customer = $loyalty->findOrCreateCustomer(
                    $customerInput['customer_name'] ?? null,
                    $customerInput['customer_phone'] ?? null
                );
                $redemption = $loyalty->redemptionFor($customer, $total - $promoDiscountAmount, $redeemPoints);

                if ($customer && $redemption['points'] > 0) {
                    $loyalty->redeem($customer, $redemption['points']);
                }

                $discountAmount = round($promoDiscountAmount + $redemption['discount'], 2);
                $finalTotal = round(max(0.01, $total - $discountAmount), 2);
                $orderDate = now()->toDateString();
                $dailyOrderNumber = $this->nextDailyOrderNumber($orderDate);

                foreach ($cart as $item) {
                    $product = Product::with('ingredients.inventoryItem')->whereKey($item['product_id'])->firstOrFail();
                    $quantity = max(1, (int) $item['quantity']);
                    $availableStock = (int) $product->stock;

                    if ($availableStock < $quantity) {
                        throw new \RuntimeException('Insufficient stock for ' . $product->name);
                    }

                    $product->stock = $availableStock - $quantity;
                    $product->save();

                    foreach ($product->ingredients as $ingredient) {
                        $inventoryItem = $ingredient->inventoryItem;

                        if (! $inventoryItem) {
                            continue;
                        }

                        $neededQuantity = (float) $ingredient->quantity * $quantity;

                        if ($inventoryItem->quantity_on_hand < $neededQuantity) {
                            throw new \RuntimeException('Insufficient ingredient stock for ' . $inventoryItem->name);
                        }

                        $inventoryItem->quantity_on_hand = round($inventoryItem->quantity_on_hand - $neededQuantity, 3);
                        $inventoryItem->save();

                        if ($inventoryItem->is_low_stock) {
                            Cache::remember('low_stock_telegram_' . $inventoryItem->id, 86400, function () use ($inventoryItem) {
                                try {
                                    app(TelegramNotificationService::class)->sendLowStockAlert($inventoryItem);
                                } catch (\Throwable $e) {
                                    report($e);
                                }
                                return true;
                            });
                        }
                    }
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name,
                    'customer_phone' => $customer?->phone,
                    'order_date' => $orderDate,
                    'daily_order_number' => $dailyOrderNumber,
                    'subtotal_amount' => $total,
                    'total_amount' => $finalTotal,
                    'discount_amount' => $discountAmount,
                    'promo_discount_amount' => $promoDiscountAmount,
                    'loyalty_discount_amount' => $redemption['discount'],
                    'loyalty_points_redeemed' => $redemption['points'],
                    'promo_id' => $promo?->id,
                    'order_type' => $customerInput['order_type'],
                    'table_number' => $customerInput['order_type'] === 'dine_in' ? $customerInput['table_number'] : null,
                    'pickup_name' => $customerInput['order_type'] !== 'dine_in' ? ($customerInput['pickup_name'] ?? null) : null,
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
                    $promo->times_used = (int) $promo->times_used + 1;
                    $promo->save();
                }

                return $order;
            }, 3);
        } catch (\RuntimeException $exception) {
            return redirect()->route('pos.index')->with('error', $exception->getMessage());
        }

        session()->forget('cart');
        session()->forget('promo_code');
        session()->forget('discount_amount');

        $this->updateCustomerDisplayState($order->id);

        ActivityLogger::log('order.created', 'Created order ' . $order->display_order_label, $order, [
            'total_amount' => $order->total_amount,
            'order_type' => $order->order_type,
        ]);

        return redirect()->route('pos.receipt', ['id' => $order->id]);
    }

    public function receipt($id)
    {
        $order = Order::with('items.product', 'payments', 'promo')->findOrFail($id);
        $settings = ShopSetting::current();

        return view('pos.receipt', compact('order', 'settings'));
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
        return in_array(strtolower((string) $product->category_name), ['coffee', 'caffee', 'cafe'], true);
    }

    private function priceForSize(Product $product, string $selectedSize): float
    {
        $column = strtolower($selectedSize) . '_price';
        $price = $product->{$column} ?? $product->price;

        return round(max(0.01, $price), 2);
    }

    private function nextDailyOrderNumber(string $orderDate): int
    {
        $query = Order::where('order_date', $orderDate);

        if (config('database.default') !== 'mongodb') {
            $query->lockForUpdate();
        }

        return ((int) $query->max('daily_order_number')) + 1;
    }

    private function updateCustomerDisplayState(string|int|null $orderId = null): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discountAmount = session()->get('discount_amount', 0);
        $finalTotal = $total - $discountAmount;

        $state = [
            'cart' => $cart,
            'total' => $total,
            'discount' => $discountAmount,
            'final_total' => $finalTotal,
            'order_id' => $orderId,
            'updated_at' => now()->toDateTimeString(),
        ];

        $user->customer_display_state = json_encode($state);
        $user->save();
    }
}
