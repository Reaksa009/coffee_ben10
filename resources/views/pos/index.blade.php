@extends('layouts.app')

@section('content')
    <style>
        .pos-cute-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            box-shadow: var(--shadow);
        }

        .pos-cute-title {
            font-size: clamp(1.5rem, 2vw, 2rem);
            font-weight: 800;
            margin: 0;
        }

        .pos-cute-kicker {
            color: var(--accent);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .cart-preview-list {
            display: grid;
            gap: .65rem;
            padding: 1rem;
        }

        .cart-preview-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: .75rem;
            align-items: center;
            padding: .75rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
        }

        .cart-preview-icon,
        .pos-product-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            background: rgba(20, 184, 166, .12);
            color: var(--brand);
        }

        .pos-menu-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .cute-product-card {
            border-radius: .5rem;
            border-color: var(--line);
        }

        .cute-product-card .product-thumb {
            aspect-ratio: 1 / .72;
        }

        .cute-product-card .placeholder-thumb {
            background: #eef2f7;
        }

        .cute-product-card form {
            width: 100%;
        }

        .product-card-footer {
            margin-top: auto;
            padding-top: .9rem;
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
            border-bottom: 1px solid var(--line);
        }

        @media (max-width: 767.98px) {
            .pos-menu-head {
                align-items: stretch;
                flex-direction: column;
            }

            .cart-preview-item {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .cart-preview-total {
                grid-column: 1 / -1;
            }
        }
    </style>

    @php
        $cart = session('cart', []);
        $cartCount = count($cart);
        $cartTotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $cartQuantity = collect($cart)->sum('quantity');
        $sizeOptions = ['Small', 'Medium', 'Large'];
        $sugarOptions = ['0%', '25%', '50%', '75%', '100%'];
        $isCoffeeCategory = fn($category) => in_array(strtolower((string) $category), ['coffee', 'caffee', 'cafe'], true);
    @endphp

    <div class="pos-cute-hero p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="pos-cute-kicker mb-2">New Sale</div>
                <h1 class="pos-cute-title mb-2">Build the cart fast.</h1>
                <p class="page-subtitle mb-0">Choose items, set modifiers, and move straight to payment.</p>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <span class="pos-chip"><i class="bi bi-cup-hot me-1"></i>{{ $cartQuantity }}
                        Cart{{ $cartQuantity === 1 ? '' : 's' }}</span>
                    <span class="pos-chip"><i class="bi bi-basket2 me-1"></i>{{ $cartCount }}
                        item{{ $cartCount === 1 ? '' : 's' }}</span>
                    <span class="pos-chip"><i class="bi bi-cash-coin me-1"></i>${{ number_format($cartTotal, 2) }}</span>
                    <a href="{{ route('pos.checkout') }}" class="btn btn-success">
                        <i class="bi bi-bag-check me-1"></i> Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($cartCount)
        <section class="app-card mb-4 overflow-hidden">
            <div class="app-card-header">
                <div>
                    <h2 class="app-card-title mb-1">Current Cart</h2>
                    <p class="text-muted small mb-0">Quick preview before checkout.</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge text-bg-primary">{{ $cartCount }} item{{ $cartCount === 1 ? '' : 's' }}</span>
                    <a href="{{ route('pos.checkout') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i> View Cart
                    </a>
                </div>
            </div>
            <div class="cart-preview-list">
                @foreach($cart as $item)
                    <div class="cart-preview-item">
                        <div class="cart-preview-icon"><i class="bi bi-cup-hot"></i></div>
                        <div>
                            <div class="fw-semibold">{{ $item['name'] }}</div>
                            <div class="text-muted small">
                                {{ $item['quantity'] }} x ${{ number_format($item['price'], 2) }}
                                @if(!empty($item['size']))
                                    <span class="ms-1">Size: {{ $item['size'] }}</span>
                                @endif
                                @if(!empty($item['sugar']))
                                    <span class="ms-1">Sugar: {{ $item['sugar'] }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="cart-preview-total fw-bold text-primary text-end">
                            ${{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($categories->isNotEmpty() || $selectedCategory)
        <div class="category-filter">
            <a href="{{ route('pos.index') }}" class="btn btn-sm {{ $selectedCategory ? 'btn-outline-secondary' : 'btn-primary' }}">
                All
            </a>
            @foreach($categories as $category)
                <a href="{{ route('pos.index', ['category' => $category]) }}"
                    class="btn btn-sm {{ $selectedCategory === $category ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $category }}
                </a>
            @endforeach
        </div>
    @endif

    @if($products->isEmpty())
        <div class="app-card">
            <div class="empty-state">No products available.</div>
        </div>
    @else
        <div class="pos-menu-head">
            <div>
                <h2 class="h4 fw-bold mb-1">{{ $selectedCategory ?: 'Menu Board' }}</h2>
                <p class="text-muted mb-0">Choose a coffee, snack, or sweet add-on.</p>
            </div>
            <span class="badge text-bg-light border">{{ $products->count() }} products</span>
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
                            <div class="product-card cute-product-card app-card h-100 overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="product-thumb" alt="{{ $product->name }}">
                                @else
                                    <div class="product-thumb placeholder-thumb">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                @endif

                                <div class="p-3 d-flex flex-column h-100">
                                    <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                        <div>
                                            <h2 class="h5 mb-1">{{ $product->name }}</h2>
                                            <p class="text-muted small mb-0">{{ Str::limit($product->description, 90) }}</p>
                                        </div>
                                        <span class="product-price"
                                            data-base-price="{{ $product->price }}"
                                            data-small-price="{{ $product->small_price ?? $product->price }}"
                                            data-medium-price="{{ $product->medium_price ?? $product->price }}"
                                            data-large-price="{{ $product->large_price ?? $product->price }}">
                                            ${{ number_format($product->medium_price ?? $product->price, 2) }}
                                        </span>
                                    </div>

                                    <div class="mt-3 d-flex flex-wrap gap-2">
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
                                        @if($product->stock <= 0)
                                            <span class="badge text-bg-danger">Out of stock</span>
                                        @elseif($product->stock <= 5)
                                            <span class="badge text-bg-warning">Low stock: {{ $product->stock }}</span>
                                        @else
                                            <span class="badge text-bg-light border">Stock: {{ $product->stock }}</span>
                                        @endif
                                        <form method="POST" action="{{ route('pos.add') }}" class="product-card-footer">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <div class="d-flex gap-2">
                                                <div class="input-group">
                                                    <span class="input-group-text">Qty</span>
                                                    <input type="number" name="quantity" value="1" min="1" class="form-control qty-input">
                                                </div>
                                            </div>
                                            <div class="input-group mt-2">
                                                <span class="input-group-text">Size</span>
                                                <select name="size" class="form-select size-select">
                                                    @foreach($sizeOptions as $option)
                                                        <option value="{{ $option }}" @selected($option === ($product->coffee_size ?: 'Medium'))>
                                                            {{ $option }}
                                                            @php($sizeColumn = strtolower($option) . '_price')
                                                            - ${{ number_format($product->{$sizeColumn} ?? $product->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if($isCoffeeCategory($product->category))
                                                <div class="input-group mt-2">
                                                    <span class="input-group-text">Sugar</span>
                                                    <select name="sugar" class="form-select">
                                                        @foreach($sugarOptions as $option)
                                                            <option value="{{ $option }}" @selected($option === ($product->sugar ?: '50%'))>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <button type="submit" class="btn btn-primary w-100 mt-2">
                                                <i class="bi bi-cart-plus me-1"></i> Add Cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sizeAdjustments = {
                Small: 'smallPrice',
                Medium: 'mediumPrice',
                Large: 'largePrice'
            };

            document.querySelectorAll('.cute-product-card').forEach(function (card) {
                const priceEl = card.querySelector('.product-price');
                const sizeSelect = card.querySelector('.size-select');

                if (!priceEl || !sizeSelect) {
                    return;
                }

                function updatePrice() {
                    const selectedSize = sizeSelect.value || 'Medium';
                    const priceKey = sizeAdjustments[selectedSize] || 'mediumPrice';
                    const nextPrice = Math.max(0.01, Number(priceEl.dataset[priceKey] || priceEl.dataset.basePrice || 0));
                    priceEl.textContent = '$' + nextPrice.toFixed(2);
                }

                sizeSelect.addEventListener('change', updatePrice);
                updatePrice();
            });
        });
    </script>
@endsection
