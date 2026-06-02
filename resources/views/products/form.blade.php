@csrf

@if($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Please fix the highlighted fields.</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="app-card p-4">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-control"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Category</label>
                @php($selectedCategory = old('category', $product->category ?? ''))
                <select name="category" class="form-select">
                    <option value="">Select category</option>
                    <option value="Coffee" @selected($selectedCategory === 'Coffee')>Coffee</option>
                    <option value="Tea" @selected($selectedCategory === 'Tea')>Tea</option>
                    <option value="Frappe" @selected($selectedCategory === 'Frappe')>Frappe</option>
                    <option value="Smoothie" @selected($selectedCategory === 'Smoothie')>Smoothie</option>
                    <option value="Bakery" @selected($selectedCategory === 'Bakery')>Bakery</option>
                    <option value="Food" @selected($selectedCategory === 'Food')>Food</option>
                    <option value="Other" @selected($selectedCategory === 'Other')>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="5"
                    class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Size</label>
                    <select name="coffee_size" class="form-select">
                        @php($selectedSize = old('coffee_size', $product->coffee_size ?? ''))
                        <option value="">Select size</option>
                        <option value="Small" @selected($selectedSize === 'Small')>Small</option>
                        <option value="Medium" @selected($selectedSize === 'Medium')>Medium</option>
                        <option value="Large" @selected($selectedSize === 'Large')>Large</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sugar</label>
                    <select name="sugar" class="form-select">
                        @php($selectedSugar = old('sugar', $product->sugar ?? ''))
                        <option value="">Select sugar</option>
                        <option value="No sugar" @selected($selectedSugar === 'No sugar')>No sugar</option>
                        <option value="25%" @selected($selectedSugar === '25%')>25%</option>
                        <option value="50%" @selected($selectedSugar === '50%')>50%</option>
                        <option value="75%" @selected($selectedSugar === '75%')>75%</option>
                        <option value="100%" @selected($selectedSugar === '100%')>100%</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Base Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}"
                            class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
                        class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Small Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="small_price"
                            value="{{ old('small_price', $product->small_price ?? '') }}" class="form-control"
                            placeholder="Use base">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Medium Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="medium_price"
                            value="{{ old('medium_price', $product->medium_price ?? '') }}" class="form-control"
                            placeholder="Use base">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Large Price</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="large_price"
                            value="{{ old('large_price', $product->large_price ?? '') }}" class="form-control"
                            placeholder="Use base">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="app-card p-4">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">

            <div class="mt-3">
                @if(!empty($product->image))
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded border"
                        alt="{{ $product->name }}">
                @else
                    <div class="border rounded bg-light text-muted d-flex align-items-center justify-content-center"
                        style="aspect-ratio:4/3;">
                        <i class="bi bi-image fs-1"></i>
                    </div>
                @endif
            </div>

            <button class="btn btn-primary w-100 mt-4">
                <i class="bi bi-save me-1"></i> Save Product
            </button>
        </div>
    </div>
</div>