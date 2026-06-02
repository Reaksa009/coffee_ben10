@extends('layouts.app')

@section('content')
    <style>
        .overview-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
            gap: 1.5rem;
            align-items: stretch;
            margin-bottom: 1.5rem;
        }

        .overview-intro {
            padding: 1.5rem;
        }

        .overview-title {
            font-size: clamp(1.7rem, 3vw, 2.4rem);
            font-weight: 800;
            margin: 0 0 .75rem;
        }

        .overview-photo {
            min-height: 280px;
            border-radius: .5rem;
            background-image:
                linear-gradient(180deg, rgba(17, 24, 39, .08), rgba(17, 24, 39, .48)),
                url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .overview-card {
            height: 100%;
            padding: 1.15rem;
        }

        .overview-card h2 {
            align-items: center;
            display: flex;
            gap: .55rem;
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: .85rem;
        }

        .overview-card ul {
            margin-bottom: 0;
            padding-left: 1.1rem;
        }

        .overview-card li {
            margin-bottom: .38rem;
        }

        .overview-pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .overview-pill-list li {
            border: 1px solid #dbe4ef;
            border-radius: 999px;
            background: #f8fafc;
            padding: .35rem .65rem;
            font-size: .86rem;
            font-weight: 600;
        }

        .example-box {
            border-left: 3px solid var(--brand);
            background: #f8fafc;
            padding: .8rem .95rem;
            border-radius: .35rem;
        }

        @media (max-width: 991.98px) {
            .overview-hero,
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-head">
        <div>
            <h1 class="page-title">Coffee Shop POS Overview</h1>
            <p class="page-subtitle">Purpose, core functions, and business benefits for the POS project.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <section class="overview-hero">
        <div class="app-card overview-intro">
            <div class="soft-icon text-primary bg-primary bg-opacity-10 mb-3">
                <i class="bi bi-shop fs-4"></i>
            </div>
            <h2 class="overview-title">Purpose of POS System</h2>
            <p class="text-muted mb-3">
                A POS system helps a coffee shop replace manual cashier work with a digital process that manages sales,
                menu items, inventory, employee activity, customer orders, and financial reports.
            </p>
            <ul class="overview-pill-list">
                <li>Sales transactions</li>
                <li>Menu items</li>
                <li>Inventory</li>
                <li>Employee activity</li>
                <li>Customer orders</li>
                <li>Financial reports</li>
            </ul>
        </div>
        <div class="overview-photo" aria-label="Coffee shop counter"></div>
    </section>

    <div class="overview-grid">
        <section class="app-card overview-card">
            <h2><i class="bi bi-cart-check text-primary"></i> Sales Management</h2>
            <p class="text-muted mb-2">The system records coffee orders, food items, discounts, taxes, and payment methods.</p>
            <ul>
                <li>Faster checkout</li>
                <li>Reduced cashier errors</li>
                <li>Real-time sales tracking</li>
            </ul>
        </section>

        <section class="app-card overview-card">
            <h2><i class="bi bi-box-seam text-primary"></i> Inventory Management</h2>
            <p class="text-muted mb-2">Tracks coffee beans, milk, syrups, cups, and bakery stock.</p>
            <ul>
                <li>Prevents stock shortages</li>
                <li>Reduces waste</li>
                <li>Monitors ingredient usage</li>
            </ul>
            <p class="text-muted small mt-3 mb-0">Modern systems can track milk and syrup usage per drink modifier.</p>
        </section>

        <section class="app-card overview-card">
            <h2><i class="bi bi-sliders text-primary"></i> Menu & Modifier System</h2>
            <p class="text-muted mb-2">Coffee orders are highly customizable, so fast modifier selection is important.</p>
            <ul class="overview-pill-list">
                <li>Milk type</li>
                <li>Sugar level</li>
                <li>Syrup flavor</li>
                <li>Extra espresso shot</li>
                <li>Ice level</li>
            </ul>
        </section>

        <section class="app-card overview-card">
            <h2><i class="bi bi-people text-primary"></i> Customer Management</h2>
            <p class="text-muted mb-2">Supports loyalty points, visit tracking, customer history, and promotions.</p>
            <div class="example-box">
                <div class="fw-semibold">Current loyalty rule</div>
                <div class="text-muted">Earn 10 points per $1 paid, redeem 1 point as $0.01 discount.</div>
            </div>
        </section>

        <section class="app-card overview-card">
            <h2><i class="bi bi-bar-chart-line text-primary"></i> Reporting & Analytics</h2>
            <p class="text-muted mb-2">Generates reports that help owners understand daily performance.</p>
            <ul>
                <li>Daily sales</li>
                <li>Best-selling drinks</li>
                <li>Peak hours</li>
                <li>Employee performance</li>
                <li>Profit analysis</li>
            </ul>
        </section>

        <section class="app-card overview-card">
            <h2><i class="bi bi-graph-up-arrow text-primary"></i> Business Benefits</h2>
            <ul>
                <li>Better business decisions</li>
                <li>Staff scheduling optimization</li>
                <li>Revenue forecasting</li>
                <li>Stronger control over waste and stock</li>
            </ul>
        </section>
    </div>
@endsection
