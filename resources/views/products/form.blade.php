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
                @php($selectedCategoryId = old('category_id', $product->category_id ?? ''))
                <select name="category_id" class="form-select">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
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

            <?php
                $recipeRows = old('ingredients');

                if ($recipeRows === null) {
                    $recipeRows = [];

                    if (isset($product)) {
                        foreach ($product->ingredients as $ingredient) {
                            $recipeRows[] = [
                                'inventory_item_id' => $ingredient->inventory_item_id,
                                'quantity' => $ingredient->quantity,
                                'unit' => $ingredient->unit,
                            ];
                        }
                    }
                }

                if (count($recipeRows) === 0) {
                    $recipeRows = [['inventory_item_id' => '', 'quantity' => '', 'unit' => '']];
                }
            ?>

            <div class="border-top pt-4 mt-2">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h5 fw-bold mb-1">Recipe / Product Costing</h2>
                        <p class="text-muted small mb-0">Add ingredients used for one drink so cost and profit margin can be calculated.</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-ingredient-row">
                        <i class="bi bi-plus-lg me-1"></i> Ingredient
                    </button>
                </div>

                @if($inventoryItems->isEmpty())
                    <div class="alert alert-warning small">
                        Add inventory items first to build a recipe.
                    </div>
                @endif

                <div id="ingredient-rows" class="d-grid gap-2">
                    @foreach($recipeRows as $index => $row)
                        <div class="row g-2 align-items-end ingredient-row">
                            <div class="col-md-5">
                                <label class="form-label small">Ingredient</label>
                                <select name="ingredients[{{ $index }}][inventory_item_id]" class="form-select ingredient-select">
                                    <option value="">Select ingredient</option>
                                    @foreach($inventoryItems as $item)
                                        <option value="{{ $item->id }}" data-unit="{{ $item->unit }}"
                                            @selected((string) ($row['inventory_item_id'] ?? '') === (string) $item->id)>
                                            {{ $item->name }} - ${{ number_format($item->unit_cost, 4) }}/{{ $item->unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Quantity</label>
                                <input type="number" step="0.001" min="0" name="ingredients[{{ $index }}][quantity]"
                                    class="form-control" value="{{ $row['quantity'] ?? '' }}" placeholder="18">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Unit</label>
                                <input type="text" name="ingredients[{{ $index }}][unit]" class="form-control ingredient-unit"
                                    value="{{ $row['unit'] ?? '' }}" placeholder="g">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 remove-ingredient-row" title="Remove ingredient">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="app-card p-4">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">

            <div class="mt-3">
                @if(!empty($product) && !empty($product->image_url))
                    <img src="{{ $product->image_url }}" class="img-fluid rounded border"
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

<template id="ingredient-row-template">
    <div class="row g-2 align-items-end ingredient-row">
        <div class="col-md-5">
            <label class="form-label small">Ingredient</label>
            <select class="form-select ingredient-select" data-name="inventory_item_id">
                <option value="">Select ingredient</option>
                @foreach($inventoryItems as $item)
                    <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">
                        {{ $item->name }} - ${{ number_format($item->unit_cost, 4) }}/{{ $item->unit }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small">Quantity</label>
            <input type="number" step="0.001" min="0" class="form-control" data-name="quantity" placeholder="18">
        </div>
        <div class="col-md-3">
            <label class="form-label small">Unit</label>
            <input type="text" class="form-control ingredient-unit" data-name="unit" placeholder="g">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger w-100 remove-ingredient-row" title="Remove ingredient">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.getElementById('ingredient-rows');
        const addButton = document.getElementById('add-ingredient-row');
        const template = document.getElementById('ingredient-row-template');

        if (!rows || !addButton || !template) {
            return;
        }

        function indexRows() {
            rows.querySelectorAll('.ingredient-row').forEach(function (row, index) {
                row.querySelectorAll('[data-name]').forEach(function (field) {
                    field.name = 'ingredients[' + index + '][' + field.dataset.name + ']';
                });
            });
        }

        function fillUnit(select) {
            const option = select.options[select.selectedIndex];
            const unit = option ? option.dataset.unit : '';
            const row = select.closest('.ingredient-row');
            const unitInput = row ? row.querySelector('.ingredient-unit') : null;

            if (unitInput && !unitInput.value) {
                unitInput.value = unit || '';
            }
        }

        addButton.addEventListener('click', function () {
            rows.appendChild(template.content.cloneNode(true));
            indexRows();
        });

        rows.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.remove-ingredient-row');

            if (!removeButton) {
                return;
            }

            const row = removeButton.closest('.ingredient-row');
            if (row && rows.querySelectorAll('.ingredient-row').length > 1) {
                row.remove();
                indexRows();
            }
        });

        rows.addEventListener('change', function (event) {
            if (event.target.matches('.ingredient-select')) {
                fillUnit(event.target);
            }
        });

        indexRows();
    });
</script>
