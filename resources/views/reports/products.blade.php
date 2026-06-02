@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Product Performance</h1>
            <p class="page-subtitle">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="GET" action="{{ route('reports.products') }}" class="app-card mb-4">
        <div style="padding: 1.5rem;">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label small">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                        value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label small">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                        value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="app-card">
        <div class="app-card-header">
            <h3 class="app-card-title">Best Selling Products</h3>
            <a href="{{ route('reports.export', ['type' => 'products', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                class="btn btn-sm btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>

        @if($products->isEmpty())
            <div class="empty-state">No products sold in the selected period</div>
        @else
            <table class="table app-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                        <th>Avg Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $data)
                        <tr>
                            <td>
                                <strong>{{ $data['product']->name }}</strong>
                            </td>
                            <td>{{ $data['product']->category ?? '-' }}</td>
                            <td>
                                <span class="badge text-bg-primary">{{ $data['quantity_sold'] }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format($data['revenue'], 2) }}</strong>
                            </td>
                            <td>
                                ${{ number_format($data['revenue'] / $data['quantity_sold'], 2) }}
                            </td>
                            <td>
                                <span class="badge text-bg-{{ $data['product']->stock <= 5 ? 'warning' : 'success' }}">
                                    {{ $data['product']->stock }} left
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection