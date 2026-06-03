<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
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
            ->paginate(12)
            ->withQueryString();
        $productsByCategory = $products->getCollection()->groupBy(fn ($product) => $product->category_name ?: 'Uncategorized');

        return view('products.index', compact('products', 'productsByCategory', 'categories', 'selectedCategory'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => $this->categoryIdRules(),
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'coffee_size' => 'nullable|string|max:50',
            'sugar' => 'nullable|string|max:50',
            'price' => 'required|numeric',
            'small_price' => 'nullable|numeric|min:0',
            'medium_price' => 'nullable|numeric|min:0',
            'large_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $this->normalizeProductData($data);
        $data = $this->withUploadedImage($request, $data);

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product created');
    }

    public function edit(Product $product)
    {
        $product->load('category');
        $categories = Category::query()->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function image(Product $product)
    {
        if (! $product->image_data || ! $product->image_mime) {
            abort(404);
        }

        $contents = base64_decode($product->image_data, true);

        if ($contents === false) {
            abort(404);
        }

        return response($contents, 200, [
            'Content-Type' => $product->image_mime,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => $this->categoryIdRules(),
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'coffee_size' => 'nullable|string|max:50',
            'sugar' => 'nullable|string|max:50',
            'price' => 'required|numeric',
            'small_price' => 'nullable|numeric|min:0',
            'medium_price' => 'nullable|numeric|min:0',
            'large_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $this->normalizeProductData($data);

        if ($request->hasFile('image') && $product->image && ! $product->image_data) {
            Storage::disk('public')->delete($product->image);
        }

        $data = $this->withUploadedImage($request, $data);

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        if ($product->image && ! $product->image_data) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product archived successfully');
    }

    private function normalizeProductData(array $data): array
    {
        if (array_key_exists('category_id', $data)) {
            $data['category_id'] = $data['category_id'] === null || $data['category_id'] === ''
                ? null
                : $data['category_id'];

            if ($data['category_id'] !== null) {
                unset($data['category']);
            }
        }

        $data['price'] = (float) $data['price'];
        $data['stock'] = max(0, (int) $data['stock']);

        foreach (['small_price', 'medium_price', 'large_price'] as $field) {
            $data[$field] = isset($data[$field]) && $data[$field] !== ''
                ? (float) $data[$field]
                : null;
        }

        return $data;
    }

    private function categoryIdRules(): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, Closure $fail): void {
                if (($value !== null && $value !== '') && ! Category::whereKey($value)->exists()) {
                    $fail('The selected category is invalid.');
                }
            },
        ];
    }

    private function withUploadedImage(Request $request, array $data): array
    {
        if (! $request->hasFile('image')) {
            return $data;
        }

        $file = $request->file('image');
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw ValidationException::withMessages([
                'image' => 'Unable to read the uploaded image.',
            ]);
        }

        $data['image'] = $file->getClientOriginalName();
        $data['image_name'] = $file->getClientOriginalName();
        $data['image_mime'] = $file->getMimeType() ?: 'image/jpeg';
        $data['image_data'] = base64_encode($contents);

        return $data;
    }
}
