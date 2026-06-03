@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Purchase #{{ $purchase->id }}</h1>
            <p class="page-subtitle">{{ $purchase->purchase_date?->format('M d, Y') }} - {{ $purchase->supplier?->name ?? 'No supplier' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Purchase
            </a>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Purchases
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Restocked Items</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover app-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->inventoryItem?->name ?? 'Inventory #' . $item->inventory_item_id }}</td>
                                    <td>{{ number_format($item->quantity, 3) }} {{ $item->inventoryItem?->unit }}</td>
                                    <td>${{ number_format($item->unit_cost, 4) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="app-card p-4">
                <div class="text-muted small">Purchase Total</div>
                <div class="display-6 fw-bold mb-3">${{ number_format($purchase->total_amount, 2) }}</div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Supplier</span>
                    <strong>{{ $purchase->supplier?->name ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Reference</span>
                    <strong>{{ $purchase->reference ?: '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Created by</span>
                    <strong>{{ $purchase->user?->name ?? '-' }}</strong>
                </div>
                @if($purchase->notes)
                    <hr>
                    <div class="text-muted small">Notes</div>
                    <div>{{ $purchase->notes }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
