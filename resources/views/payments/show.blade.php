@extends('layouts.app')

@section('content')
    @php
        $statusClass = match ($payment->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'cancelled', 'failed' => 'danger',
            default => 'secondary',
        };

        $verificationClass = match ($payment->verification_status) {
            'verified' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Payment #{{ $payment->id }}</h1>
            <p class="page-subtitle">{{ $payment->created_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($payment->order)
                <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-outline-primary">
                    <i class="bi bi-receipt me-1"></i> Order #{{ $payment->order->id }}
                </a>
            @endif
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Payments
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="app-card p-4 mb-4">
                <div class="text-muted small">Amount</div>
                <div class="display-6 fw-bold mb-3">${{ number_format($payment->amount, 2) }}</div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Status</span>
                    <span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Verification</span>
                    <span class="badge text-bg-{{ $verificationClass }}">{{ ucfirst($payment->verification_status ?? 'pending') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Method</span>
                    <span class="fw-semibold">{{ strtoupper($payment->payment_method ?? $payment->provider ?? '-') }}</span>
                </div>
            </section>

            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Transaction</h2>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-4 py-3">
                        <div class="small text-muted">Provider</div>
                        <div class="fw-semibold">{{ strtoupper($payment->provider ?? '-') }}</div>
                    </div>
                    <div class="list-group-item px-4 py-3">
                        <div class="small text-muted">Transaction ID</div>
                        <div class="fw-semibold text-break">{{ $payment->transaction_id ?? 'Not recorded' }}</div>
                    </div>
                    <div class="list-group-item px-4 py-3">
                        <div class="small text-muted">Verified At</div>
                        <div class="fw-semibold">{{ $payment->verified_at?->format('M d, Y H:i') ?? 'Not verified' }}</div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="app-card mb-4">
                <div class="app-card-header">
                    <h2 class="app-card-title">Order Snapshot</h2>
                </div>
                @if(! $payment->order)
                    <div class="empty-state">The related order was not found.</div>
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
                                @foreach($payment->order->items as $item)
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

            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Gateway Metadata</h2>
                </div>
                @if(empty($payment->meta))
                    <div class="empty-state">No metadata recorded.</div>
                @else
                    <pre class="m-0 p-4 bg-light small text-break" style="white-space: pre-wrap;">{{ json_encode($payment->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @endif
            </section>

            @if($payment->verification_error)
                <section class="app-card mt-4 p-4">
                    <h2 class="app-card-title text-danger mb-2">Verification Error</h2>
                    <p class="mb-0 text-break">{{ $payment->verification_error }}</p>
                </section>
            @endif
        </div>
    </div>
@endsection
