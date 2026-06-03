@extends('layouts.app')

@section('content')
    <style>
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 1.35rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            box-shadow: var(--shadow);
        }

        .dashboard-title {
            font-size: clamp(1.45rem, 2vw, 1.9rem);
            font-weight: 800;
            margin: 0;
        }

        .dashboard-kicker {
            color: var(--accent);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            margin-bottom: .35rem;
            text-transform: uppercase;
        }

        .dashboard-subtitle {
            color: #6c757d;
            margin: .25rem 0 0;
        }

        .metric-card {
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            padding: 1.1rem;
            height: 100%;
            box-shadow: var(--shadow);
        }

        .metric-icon {
            width: 44px;
            height: 44px;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .metric-label {
            color: #6c757d;
            font-size: .86rem;
            margin-bottom: .25rem;
        }

        .metric-value {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1.15;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--line);
        }

        .panel-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .dashboard-table {
            margin: 0;
        }

        .dashboard-table th {
            color: #6c757d;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            background: #f8f9fa;
            border-bottom: 1px solid #eef1f4;
        }

        .dashboard-table td {
            vertical-align: middle;
        }

        .empty-state {
            padding: 2rem 1rem;
            text-align: center;
            color: #6c757d;
        }

        .dashboard-list {
            display: grid;
            gap: .75rem;
            padding: 1rem;
        }

        .dashboard-list-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .75rem;
            align-items: center;
            padding: .8rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
        }

        .dashboard-item-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            color: var(--brand);
            background: rgba(20, 184, 166, .12);
        }

        .dashboard-side-list {
            display: grid;
            gap: .65rem;
            padding: 1rem;
        }

        .dashboard-side-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .75rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
        }

        .bar-chart {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: .85rem;
            min-height: 260px;
            padding: 1rem 1rem 1.2rem;
        }

        .bar-chart-item {
            display: grid;
            grid-template-rows: 1fr auto;
            gap: .7rem;
            min-width: 0;
        }

        .bar-track {
            display: flex;
            align-items: end;
            min-height: 190px;
            border-radius: .5rem;
            background: #eef2f7;
            overflow: hidden;
        }

        .bar-fill {
            width: 100%;
            min-height: 8px;
            border-radius: .5rem .5rem 0 0;
            background: linear-gradient(180deg, var(--brand), #2563eb);
            transition: height .2s ease;
        }

        .bar-caption {
            min-width: 0;
            text-align: center;
        }

        .bar-label {
            font-weight: 800;
            line-height: 1.1;
        }

        .bar-date,
        .bar-value {
            color: #6c757d;
            font-size: .78rem;
            line-height: 1.25;
        }

        @media (max-width: 767.98px) {
            .dashboard-header {
                align-items: stretch;
                flex-direction: column;
            }

            .dashboard-list-item {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .dashboard-list-item > .text-end {
                grid-column: 1 / -1;
                text-align: left !important;
            }

            .bar-chart {
                gap: .45rem;
                padding-inline: .75rem;
            }

            .bar-track {
                min-height: 150px;
            }

            .bar-value {
                display: none;
            }
        }
    </style>

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

    <div class="dashboard-header">
        <div>
            <div class="dashboard-kicker">Command Center</div>
            <h1 class="dashboard-title">Good day, {{ auth()->user()->name ?? 'Cashier' }}.</h1>
            <p class="dashboard-subtitle">Track orders, payments, and stock without leaving the counter.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pos.index') }}" class="btn btn-primary">
                <i class="bi bi-cup-hot me-1"></i> New Sale
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-box-seam me-1"></i> Products
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="metric-icon text-primary bg-primary bg-opacity-10">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <span class="badge text-bg-light">All time</span>
                </div>
                <div class="metric-label">Total Orders</div>
                <div class="metric-value">{{ number_format($totalOrders) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="metric-icon text-success bg-success bg-opacity-10">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <span class="badge text-bg-success">Paid</span>
                </div>
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="metric-icon text-warning bg-warning bg-opacity-10">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <span class="badge text-bg-warning">Open</span>
                </div>
                <div class="metric-label">Pending Orders</div>
                <div class="metric-value">{{ number_format($pendingOrders) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="metric-icon text-danger bg-danger bg-opacity-10">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <span class="badge text-bg-light">{{ number_format($totalProducts) }} products</span>
                </div>
                <div class="metric-label">Low Stock Items</div>
                <div class="metric-value">{{ number_format($lowStock->count()) }}</div>
            </div>
        </div>
    </div>

    <section class="panel mb-4">
        <div class="panel-header">
            <div>
                <h2 class="panel-title">Sales This Week</h2>
                <div class="text-muted small">Paid order revenue for the last 7 days</div>
            </div>
            <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary">Sales Report</a>
        </div>
        <div class="bar-chart" role="img" aria-label="Seven day sales bar chart">
            @foreach($salesChart as $point)
                @php
                    $height = $point['revenue'] > 0 ? max(8, ($point['revenue'] / $maxChartRevenue) * 100) : 0;
                @endphp
                <div class="bar-chart-item" title="{{ $point['date'] }}: ${{ number_format($point['revenue'], 2) }} from {{ $point['orders'] }} order(s)">
                    <div class="bar-track">
                        <div class="bar-fill" style="height: {{ $height }}%;"></div>
                    </div>
                    <div class="bar-caption">
                        <div class="bar-label">{{ $point['label'] }}</div>
                        <div class="bar-date">{{ $point['date'] }}</div>
                        <div class="bar-value">${{ number_format($point['revenue'], 0) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="row g-4">
        <div class="col-xl-7">
            <section class="panel h-100">
                <div class="panel-header">
                    <h2 class="panel-title">Recent Orders</h2>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="empty-state">No orders yet.</div>
                @else
                    <div class="dashboard-list">
                        @foreach($recentOrders as $order)
                            <div class="dashboard-list-item">
                                <div class="dashboard-item-icon">
                                    <i class="bi bi-receipt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">Order {{ $order->display_order_label }}</div>
                                    <div class="text-muted small">{{ $order->created_at->format('M d, H:i') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">${{ number_format($order->total_amount, 2) }}</div>
                                    <div class="d-flex gap-2 justify-content-end mt-1">
                                        <span class="badge text-bg-{{ $statusClass($order->status) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <a href="{{ route('pos.receipt', ['id' => $order->id]) }}" class="btn btn-sm btn-light" title="View receipt">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="col-xl-5">
            <section class="panel mb-4">
                <div class="panel-header">
                    <h2 class="panel-title">Low Stock Products</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
                </div>

                @if($lowStock->isEmpty())
                    <div class="empty-state">Stock levels look good.</div>
                @else
                    <div class="dashboard-side-list">
                        @foreach($lowStock as $product)
                            <div class="dashboard-side-item">
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="text-muted small">{{ $product->category_name ?? 'Menu item' }}</div>
                                </div>
                                <span class="badge text-bg-{{ $product->stock <= 2 ? 'danger' : 'warning' }}">
                                    {{ $product->stock }} left
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Recent Payments</h2>
                </div>

                @if($recentPayments->isEmpty())
                    <div class="empty-state">No payments recorded.</div>
                @else
                    <div class="dashboard-side-list">
                        @foreach($recentPayments as $payment)
                            <div class="dashboard-side-item">
                                <div>
                                    <a href="{{ route('pos.receipt', ['id' => $payment->order_id]) }}" class="fw-semibold text-decoration-none">
                                        Order {{ $payment->order?->display_order_label ?? '#' . $payment->order_id }}
                                    </a>
                                    <div class="text-muted small">{{ strtoupper($payment->provider ?? '-') }} - ${{ number_format($payment->amount, 2) }}</div>
                                </div>
                                <span class="badge text-bg-{{ $statusClass($payment->status) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
