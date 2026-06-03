<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coffee Ben10</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230f766e'/%3E%3Ctext x='32' y='39' font-family='Arial,sans-serif' font-size='22' font-weight='700' text-anchor='middle' fill='white'%3ECB%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand: #0f766e;
            --brand-dark: #115e59;
            --accent: #d97706;
            --ink: #111827;
            --muted: #64748b;
            --line: #dbe4ef;
            --surface: #ffffff;
            --soft: #f5f7fb;
            --surface-subtle: #f8fafc;
            --shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        body {
            background: var(--soft);
            color: var(--ink);
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        .navbar {
            border-bottom: 1px solid var(--line);
        }

        .app-topbar {
            background: rgba(255, 255, 255, .96) !important;
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
            min-height: 64px;
        }

        .brand-lockup {
            color: var(--ink);
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            font-weight: 800;
            text-decoration: none;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #2563eb);
            box-shadow: 0 10px 22px rgba(15, 118, 110, .22);
        }

        .brand-small {
            color: var(--muted);
            display: block;
            font-size: .72rem;
            font-weight: 700;
            line-height: 1;
        }

        .brand-name {
            display: block;
            line-height: 1.05;
        }

        .topbar-user {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .35rem .55rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--brand);
            background: rgba(20, 184, 166, .12);
            font-weight: 800;
        }

        .stat-card {
            border-radius: .5rem;
            color: #fff
        }

        .stat-card .value {
            font-size: 1.75rem;
            font-weight: 700
        }

        .stat-card .label {
            opacity: .85
        }

        .table-sm td,
        .table-sm th {
            padding: .5rem .6rem
        }

        .card-table thead th {
            background: #f8f9fa
        }

        .sidebar {
            min-height: 100vh;
            border-right: 1px solid var(--line);
            background: #fff !important;
            box-shadow: 8px 0 24px rgba(15, 23, 42, .03);
        }

        .sidebar .nav-link {
            align-items: center;
            color: #374151;
            display: flex;
            gap: .65rem;
            border-radius: .5rem;
            margin-bottom: .2rem;
            padding: .68rem .75rem;
            font-weight: 650;
        }

        .sidebar .nav-link.active {
            background: rgba(15, 118, 110, .1);
            color: var(--brand);
            font-weight: 600;
        }

        .sidebar .nav-link:hover {
            background: #f1f5f9;
            color: var(--ink);
        }

        .sidebar .nav-link i {
            width: 1.1rem;
        }

        .sidebar-profile {
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            background: var(--surface-subtle);
            padding: .8rem;
            margin-bottom: 1rem;
        }

        .nav-section-label {
            color: var(--muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            margin: 1rem 0 .35rem;
            text-transform: uppercase;
        }

        .nav-feature-link {
            align-items: center;
            display: flex;
            gap: .5rem;
            font-size: .88rem;
            padding: .42rem .65rem;
        }

        .nav-feature-link i {
            color: var(--brand);
            font-size: .95rem;
            width: 1rem;
        }

        .topbar-actions {
            gap: .5rem
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--line);
        }

        .page-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0;
        }

        .page-subtitle {
            color: var(--muted);
            margin: .25rem 0 0;
        }

        .app-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: .5rem;
            box-shadow: var(--shadow);
        }

        .app-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--line);
        }

        .app-card-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }

        .app-table {
            margin-bottom: 0;
        }

        .app-table th {
            color: var(--muted);
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            background: var(--surface-subtle);
            border-bottom: 1px solid var(--line);
        }

        .app-table td {
            vertical-align: middle;
        }

        .empty-state {
            color: var(--muted);
            padding: 2rem 1rem;
            text-align: center;
        }

        .product-thumb {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #f1f5f9;
        }

        .soft-icon {
            width: 42px;
            height: 42px;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
        }

        .btn-primary:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
        }

        .btn-outline-primary {
            border-color: var(--brand);
            color: var(--brand);
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .text-primary {
            color: var(--brand) !important;
        }

        .bg-primary {
            background-color: var(--brand) !important;
        }

        .text-bg-primary {
            background-color: var(--brand) !important;
            color: #fff !important;
        }

        .pos-hero {
            background: #fff;
        }

        .pos-chip {
            display: inline-flex;
            align-items: center;
            padding: .55rem .8rem;
            border-radius: .5rem;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            font-weight: 600;
        }

        .product-card {
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 26px rgba(15, 23, 42, .08);
            border-color: rgba(15, 118, 110, .25);
        }

        .product-price {
            min-width: 74px;
            text-align: right;
            font-weight: 700;
            color: var(--brand);
        }

        .placeholder-thumb {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--muted);
            background: #eef2f7;
        }

        .qty-input {
            min-width: 80px;
        }

        .report-chart-body {
            min-height: 320px;
            padding: 1rem;
            position: relative;
        }

        .report-chart-body.compact {
            min-height: 260px;
        }

        .report-chart-body canvas {
            max-height: 320px;
            width: 100% !important;
        }

        .report-chart-empty {
            color: var(--muted);
            padding: 3rem 1rem;
            text-align: center;
        }

        .app-footer {
            color: var(--muted);
            font-size: .82rem;
            padding-top: 1.25rem;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .page-head {
                align-items: stretch;
                flex-direction: column;
            }
        }

        /* Pagination styling */
        .pagination {
            margin: 1rem 0 0;
            gap: 0;
        }

        .pagination .page-link {
            border: 1px solid #e5e7eb;
            color: var(--brand);
            padding: 0.5rem 0.75rem;
            margin: 0;
        }

        .pagination .page-link:hover {
            background-color: rgba(15, 118, 110, .08);
            color: var(--brand-dark);
            border-color: rgba(15, 118, 110, .2);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--brand);
            border-color: var(--brand);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #d1d5db;
            background-color: #f9fafb;
            border-color: #e5e7eb;
            cursor: not-allowed;
        }
    </style>
    {{-- @viteWhenAvailable --}}
</head>

<body>
    @auth
        <nav class="navbar navbar-expand-lg navbar-light app-topbar sticky-top">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list"></i>
                </button>
                <a class="brand-lockup d-md-none" href="{{ route('dashboard') }}">
                    <span class="brand-mark"><i class="bi bi-cup-hot"></i></span>
                    <span>
                        Coffee Ben10
                        <span class="brand-small">POS System</span>
                    </span>
                </a>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> New Sale
                    </a>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-sm" title="Edit Profile">
                        <i class="bi bi-person"></i>
                    </a>
                    <div class="topbar-user">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="d-none d-sm-inline small fw-semibold">{{ auth()->user()->name }}</span>
                    </div>
                    @if (Route::has('logout'))
                        <a class="btn btn-outline-secondary btn-sm" href="#"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit()">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
                    @endif
                </div>
            </div>
        </nav>

        <!-- Offcanvas for mobile sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header">
                <a class="brand-lockup" href="{{ route('dashboard') }}">
                    <span class="brand-mark"><i class="bi bi-cup-hot"></i></span>
                    <span>
                        Coffee Ben10
                        <span class="brand-small">POS System</span>
                    </span>
                </a>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="sidebar-profile">
                    <div class="small text-muted">Signed in as</div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item"><a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ Request::routeIs('pos.*') ? 'active' : '' }}"
                            href="{{ route('pos.index') }}"><i class="bi bi-cup-hot"></i> POS</a></li>
                    @if(auth()->user()->canManageBackOffice())
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}"
                                href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('orders.*') ? 'active' : '' }}"
                                href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> Orders</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}"
                                href="{{ route('customers.index') }}"><i class="bi bi-people"></i> Customers</a></li>
                        @if(auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                            <li class="nav-item"><a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}"><i class="bi bi-person-gear"></i> Users</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('payments.*') ? 'active' : '' }}"
                                href="{{ route('payments.index') }}"><i class="bi bi-credit-card"></i> Payments</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('promos.*') ? 'active' : '' }}"
                                href="{{ route('promos.index') }}"><i class="bi bi-tag"></i> Promos</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}"
                                href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i> Reports</a></li>
                    @endif
                </ul>


            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-md-block sidebar p-3">
                    <div class="mb-3">
                        <a class="brand-lockup" href="{{ route('dashboard') }}">
                            <span class="brand-mark"><i class="bi bi-cup-hot"></i></span>
                            <span>
                                Coffee Ben10
                                <span class="brand-small">POS System</span>
                            </span>
                        </a>
                    </div>
                    @if (Route::has('login'))
                        <div class="sidebar-profile">
                            <div class="small text-muted">Signed in as</div>
                            <div class="fw-bold">{{ auth()->user()->name }}</div>
                            <div class="small text-muted">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                    @endif

                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('pos.*') ? 'active' : '' }}"
                                href="{{ route('pos.index') }}"><i class="bi bi-cup-hot"></i> POS</a>
                        </li>
                        @if(auth()->user()->canManageBackOffice())
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}"
                                    href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('orders.*') ? 'active' : '' }}"
                                    href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}"
                                    href="{{ route('customers.index') }}"><i class="bi bi-people"></i> Customers</a>
                            </li>
                            @if(auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}"
                                        href="{{ route('users.index') }}"><i class="bi bi-person-gear"></i> Users</a>
                                </li>
                            @endif
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('project.overview') ? 'active' : '' }}"
                                href="{{ route('project.overview') }}"><i class="bi bi-journal-text"></i> Project
                                Overview</a>
                        </li> --}}
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}"><i
                                        class="bi bi-credit-card"></i> Payments</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('promos.*') ? 'active' : '' }}" href="{{ route('promos.index') }}"><i
                                        class="bi bi-tag"></i> Promos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i
                                        class="bi bi-graph-up"></i> Reports</a>
                            </li>
                        @endif
                    </ul>


                </nav>

                <main class="col-12 col-md-10 ms-sm-auto px-4 py-4">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @yield('content')

                    <footer class="app-footer">
                        Copyright &copy; {{ date('Y') }} by Reaksa Vuthy.
                    </footer>
                </main>
            </div>
        </div>
    @else
        <main class="container py-5">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @yield('content')

            <footer class="app-footer">
                Copyright &copy; {{ date('Y') }} by Reaksa Vuthy.
            </footer>
        </main>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.confirm-delete').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.confirm-cancel').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (!confirm('Are you sure you want to cancel this cart?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
