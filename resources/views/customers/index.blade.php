@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Customers</h1>
            <p class="page-subtitle">Track loyalty points, visits, and customer spend.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> New Sale
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="app-card p-3">
                <div class="text-muted small">Customers</div>
                <div class="h3 mb-0">{{ number_format($summary['total']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="app-card p-3">
                <div class="text-muted small">Active Points</div>
                <div class="h3 mb-0">{{ number_format($summary['points']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="app-card p-3">
                <div class="text-muted small">Total Spend</div>
                <div class="h3 mb-0">${{ number_format($summary['spent'], 2) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="app-card p-3">
                <div class="text-muted small">Visits</div>
                <div class="h3 mb-0">{{ number_format($summary['visits']) }}</div>
            </div>
        </div>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Loyalty Members</h2>
            <form method="GET" action="{{ route('customers.index') }}" class="d-flex gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm"
                    placeholder="Search name or phone">
                <button class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        @if($customers->isEmpty())
            <div class="empty-state">No loyalty customers yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Points</th>
                            <th>Visits</th>
                            <th>Total Spend</th>
                            <th>Last Order</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $customer->name ?: 'Walk-in Customer' }}</div>
                                    <div class="small text-muted">{{ $customer->phone }}</div>
                                </td>
                                <td>
                                    <span class="badge text-bg-primary">{{ number_format($customer->points_balance) }} pts</span>
                                </td>
                                <td>{{ number_format($customer->visits) }}</td>
                                <td>${{ number_format($customer->total_spent, 2) }}</td>
                                <td>{{ $customer->last_order_at?->format('M d, Y H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">
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

    <div class="d-flex justify-content-center mt-4">{{ $customers->links() }}</div>
@endsection
