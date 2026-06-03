@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">New Purchase</h1>
            <p class="page-subtitle">Record stock purchases and restock ingredient inventory.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Purchases
        </a>
    </div>

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

    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="row g-4">
            <div class="col-lg-4">
                <section class="app-card p-4">
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">No supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id') === (string) $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="Invoice number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Record Purchase
                    </button>
                </section>
            </div>

            <div class="col-lg-8">
                <section class="app-card">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-card-title">Restock Items</h2>
                            <p class="text-muted small mb-0">Each row increases inventory quantity and updates weighted unit cost.</p>
                        </div>
                        <button type="button" id="add-purchase-item" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg me-1"></i> Item
                        </button>
                    </div>
                    @if($inventoryItems->isEmpty())
                        <div class="empty-state">Add inventory items before recording purchases.</div>
                    @else
                        @php($purchaseRows = old('items', [['inventory_item_id' => '', 'quantity' => '', 'unit_cost' => '']]))
                        <div class="p-3 d-grid gap-2" id="purchase-items">
                            @foreach($purchaseRows as $index => $row)
                                <div class="row g-2 align-items-end purchase-item-row">
                                    <div class="col-md-5">
                                        <label class="form-label small">Inventory Item</label>
                                        <select name="items[{{ $index }}][inventory_item_id]" class="form-select">
                                            <option value="">Select item</option>
                                            @foreach($inventoryItems as $item)
                                                <option value="{{ $item->id }}" @selected((string) ($row['inventory_item_id'] ?? '') === (string) $item->id)>
                                                    {{ $item->name }} ({{ $item->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Quantity</label>
                                        <input type="number" step="0.001" min="0" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $row['quantity'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Unit Cost</label>
                                        <input type="number" step="0.0001" min="0" name="items[{{ $index }}][unit_cost]" class="form-control" value="{{ $row['unit_cost'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-purchase-item" title="Remove item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </form>

    <template id="purchase-item-template">
        <div class="row g-2 align-items-end purchase-item-row">
            <div class="col-md-5">
                <label class="form-label small">Inventory Item</label>
                <select class="form-select" data-name="inventory_item_id">
                    <option value="">Select item</option>
                    @foreach($inventoryItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Quantity</label>
                <input type="number" step="0.001" min="0" class="form-control" data-name="quantity">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Unit Cost</label>
                <input type="number" step="0.0001" min="0" class="form-control" data-name="unit_cost">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100 remove-purchase-item" title="Remove item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.getElementById('purchase-items');
            const addButton = document.getElementById('add-purchase-item');
            const template = document.getElementById('purchase-item-template');

            if (!rows || !addButton || !template) {
                return;
            }

            function indexRows() {
                rows.querySelectorAll('.purchase-item-row').forEach(function (row, index) {
                    row.querySelectorAll('[data-name]').forEach(function (field) {
                        field.name = 'items[' + index + '][' + field.dataset.name + ']';
                    });
                });
            }

            addButton.addEventListener('click', function () {
                rows.appendChild(template.content.cloneNode(true));
                indexRows();
            });

            rows.addEventListener('click', function (event) {
                const button = event.target.closest('.remove-purchase-item');
                if (!button) {
                    return;
                }

                const row = button.closest('.purchase-item-row');
                if (row && rows.querySelectorAll('.purchase-item-row').length > 1) {
                    row.remove();
                    indexRows();
                }
            });

            indexRows();
        });
    </script>
@endsection
