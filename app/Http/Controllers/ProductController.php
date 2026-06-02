<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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
            ->paginate(12)
            ->withQueryString();
        $productsByCategory = $products->getCollection()->groupBy(fn ($product) => $product->category ?: 'Uncategorized');

        return view('products.index', compact('products', 'productsByCategory', 'categories', 'selectedCategory'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'coffee_size' => 'nullable|string|max:50',
            'sugar' => 'nullable|string|max:50',
            'price' => 'required|numeric',
            'small_price' => 'nullable|numeric|min:0',
            'medium_price' => 'nullable|numeric|min:0',
            'large_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product created');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'coffee_size' => 'nullable|string|max:50',
            'sugar' => 'nullable|string|max:50',
            'price' => 'required|numeric',
            'small_price' => 'nullable|numeric|min:0',
            'medium_price' => 'nullable|numeric|min:0',
            'large_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // delete old
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    public function destroy(Product $product)
    {
        $products = Product::findOrFail($product->id);
        if ($products->image) {
            Storage::disk('public')->delete($product->image);
        }

        $products->delete();
        return redirect()->route('products.index')->with('success', 'Product archived successfully');
    }
}
