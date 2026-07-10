@extends('layouts.app')

@section('content')
    <style>
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.04);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--brand), var(--accent));
        }

        .dashboard-title {
            font-size: clamp(1.6rem, 2.5vw, 2.1rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin: 0;
        }

        .dashboard-kicker {
            color: var(--accent);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: .08em;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
        }

        .dashboard-subtitle {
            color: #475569;
            font-size: 0.95rem;
            margin: .35rem 0 0;
        }

        .metric-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
            background: #ffffff;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.02), 0 8px 16px -6px rgba(15, 23, 42, 0.01);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.08);
            border-color: rgba(15, 118, 110, 0.2);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .metric-card:hover .metric-icon {
            transform: scale(1.1);
        }

        .metric-label {
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .panel {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.02), 0 8px 16px -6px rgba(15, 23, 42, 0.01);
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            background-color: #f8fafc;
        }

        .panel-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .dashboard-list {
            display: grid;
            gap: 0.85rem;
            padding: 1.25rem;
        }

        .dashboard-list-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border: 1px solid #f1f5f9;
            border-radius: 0.75rem;
            background: #ffffff;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }

        .dashboard-list-item:hover {
            transform: translateX(6px);
            background: #f8fafc;
            border-left-color: var(--brand);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
        }

        .dashboard-item-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            color: var(--brand);
            background: rgba(15, 118, 110, 0.08);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .dashboard-list-item:hover .dashboard-item-icon {
            background: var(--brand);
            color: #ffffff;
        }

        .dashboard-side-list {
            display: grid;
            gap: 0.75rem;
            padding: 1.25rem;
        }

        .dashboard-side-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border: 1px solid #f1f5f9;
            border-radius: 0.75rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid transparent;
        }

        .dashboard-side-item:hover {
            transform: translateX(6px);
            background: #f8fafc;
            border-left-color: var(--accent);
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.03);
        }

        .bar-chart {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 1.25rem;
            min-height: 280px;
            padding: 1.5rem 1.5rem 1.75rem;
        }

        .bar-chart-item {
            display: grid;
            grid-template-rows: 1fr auto;
            gap: 0.85rem;
            min-width: 0;
            transition: transform 0.2s ease;
        }
        
        .bar-chart-item:hover {
            transform: scale(1.03);
        }

        .bar-track {
            display: flex;
            align-items: end;
            min-height: 200px;
            border-radius: 0.75rem;
            background: #f1f5f9;
            border: 1px solid rgba(226, 232, 240, 0.5);
            overflow: hidden;
            position: relative;
        }

        .bar-fill {
            width: 100%;
            min-height: 8px;
            border-radius: 0.75rem 0.75rem 0 0;
            background: linear-gradient(180deg, var(--brand), var(--brand-dark));
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.15);
            transition: height 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            animation: growUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            height: 0;
        }
        
        @keyframes growUp {
            from { height: 0; }
            to { height: var(--final-height); }
        }
        
        .bar-chart-item:hover .bar-fill {
            background: linear-gradient(180deg, var(--brand-dark), var(--brand));
            box-shadow: 0 0 15px rgba(15, 118, 110, 0.4);
        }

        .bar-caption {
            min-width: 0;
            text-align: center;
        }

        .bar-label {
            font-weight: 800;
            color: var(--ink);
            font-size: 0.9rem;
            line-height: 1.1;
        }

        .bar-date {
            color: #64748b;
            font-size: 0.75rem;
            margin-top: 0.15rem;
            line-height: 1.25;
        }
        
        .bar-value {
            color: var(--brand);
            font-weight: 700;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            line-height: 1.25;
        }

        @media (max-width: 767.98px) {
            .dashboard-header {
                align-items: stretch;
                flex-direction: column;
                padding: 1.25rem;
            }

            .dashboard-list-item {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .dashboard-list-item > .text-end {
                grid-column: 1 / -1;
                text-align: left !important;
                margin-top: 0.5rem;
                padding-left: 3.5rem;
            }

            .bar-chart {
                gap: 0.5rem;
                padding: 1rem 0.75rem;
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
                        <div class="bar-fill" style="--final-height: {{ $height }}%;"></div>
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
