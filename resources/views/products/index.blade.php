@extends('layouts.app')

@section('content')
    <style>
        .products-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem;
            margin-bottom: 1.25rem;
            border: 1px solid #e4e9f2;
            border-radius: .75rem;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .94), rgba(239, 246, 255, .9)),
                url('https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=1400&q=80');
            background-size: cover;
            background-position: center;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .08);
        }

        .products-kicker {
            color: var(--brand);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            margin-bottom: .35rem;
            text-transform: uppercase;
        }

        .products-title {
            font-size: clamp(1.75rem, 3vw, 2.45rem);
            font-weight: 800;
            margin: 0;
        }

        .products-menu-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .product-menu-card {
            border-radius: .75rem;
            border-color: #e4e9f2;
            box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
            display: flex;
            flex-direction: column;
        }

        .product-menu-card .product-thumb {
            aspect-ratio: 1 / .72;
        }

        .product-menu-card .placeholder-thumb {
            background:
                radial-gradient(circle at 35% 25%, rgba(37, 99, 235, .12), transparent 36%),
                linear-gradient(180deg, #f8fafc, #eef2ff);
        }

        .product-price-pill {
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #0f766e);
            padding: .35rem .65rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .product-stock-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: auto;
            padding-top: .9rem;
        }

        .product-card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-height: 0;
        }

        .category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .category-section {
            margin-bottom: 1.5rem;
        }

        .category-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .85rem;
            padding-bottom: .55rem;
            border-bottom: 1px solid #e4e9f2;
        }

        @media (max-width: 575.98px) {
            .product-stock-row {
                align-items: stretch;
                flex-direction: column;
            }

            .product-stock-row .btn {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {

            .products-hero,
            .products-menu-head {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>

    <div class="products-hero">
        <div>
            <div class="products-kicker">Coffee menu manager</div>
            <h1 class="products-title">Products</h1>
            <p class="page-subtitle mb-0">Manage drinks, snacks, categories, sizes, sugar levels, and stock.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Product
        </a>
    </div>

    @if($categories->isNotEmpty() || $selectedCategory)
        <div class="category-filter">
            <a href="{{ route('products.index') }}" class="btn btn-sm {{ $selectedCategory ? 'btn-outline-secondary' : 'btn-primary' }}">
                All
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category]) }}"
                    class="btn btn-sm {{ $selectedCategory === $category ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $category }}
                </a>
            @endforeach
        </div>
    @endif

    @if($products->isEmpty())
        <div class="app-card">
            <div class="empty-state">No products yet.</div>
        </div>
    @else
        <div class="products-menu-head">
            <div>
                <h2 class="h4 fw-bold mb-1">{{ $selectedCategory ?: 'Menu Items' }}</h2>
                <p class="text-muted mb-0">Keep the counter menu fresh and ready for checkout.</p>
            </div>
            <span class="badge text-bg-light border">{{ $products->total() }} products</span>
        </div>

        @foreach($productsByCategory as $categoryName => $categoryProducts)
            <section class="category-section">
                <div class="category-heading">
                    <h3 class="h5 fw-bold mb-0">
                        <i class="bi bi-tags me-1 text-primary"></i>{{ $categoryName }}
                    </h3>
                    <span class="badge text-bg-light border">{{ $categoryProducts->count() }} item{{ $categoryProducts->count() === 1 ? '' : 's' }}</span>
                </div>

                <div class="row g-3">
                    @foreach($categoryProducts as $product)
                        <div class="col-sm-6 col-xl-3">
                            <div class="product-card product-menu-card app-card h-100 overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="product-thumb" alt="{{ $product->name }}">
                                @else
                                    <div class="product-thumb placeholder-thumb">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                @endif

                                <div class="product-card-body p-3">
                                    <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                        <h2 class="h5 mb-0">{{ $product->name }}</h2>
                                        <span class="product-price-pill">${{ number_format($product->medium_price ?? $product->price, 2) }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">{{ Str::limit($product->description, 80) }}</p>

                                    @if($product->category || $product->coffee_size || $product->sugar)
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if($product->category)
                                                <span class="badge text-bg-primary">
                                                    <i class="bi bi-tag me-1"></i>{{ $product->category }}
                                                </span>
                                            @endif
                                            @if($product->coffee_size)
                                                <span class="badge text-bg-light border">
                                                    <i class="bi bi-cup-hot me-1"></i>{{ $product->coffee_size }}
                                                </span>
                                            @endif
                                            @if($product->sugar)
                                                <span class="badge text-bg-light border">
                                                    <i class="bi bi-droplet-half me-1"></i>Sugar: {{ $product->sugar }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge text-bg-light border">S: ${{ number_format($product->small_price ?? $product->price, 2) }}</span>
                                        <span class="badge text-bg-light border">M: ${{ number_format($product->medium_price ?? $product->price, 2) }}</span>
                                        <span class="badge text-bg-light border">L: ${{ number_format($product->large_price ?? $product->price, 2) }}</span>
                                    </div>

                                    <div class="product-stock-row">
                                        @if($product->stock <= 0)
                                            <span class="badge text-bg-danger">Out of stock</span>
                                        @elseif($product->stock <= 5)
                                            <span class="badge text-bg-warning">Low stock: {{ $product->stock }}</span>
                                        @else
                                            <span class="badge text-bg-light border">Stock: {{ $product->stock }}</span>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                class="confirm-delete flex-fill">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="d-flex justify-content-center mt-4">{{ $products->links() }}</div>
    @endif
@endsection
