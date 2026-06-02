@extends('layouts.app')

@section('content')
    @php
        $statusClass = match ($order->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'cancelled', 'failed' => 'danger',
            default => 'secondary',
        };
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Order #{{ $order->id }}</h1>
            <p class="page-subtitle">{{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pos.receipt', ['id' => $order->id]) }}" class="btn btn-outline-primary">
                <i class="bi bi-receipt me-1"></i> Receipt
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Orders
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Items</h2>
                    <span class="badge text-bg-light">{{ $order->items->count() }} items</span>
                </div>
                @if($order->items->isEmpty())
                    <div class="empty-state">No item rows found for this order.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover app-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $item->product?->name ?? 'Product #' . $item->product_id }}
                                            @if($item->size)
                                                <div class="text-muted small">Size: {{ $item->size }}</div>
                                            @endif
                                            @if($item->sugar)
                                                <div class="text-muted small">Sugar: {{ $item->sugar }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-semibold">${{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-4">
            <section class="app-card p-4 mb-4">
                <div class="text-muted small">Order Total</div>
                <div class="display-6 fw-bold mb-3">${{ number_format($order->total_amount, 2) }}</div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Status</span>
                    <span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                </div>
            </section>

            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Payments</h2>
                </div>
                @if($order->payments->isEmpty())
                    <div class="empty-state">No payments recorded.</div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($order->payments as $payment)
                            @php
                                $paymentStatusClass = match ($payment->status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'cancelled', 'failed' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <div class="list-group-item px-4 py-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ strtoupper($payment->provider ?? '-') }}</div>
                                        <div class="small text-muted">{{ $payment->transaction_id ?? 'No transaction ID' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">${{ number_format($payment->amount, 2) }}</div>
                                        <span class="badge text-bg-{{ $paymentStatusClass }}">{{ ucfirst($payment->status) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
