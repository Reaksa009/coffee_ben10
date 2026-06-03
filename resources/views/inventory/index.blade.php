@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Ingredient Inventory</h1>
            <p class="page-subtitle">Track coffee beans, milk, cups, lids, sugar, syrup, and other shop supplies.</p>
        </div>
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

    @if($lowStockItems->isNotEmpty())
        <section class="app-card mb-4">
            <div class="app-card-header">
                <div>
                    <h2 class="app-card-title">Low-stock Alerts</h2>
                    <p class="text-muted small mb-0">Restock these items before the counter runs short.</p>
                </div>
                <span class="badge text-bg-warning">{{ $lowStockItems->count() }} alert{{ $lowStockItems->count() === 1 ? '' : 's' }}</span>
            </div>
            <div class="p-3 d-flex flex-wrap gap-2">
                @foreach($lowStockItems as $item)
                    <span class="badge text-bg-warning border">
                        {{ $item->name }}: {{ number_format($item->quantity_on_hand, 3) }} {{ $item->unit }}
                    </span>
                @endforeach
            </div>
        </section>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="app-card p-4">
                <h2 class="h5 fw-bold mb-3">Add Inventory Item</h2>
                <form method="POST" action="{{ route('inventory.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Coffee beans" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', 'g') }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Unit Cost</label>
                            <input type="number" step="0.0001" min="0" name="unit_cost" class="form-control" value="{{ old('unit_cost', 0) }}" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Current Qty</label>
                            <input type="number" step="0.001" min="0" name="quantity_on_hand" class="form-control" value="{{ old('quantity_on_hand', 0) }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Low At</label>
                            <input type="number" step="0.001" min="0" name="low_stock_quantity" class="form-control" value="{{ old('low_stock_quantity', 0) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Save Item
                    </button>
                </form>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <div>
                        <h2 class="app-card-title">Stock List</h2>
                        <p class="text-muted small mb-0">Recipe usage is counted from product recipes.</p>
                    </div>
                </div>
                @if($items->isEmpty())
                    <div class="empty-state">No inventory items yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover app-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Low At</th>
                                    <th>Cost</th>
                                    <th>Recipes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <form id="inventory-update-{{ $item->id }}" method="POST" action="{{ route('inventory.update', $item) }}">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <input form="inventory-update-{{ $item->id }}" type="text" name="name" class="form-control form-control-sm" value="{{ $item->name }}" required>
                                            <input form="inventory-update-{{ $item->id }}" type="text" name="notes" class="form-control form-control-sm mt-1" value="{{ $item->notes }}" placeholder="Notes">
                                        </td>
                                        <td style="min-width: 130px;">
                                            <div class="input-group input-group-sm">
                                                <input form="inventory-update-{{ $item->id }}" type="number" step="0.001" min="0" name="quantity_on_hand" class="form-control" value="{{ $item->quantity_on_hand }}" required>
                                                <input form="inventory-update-{{ $item->id }}" type="text" name="unit" class="form-control" value="{{ $item->unit }}" required>
                                            </div>
                                            @if($item->is_low_stock)
                                                <span class="badge text-bg-warning mt-1">Low stock</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 110px;">
                                            <input form="inventory-update-{{ $item->id }}" type="number" step="0.001" min="0" name="low_stock_quantity" class="form-control form-control-sm" value="{{ $item->low_stock_quantity }}" required>
                                        </td>
                                        <td style="min-width: 120px;">
                                            <input form="inventory-update-{{ $item->id }}" type="number" step="0.0001" min="0" name="unit_cost" class="form-control form-control-sm" value="{{ $item->unit_cost }}" required>
                                        </td>
                                        <td>{{ $item->product_ingredients_count }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button form="inventory-update-{{ $item->id }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-save"></i>
                                                </button>
                                                @if(auth()->user()->canDeleteBackOfficeRecords())
                                                    <form method="POST" action="{{ route('inventory.destroy', $item) }}" class="confirm-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pb-3">{{ $items->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
