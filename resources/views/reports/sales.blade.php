@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Sales Report</h1>
            <p class="page-subtitle">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="GET" action="{{ route('reports.sales') }}" class="app-card mb-4">
        <div style="padding: 1.5rem;">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label small">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="period" class="form-label small">Group By</label>
                    <select id="period" name="period" class="form-select">
                        <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="app-card" style="text-align: center; padding: 1.5rem;">
                <div class="text-muted small mb-2">Total Orders</div>
                <div style="font-size: 2rem; font-weight: 800;">{{ $summary['total_orders'] }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="app-card" style="text-align: center; padding: 1.5rem;">
                <div class="text-muted small mb-2">Total Revenue</div>
                <div style="font-size: 2rem; font-weight: 800; color: #059669;">
                    ${{ number_format($summary['total_revenue'], 2) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="app-card" style="text-align: center; padding: 1.5rem;">
                <div class="text-muted small mb-2">Total Discounts</div>
                <div style="font-size: 2rem; font-weight: 800;">${{ number_format($summary['total_discounts'], 2) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="app-card" style="text-align: center; padding: 1.5rem;">
                <div class="text-muted small mb-2">Average Order</div>
                <div style="font-size: 2rem; font-weight: 800;">${{ number_format($summary['average_order'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h3 class="app-card-title">Order Details</h3>
            <a href="{{ route('reports.export', ['type' => 'sales', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                class="btn btn-sm btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state">No orders found for the selected period</div>
        @else
            <table class="table app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Discount</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->created_at->format('M d, H:i') }}</td>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>
                                @foreach($order->items as $item)
                                    <div class="small">{{ $item->quantity }}x {{ $item->product->name }}</div>
                                @endforeach
                            </td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->discount_amount > 0)
                                    <span class="badge text-bg-info">${{ number_format($order->discount_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge text-bg-light">{{ ucfirst($order->payment_method ?? 'KHQR') }}</span>
                            </td>
                            <td>
                                <span class="badge text-bg-success">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection