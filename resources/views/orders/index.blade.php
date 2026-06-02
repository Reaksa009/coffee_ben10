@extends('layouts.app')

@section('content')
    @php
        $statusClass = function ($status) {
            return match ($status) {
                'paid' => 'success',
                'pending' => 'warning',
                'cancelled', 'failed' => 'danger',
                default => 'secondary',
            };
        };
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">Review recent sales, payment status, and order details.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> New Sale
        </a>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Order History</h2>
            <span class="badge text-bg-light">{{ $orders->total() }} total</span>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state">No orders yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="fw-semibold">#{{ $order->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $order->items_count }} items</div>
                                    <div class="small text-muted">
                                        @forelse($order->items->take(3) as $item)
                                            {{ $item->quantity }}x {{ $item->product?->name ?? 'Product #' . $item->product_id }}@if(! $loop->last), @endif
                                        @empty
                                            No item rows
                                        @endforelse
                                        @if($order->items_count > 3)
                                            +{{ $order->items_count - 3 }} more
                                        @endif
                                    </div>
                                </td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $statusClass($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="d-flex justify-content-center mt-4">{{ $orders->links() }}</div>
@endsection
