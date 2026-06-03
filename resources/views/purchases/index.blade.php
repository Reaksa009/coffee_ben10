@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Purchase Info</h1>
            <p class="page-subtitle">Purchase history and inventory restocks.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Purchase
        </a>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Purchase Records</h2>
            <span class="badge text-bg-light border">{{ $purchases->total() }} records</span>
        </div>
        @if($purchases->isEmpty())
            <div class="empty-state">No purchases recorded yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>Created By</th>
                            <th class="text-end">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->purchase_date?->format('M d, Y') }}</td>
                                <td class="fw-semibold">{{ $purchase->supplier?->name ?? 'No supplier' }}</td>
                                <td>{{ $purchase->reference ?: '-' }}</td>
                                <td>{{ $purchase->user?->name ?? '-' }}</td>
                                <td class="text-end fw-bold">${{ number_format($purchase->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 pb-3">{{ $purchases->links() }}</div>
        @endif
    </section>
@endsection
