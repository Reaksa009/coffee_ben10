@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Backup / Export</h1>
            <p class="page-subtitle">Export products, orders, customers, inventory, purchases, and daily backup data to CSV.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">CSV Exports</h2>
                </div>
                <div class="p-4" style="display: grid; gap: .8rem;">
                    <a href="{{ route('backup.export', 'products') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-box-seam me-2"></i> Export Products
                    </a>
                    <a href="{{ route('backup.export', 'orders') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-receipt me-2"></i> Export Orders
                    </a>
                    <a href="{{ route('backup.export', 'customers') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-people me-2"></i> Export Customers
                    </a>
                    <a href="{{ route('backup.export', 'inventory') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-boxes me-2"></i> Export Inventory
                    </a>
                    <a href="{{ route('backup.export', 'purchases') }}" class="btn btn-outline-primary text-start">
                        <i class="bi bi-basket-fill me-2"></i> Export Purchases
                    </a>
                </div>
            </section>
        </div>
        <div class="col-lg-4">
            <section class="app-card p-4">
                <h2 class="h5 fw-bold mb-3">Daily Backup</h2>
                <form method="GET" action="{{ route('backup.export', 'daily') }}">
                    <div class="mb-3">
                        <label class="form-label">Business Date</label>
                        <input type="date" name="date" value="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-download me-1"></i> Download Daily CSV
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
