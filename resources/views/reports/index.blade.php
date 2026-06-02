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
            border-radius: .75rem;
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