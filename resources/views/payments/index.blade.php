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

        $verificationClass = function ($status) {
            return match ($status) {
                'verified' => 'success',
                'failed' => 'danger',
                'pending' => 'warning',
                default => 'secondary',
            };
        };
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Payments</h1>
            <p class="page-subtitle">Track KHQR, cash, card, and wallet transactions.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> New Sale
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <section class="app-card p-3">
                <div class="small text-muted">All Payments</div>
                <div class="h4 fw-bold mb-0">${{ number_format($summary['total'], 2) }}</div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="app-card p-3">
                <div class="small text-muted">Paid Revenue</div>
                <div class="h4 fw-bold mb-0 text-success">${{ number_format($summary['paid'], 2) }}</div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="app-card p-3">
                <div class="small text-muted">Pending Payments</div>
                <div class="h4 fw-bold mb-0 text-warning">{{ $summary['pending'] }}</div>
            </section>
        </div>
        <div class="col-md-3">
            <section class="app-card p-3">
                <div class="small text-muted">Failed Verifications</div>
                <div class="h4 fw-bold mb-0 text-danger">{{ $summary['failed_verifications'] }}</div>
            </section>
        </div>
    </div>

    <section class="app-card mb-4">
        <form class="app-card-header align-items-end" method="GET" action="{{ route('payments.index') }}">
            <div>
                <h2 class="app-card-title">Filters</h2>
                <div class="small text-muted">Narrow down payment records.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <select name="status" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All statuses</option>
                    @foreach(['pending', 'paid', 'failed', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="method" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">All methods</option>
                    @foreach($methods as $method)
                        <option value="{{ $method }}" @selected(request('method') === $method)>{{ strtoupper($method) }}</option>
                    @endforeach
                </select>
                <select name="verification" class="form-select form-select-sm" style="width: 170px;">
                    <option value="">All verification</option>
                    @foreach(['pending', 'verified', 'failed'] as $verification)
                        <option value="{{ $verification }}" @selected(request('verification') === $verification)>{{ ucfirst($verification) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-primary" type="submit">
                    <i class="bi bi-funnel me-1"></i> Apply
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Payment History</h2>
            <span class="badge text-bg-light">{{ $payments->total() }} total</span>
        </div>

        @if($payments->isEmpty())
            <div class="empty-state">No payments found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Order</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Verification</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <div class="fw-semibold">#{{ $payment->id }}</div>
                                    <div class="small text-muted">{{ $payment->transaction_id ?? 'No transaction ID' }}</div>
                                </td>
                                <td>
                                    @if($payment->order)
                                        <a href="{{ route('orders.show', $payment->order) }}" class="fw-semibold text-decoration-none">
                                            #{{ $payment->order->id }}
                                        </a>
                                    @else
                                        <span class="text-muted">Missing order</span>
                                    @endif
                                </td>
                                <td>{{ strtoupper($payment->payment_method ?? $payment->provider ?? '-') }}</td>
                                <td class="fw-semibold">${{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $statusClass($payment->status) }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge text-bg-{{ $verificationClass($payment->verification_status) }}">
                                        {{ ucfirst($payment->verification_status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-primary">
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

    <div class="d-flex justify-content-center mt-4">{{ $payments->links() }}</div>
@endsection
