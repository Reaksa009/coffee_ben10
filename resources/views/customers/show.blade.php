@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">{{ $customer->name ?: 'Walk-in Customer' }}</h1>
            <p class="page-subtitle">{{ $customer->phone }}</p>
        </div>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Customers
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="app-card p-4 mb-4">
                <div class="text-muted small">Current Balance</div>
                <div class="display-5 fw-bold text-primary mb-3">{{ number_format($customer->points_balance) }} pts</div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Earned</span>
                    <strong>{{ number_format($customer->total_points_earned) }} pts</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Redeemed</span>
                    <strong>{{ number_format($customer->total_points_redeemed) }} pts</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Visits</span>
                    <strong>{{ number_format($customer->visits) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Total Spend</span>
                    <strong>${{ number_format($customer->total_spent, 2) }}</strong>
                </div>
            </section>

            <section class="app-card p-4">
                <h2 class="app-card-title mb-3">How Points Work</h2>
                <div class="small text-muted">
                    Customers earn 10 points per $1 paid. Each point can redeem $0.01 on a future checkout.
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Order History</h2>
                    <span class="badge text-bg-light">{{ $orders->total() }} orders</span>
                </div>

                @if($orders->isEmpty())
                    <div class="empty-state">No paid orders recorded for this customer yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover app-table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Total</th>
                                    <th>Loyalty</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    @php
                                        $statusClass = match ($order->status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'cancelled', 'failed' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $order->display_order_label }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if($order->loyalty_points_redeemed)
                                                <div class="small text-success">Redeemed {{ $order->loyalty_points_redeemed }} pts</div>
                                            @endif
                                            @if($order->loyalty_points_earned)
                                                <div class="small text-primary">Earned {{ $order->loyalty_points_earned }} pts</div>
                                            @endif
                                            @if(! $order->loyalty_points_redeemed && ! $order->loyalty_points_earned)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($order->status) }}</span></td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('pos.receipt', ['id' => $order->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt me-1"></i> Receipt
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
        </div>
    </div>
@endsection
