<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coffee Ben10</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230f766e'/%3E%3Ctext x='32' y='39' font-family='Arial,sans-serif' font-size='22' font-weight='700' text-anchor='middle' fill='white'%3ECB%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: var(--soft);
            color: var(--ink);
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        .navbar {
            border-bottom: 1px solid var(--line);
        }

        .app-topbar {
            background: rgba(255, 255, 255, 0.82) !important;
            backdrop-filter: blur(18px);
            box-shadow: 0 8px 30px rgba(15, 23, 42, .04);
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
            border-right: none !important;
            background: linear-gradient(180deg, #0e5e58, #053330) !important;
            box-shadow: 8px 0 24px rgba(15, 23, 42, .08);
            color: rgba(255, 255, 255, 0.75) !important;
        }

        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1000;
                width: 16.666667%;
                height: 100vh;
                overflow-y: auto;
            }
            .app-topbar {
                margin-left: 16.666667%;
                width: calc(100% - 16.666667%);
            }
        }

        .sidebar .brand-lockup {
            color: #fff !important;
        }
        
        .sidebar .brand-small {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .sidebar .nav-link {
            align-items: center;
            color: rgba(255, 255, 255, 0.8) !important;
            display: flex;
            gap: .65rem;
            border-radius: .5rem;
            margin-bottom: .2rem;
            padding: .68rem .75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
            font-weight: 700;
            box-shadow: inset 3px 0 0 #34d399;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #fff !important;
        }

        .sidebar .nav-link i {
            width: 1.1rem;
        }

        .sidebar-dropdown-toggle {
            background: transparent;
            border: 0;
            text-align: left;
            width: 100%;
        }

        .sidebar-caret {
            margin-left: auto;
            transition: transform .18s ease;
            width: auto;
        }

        .sidebar-dropdown-toggle[aria-expanded="true"] .sidebar-caret {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            display: grid;
            gap: .5rem;
            margin: .25rem 0 .65rem;
            padding-left: 0.5rem;
        }

        .sidebar-submenu .nav-link {
            background: transparent;
            border-left: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 0;
            color: rgba(255, 255, 255, 0.65) !important;
            margin-bottom: 0;
            padding: .5rem .75rem .5rem 1.25rem;
            font-size: .88rem;
            font-weight: 600;
            transition: all .2s ease;
        }

        .sidebar-submenu .nav-link:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
            border-left-color: #34d399 !important;
        }

        .sidebar-submenu .nav-link.active {
            background: rgba(255, 255, 255, 0.1) !important;
            border-left-color: #34d399 !important;
            color: #34d399 !important;
            font-weight: 700;
        }

        .sidebar-submenu .nav-link.disabled {
            background: transparent;
            border-left-color: transparent;
            color: rgba(255, 255, 255, 0.3);
            cursor: not-allowed;
            opacity: 0.5;
        }

        .sidebar-profile {
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: .5rem;
            background: rgba(255, 255, 255, 0.05) !important;
            padding: .8rem;
            margin-bottom: 1rem;
            color: #fff !important;
        }

        .sidebar-profile .text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .sidebar .nav-section-label {
            color: rgba(255, 255, 255, 0.4) !important;
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

        .app-alert {
            align-items: flex-start;
            border: 1px solid var(--line);
            border-left: 4px solid var(--brand);
            border-radius: .5rem;
            box-shadow: var(--shadow);
            display: flex;
            gap: .8rem;
            margin-bottom: 1rem;
            padding: .95rem 1rem;
        }

        .app-alert-success {
            background: linear-gradient(135deg, #ecfdf5, #ffffff);
            border-left-color: #10b981;
        }

        .app-alert-error {
            background: linear-gradient(135deg, #fef2f2, #ffffff);
            border-left-color: #ef4444;
        }

        .app-alert-icon {
            align-items: center;
            border-radius: .5rem;
            display: inline-flex;
            flex: 0 0 auto;
            height: 38px;
            justify-content: center;
            width: 38px;
        }

        .app-alert-content {
            flex: 1 1 auto;
            min-width: 0;
        }

        .app-alert-success .app-alert-icon {
            background: rgba(16, 185, 129, .12);
            color: #047857;
        }

        .app-alert-error .app-alert-icon {
            background: rgba(239, 68, 68, .12);
            color: #b91c1c;
        }

        .app-alert-title {
            font-size: .86rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: .15rem;
        }

        .app-alert-message {
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.4;
        }

        .app-alert-dismiss {
            align-self: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: .5rem;
            color: var(--ink);
            flex: 0 0 auto;
            font-size: .82rem;
            font-weight: 800;
            padding: .4rem .7rem;
        }

        .app-alert-dismiss:hover {
            background: var(--surface-subtle);
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

        /* Premium aesthetics override settings */
        .form-control, .form-select {
            border-radius: 0.5rem !important;
            border-color: #cbd5e1 !important;
            padding: 0.55rem 0.75rem !important;
            font-size: 0.88rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15) !important;
        }

        .btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
        }
        
        .btn-primary:active, .btn-primary:focus {
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.25) !important;
        }

        .app-card {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02) !important;
            border: 1px solid var(--line) !important;
            overflow: hidden;
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .app-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px rgba(15, 23, 42, 0.06) !important;
        }

        .app-card-header {
            background-color: var(--surface-subtle) !important;
            border-bottom: 1px solid var(--line) !important;
        }

        .app-table th {
            color: #475569 !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
            background: #f8fafc !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.75rem 1rem !important;
        }

        .app-table td {
            padding: 0.95rem 1rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        .app-table tr:hover td {
            background-color: rgba(15, 118, 110, 0.02) !important;
        }

        .badge {
            border-radius: 0.5rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em;
        }

        .empty-state {
            padding: 3rem 1.5rem !important;
            border-radius: 0.75rem;
            background: #fff;
            border: 1px dashed var(--line);
        }

        .bg-teal-subtle {
            background-color: rgba(20, 184, 166, 0.12) !important;
        }

        /* Inline table inputs styling */
        .app-table .form-control, .app-table .form-select {
            background-color: transparent !important;
            border-color: transparent !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            transition: all 0.15s ease !important;
        }
        .app-table .form-control:hover, .app-table .form-select:hover {
            border-color: var(--line) !important;
            background-color: #fff !important;
        }
        .app-table .form-control:focus, .app-table .form-select:focus {
            border-color: var(--brand) !important;
            background-color: #fff !important;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12) !important;
        }

        .bg-amber-subtle {
            background-color: #fef3c7 !important;
        }
        .text-amber {
            color: #d97706 !important;
        }
        .border-amber-subtle {
            border-color: #fde68a !important;
        }

        .bg-emerald-subtle {
            background-color: #d1fae5 !important;
        }
        .text-emerald {
            color: #047857 !important;
        }
        .border-emerald-subtle {
            border-color: #a7f3d0 !important;
        }

        .bg-slate-subtle {
            background-color: #f1f5f9 !important;
        }
        .text-slate {
            color: #475569 !important;
        }
        .border-slate-subtle {
            border-color: #cbd5e1 !important;
        }

        .bg-blue-subtle {
            background-color: #dbeafe !important;
        }
        .text-blue {
            color: #1d4ed8 !important;
        }
        .border-blue-subtle {
            border-color: #bfdbfe !important;
        }

        .bg-rose-subtle {
            background-color: #ffe4e6 !important;
        }
        .text-rose {
            color: #be123c !important;
        }
        .border-rose-subtle {
            border-color: #fecdd3 !important;
        }
    </style>
    {{-- @viteWhenAvailable --}}
</head>

<body>
    @auth
        @php
            $settingsOpen = Request::routeIs('categories.*')
                || Request::routeIs('products.*')
                || Request::routeIs('promos.*')
                || Request::routeIs('inventory.*')
                || Request::routeIs('shop-settings.*');
            $purchaseOpen = Request::routeIs('suppliers.*') || Request::routeIs('purchases.*');
        @endphp
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
                    <li class="nav-item"><a class="nav-link {{ Request::routeIs('cashier-shifts.*') ? 'active' : '' }}"
                            href="{{ route('cashier-shifts.index') }}"><i class="bi bi-cash-stack"></i> Shift Closing</a></li>
                    @if(auth()->user()->canManageBackOffice())
                        <li class="nav-item">
                            <button class="nav-link sidebar-dropdown-toggle {{ $settingsOpen ? 'active' : '' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#mobileSettingsMenu"
                                aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}" aria-controls="mobileSettingsMenu">
                                <i class="bi bi-gear-fill"></i> Settings <i class="bi bi-chevron-down sidebar-caret"></i>
                            </button>
                            <div id="mobileSettingsMenu" class="collapse {{ $settingsOpen ? 'show' : '' }}">
                                <div class="sidebar-submenu">
                                    <a class="nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}"
                                        href="{{ route('categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
                                    <a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}"
                                        href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
                                    <a class="nav-link {{ Request::routeIs('promos.*') ? 'active' : '' }}"
                                        href="{{ route('promos.index') }}"><i class="bi bi-percent"></i> Discounts</a>
                                    <a class="nav-link {{ Request::routeIs('inventory.*') ? 'active' : '' }}"
                                        href="{{ route('inventory.index') }}"><i class="bi bi-boxes"></i> Inventory</a>
                                    <a class="nav-link {{ Request::routeIs('shop-settings.*') ? 'active' : '' }}"
                                        href="{{ route('shop-settings.edit') }}"><i class="bi bi-shop"></i> Shop Settings</a>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link sidebar-dropdown-toggle {{ $purchaseOpen ? 'active' : '' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#mobilePurchaseMenu"
                                aria-expanded="{{ $purchaseOpen ? 'true' : 'false' }}" aria-controls="mobilePurchaseMenu">
                                <i class="bi bi-bag-fill"></i> Purchase Management <i class="bi bi-chevron-down sidebar-caret"></i>
                            </button>
                            <div id="mobilePurchaseMenu" class="collapse {{ $purchaseOpen ? 'show' : '' }}">
                                <div class="sidebar-submenu">
                                    <a class="nav-link {{ Request::routeIs('suppliers.*') ? 'active' : '' }}"
                                        href="{{ route('suppliers.index') }}"><i class="bi bi-info-circle-fill"></i> Supplier Info</a>
                                    <a class="nav-link {{ Request::routeIs('purchases.*') ? 'active' : '' }}"
                                        href="{{ route('purchases.index') }}"><i class="bi bi-basket-fill"></i> Purchase Info</a>
                                </div>
                            </div>
                        </li>
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
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}"
                                href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i> Reports</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}"
                                href="{{ route('activity-logs.index') }}"><i class="bi bi-clock-history"></i> Activity Log</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('backup.*') ? 'active' : '' }}"
                                href="{{ route('backup.index') }}"><i class="bi bi-download"></i> Backup</a></li>
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
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('cashier-shifts.*') ? 'active' : '' }}"
                                href="{{ route('cashier-shifts.index') }}"><i class="bi bi-cash-stack"></i> Shift Closing</a>
                        </li>
                        @if(auth()->user()->canManageBackOffice())
                            <li class="nav-item">
                                <button class="nav-link sidebar-dropdown-toggle {{ $settingsOpen ? 'active' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#desktopSettingsMenu"
                                    aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}" aria-controls="desktopSettingsMenu">
                                    <i class="bi bi-gear-fill"></i> Settings <i class="bi bi-chevron-down sidebar-caret"></i>
                                </button>
                                <div id="desktopSettingsMenu" class="collapse {{ $settingsOpen ? 'show' : '' }}">
                                    <div class="sidebar-submenu">
                                        <a class="nav-link {{ Request::routeIs('categories.*') ? 'active' : '' }}"
                                            href="{{ route('categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
                                        <a class="nav-link {{ Request::routeIs('products.*') ? 'active' : '' }}"
                                            href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
                                        <a class="nav-link {{ Request::routeIs('promos.*') ? 'active' : '' }}"
                                            href="{{ route('promos.index') }}"><i class="bi bi-percent"></i> Discounts</a>
                                        <a class="nav-link {{ Request::routeIs('inventory.*') ? 'active' : '' }}"
                                            href="{{ route('inventory.index') }}"><i class="bi bi-boxes"></i> Inventory</a>
                                        <a class="nav-link {{ Request::routeIs('shop-settings.*') ? 'active' : '' }}"
                                            href="{{ route('shop-settings.edit') }}"><i class="bi bi-shop"></i> Shop Settings</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link sidebar-dropdown-toggle {{ $purchaseOpen ? 'active' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#desktopPurchaseMenu"
                                    aria-expanded="{{ $purchaseOpen ? 'true' : 'false' }}" aria-controls="desktopPurchaseMenu">
                                    <i class="bi bi-bag-fill"></i> Purchase Management <i class="bi bi-chevron-down sidebar-caret"></i>
                                </button>
                                <div id="desktopPurchaseMenu" class="collapse {{ $purchaseOpen ? 'show' : '' }}">
                                    <div class="sidebar-submenu">
                                        <a class="nav-link {{ Request::routeIs('suppliers.*') ? 'active' : '' }}"
                                            href="{{ route('suppliers.index') }}"><i class="bi bi-info-circle-fill"></i> Supplier Info</a>
                                        <a class="nav-link {{ Request::routeIs('purchases.*') ? 'active' : '' }}"
                                            href="{{ route('purchases.index') }}"><i class="bi bi-basket-fill"></i> Purchase Info</a>
                                    </div>
                                </div>
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
                                <a class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i
                                        class="bi bi-graph-up"></i> Reports</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}"
                                    href="{{ route('activity-logs.index') }}"><i class="bi bi-clock-history"></i> Activity Log</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ Request::routeIs('backup.*') ? 'active' : '' }}"
                                    href="{{ route('backup.index') }}"><i class="bi bi-download"></i> Backup</a>
                            </li>
                        @endif
                    </ul>


                </nav>

                <main class="col-12 col-md-10 ms-sm-auto px-4 py-4">
                    @if(session('error'))
                        <div class="app-alert app-alert-error" role="alert">
                            <span class="app-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                            <span class="app-alert-content">
                                <span class="app-alert-title d-block">Action needed</span>
                                <span class="app-alert-message">{{ session('error') }}</span>
                            </span>
                            <button type="button" class="app-alert-dismiss" data-app-alert-dismiss>Done</button>
                        </div>
                    @endif
                    @if(session('success') && ! Request::routeIs('pos.receipt'))
                        <div class="app-alert app-alert-success" role="status">
                            <span class="app-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                            <span class="app-alert-content">
                                <span class="app-alert-title d-block">Success</span>
                                <span class="app-alert-message">{{ session('success') }}</span>
                            </span>
                            <button type="button" class="app-alert-dismiss" data-app-alert-dismiss>Done</button>
                        </div>
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
                <div class="app-alert app-alert-error" role="alert">
                    <span class="app-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <span class="app-alert-content">
                        <span class="app-alert-title d-block">Action needed</span>
                        <span class="app-alert-message">{{ session('error') }}</span>
                    </span>
                    <button type="button" class="app-alert-dismiss" data-app-alert-dismiss>Done</button>
                </div>
            @endif
            @if(session('success') && ! Request::routeIs('pos.receipt'))
                <div class="app-alert app-alert-success" role="status">
                    <span class="app-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <span class="app-alert-content">
                        <span class="app-alert-title d-block">Success</span>
                        <span class="app-alert-message">{{ session('success') }}</span>
                    </span>
                    <button type="button" class="app-alert-dismiss" data-app-alert-dismiss>Done</button>
                </div>
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

            document.querySelectorAll('[data-app-alert-dismiss]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const alert = button.closest('.app-alert');
                    if (alert) {
                        alert.remove();
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
