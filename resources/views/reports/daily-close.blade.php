@extends('layouts.app')

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: 1.2rem;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
        }

        .stat-label {
            color: #6b7280;
            font-size: .78rem;
            font-weight: 700;
            margin-bottom: .35rem;
            text-transform: uppercase;
        }

        .stat-value {
            color: #111827;
            font-size: 1.55rem;
            font-weight: 800;
        }
    </style>

    <div class="page-head">
        <div>
            <h1 class="page-title">Daily Close Report</h1>
            <p class="page-subtitle">Sales, payments, best-selling drinks, discounts, and low-stock summary.</p>
        </div>
        <form method="GET" action="{{ route('reports.daily-close') }}" class="d-flex gap-2">
            <input type="date" name="date" value="{{ $date }}" class="form-control">
            <button class="btn btn-primary">
                <i class="bi bi-search me-1"></i> View
            </button>
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $summary['total_orders'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Paid Orders</div>
            <div class="stat-value">{{ $summary['paid_orders'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Revenue</div>
            <div class="stat-value">${{ number_format($summary['revenue'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Discounts</div>
            <div class="stat-value">${{ number_format($summary['discounts'], 2) }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Average Order</div>
            <div class="stat-value">${{ number_format($summary['average_order'], 2) }}</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <h2 class="app-card-title">Sales by Payment Method</h2>
                </div>
                @if($paymentMethods->isEmpty())
                    <div class="empty-state">No payments for this date.</div>
                @else
                    <div class="table-responsive">
                        <table class="table app-table">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Transactions</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethods as $method)
                                    <tr>
                                        <td class="fw-semibold">{{ $method['method'] }}</td>
                                        <td>{{ $method['count'] }}</td>
                                        <td class="text-end fw-bold">${{ number_format($method['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-6">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <h2 class="app-card-title">Best-selling Drinks</h2>
                </div>
                @if($bestSellers->isEmpty())
                    <div class="empty-state">No items sold for this date.</div>
                @else
                    <div class="table-responsive">
                        <table class="table app-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bestSellers as $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $item['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item['revenue'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-6">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <h2 class="app-card-title">Discounts Used</h2>
                </div>
                @if($discountsUsed->isEmpty())
                    <div class="empty-state">No discounts used for this date.</div>
                @else
                    <div class="table-responsive">
                        <table class="table app-table">
                            <thead>
                                <tr>
                                    <th>Discount</th>
                                    <th>Orders</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($discountsUsed as $discount)
                                    <tr>
                                        <td class="fw-semibold">{{ $discount['label'] }}</td>
                                        <td>{{ $discount['orders'] }}</td>
                                        <td class="text-end fw-bold text-success">-${{ number_format($discount['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-6">
            <section class="app-card h-100">
                <div class="app-card-header">
                    <h2 class="app-card-title">Low-stock Summary</h2>
                    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-boxes me-1"></i> Inventory
                    </a>
                </div>
                @if($lowStockItems->isEmpty())
                    <div class="empty-state">No low-stock alerts.</div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($lowStockItems as $item)
                            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="small text-muted">Low at {{ number_format($item->low_stock_quantity, 3) }} {{ $item->unit }}</div>
                                </div>
                                <span class="badge text-bg-warning">
                                    {{ number_format($item->quantity_on_hand, 3) }} {{ $item->unit }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
