@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Sales Reports</h1>
            <p class="page-subtitle">Monitor sales performance, revenue, and trends</p>
        </div>
    </div>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: 1.5rem;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
        }

        .stat-label {
            color: #6b7280;
            font-size: .875rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: .5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
        }

        .stat-change {
            font-size: .875rem;
            margin-top: .5rem;
            color: #059669;
        }
    </style>

    @php
        $hasOverviewTrend = collect($overviewTrend)->sum('orders') > 0 || collect($overviewTrend)->sum('revenue') > 0;
        $hasPaymentChart = array_sum($paymentChart['totals']) > 0;
    @endphp

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Today's Orders</div>
            <div class="stat-value">{{ $todayStats['orders'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Today's Revenue</div>
            <div class="stat-value">${{ number_format($todayStats['revenue'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Today's Discounts</div>
            <div class="stat-value">${{ number_format($todayStats['discounts'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">This Month Orders</div>
            <div class="stat-value">{{ $monthStats['orders'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">This Month Revenue</div>
            <div class="stat-value">${{ number_format($monthStats['revenue'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">This Month Discounts</div>
            <div class="stat-value">${{ number_format($monthStats['discounts'], 2) }}</div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <div>
                        <h3 class="app-card-title">Revenue Trend</h3>
                        <div class="text-muted small">Paid order revenue and order count for the last 7 days</div>
                    </div>
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-up-right me-1"></i> Details
                    </a>
                </div>
                <div class="report-chart-body">
                    @if($hasOverviewTrend)
                        <canvas id="overviewTrendChart" aria-label="Seven day revenue trend chart" role="img"></canvas>
                    @else
                        <div class="report-chart-empty">No paid orders in the last 7 days</div>
                    @endif
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <div>
                        <h3 class="app-card-title">Payments Today</h3>
                        <div class="text-muted small">Transaction total by payment method</div>
                    </div>
                </div>
                <div class="report-chart-body compact">
                    @if($hasPaymentChart)
                        <canvas id="paymentMethodChart" aria-label="Payment method chart" role="img"></canvas>
                    @else
                        <div class="report-chart-empty">No payment methods today</div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">Payment Methods (Today)</h3>
                </div>
                @if($paymentMethods->isEmpty())
                    <div class="empty-state">No transactions today</div>
                @else
                    <div style="padding: 1rem;">
                        @foreach($paymentMethods as $method)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3"
                                style="border: 1px solid #e5e7eb; border-radius: .5rem;">
                                <div>
                                    <div class="fw-semibold">{{ ucfirst($method->payment_method) }}</div>
                                    <div class="text-muted small">{{ $method->count }} transaction(s)</div>
                                </div>
                                <div class="fw-bold text-primary">${{ number_format($method->total, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">Quick Actions</h3>
                </div>
                <div style="padding: 1.5rem; display: grid; gap: 1rem;">
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-primary">
                        <i class="bi bi-graph-up me-2"></i> View Sales Report
                    </a>
                    <a href="{{ route('reports.products') }}" class="btn btn-outline-primary">
                        <i class="bi bi-box-seam me-2"></i> Product Performance
                    </a>
                    <a href="{{ route('reports.export', ['type' => 'sales']) }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i> Export Sales (CSV)
                    </a>
                    <a href="{{ route('reports.export', ['type' => 'products']) }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i> Export Products (CSV)
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Chart) {
                return;
            }

            const overviewTrend = @json($overviewTrend);
            const paymentChart = @json($paymentChart);
            const money = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });
            const chartColors = ['#0f766e', '#2563eb', '#d97706', '#7c3aed', '#dc2626', '#0891b2'];

            const overviewCanvas = document.getElementById('overviewTrendChart');
            if (overviewCanvas) {
                new Chart(overviewCanvas, {
                    type: 'line',
                    data: {
                        labels: overviewTrend.map((point) => point.label),
                        datasets: [
                            {
                                label: 'Revenue',
                                data: overviewTrend.map((point) => point.revenue),
                                borderColor: '#0f766e',
                                backgroundColor: 'rgba(15, 118, 110, .12)',
                                borderWidth: 3,
                                fill: true,
                                tension: .35,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Orders',
                                data: overviewTrend.map((point) => point.orders),
                                borderColor: '#2563eb',
                                backgroundColor: '#2563eb',
                                borderDash: [5, 5],
                                borderWidth: 2,
                                tension: .35,
                                yAxisID: 'y1',
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label === 'Revenue'
                                            ? 'Revenue: ' + money.format(context.parsed.y)
                                            : 'Orders: ' + context.parsed.y;
                                    },
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (value) => money.format(value) },
                                grid: { color: 'rgba(148, 163, 184, .18)' },
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                ticks: { precision: 0 },
                                grid: { drawOnChartArea: false },
                            },
                            x: { grid: { display: false } },
                        },
                    },
                });
            }

            const paymentCanvas = document.getElementById('paymentMethodChart');
            if (paymentCanvas) {
                new Chart(paymentCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: paymentChart.labels,
                        datasets: [{
                            data: paymentChart.totals,
                            backgroundColor: chartColors,
                            borderColor: '#fff',
                            borderWidth: 3,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '64%',
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const count = paymentChart.counts[context.dataIndex] || 0;
                                        return context.label + ': ' + money.format(context.parsed) + ' (' + count + ' tx)';
                                    },
                                },
                            },
                        },
                    },
                });
            }
        });
    </script>
@endsection
