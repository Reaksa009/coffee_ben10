@extends('layouts.app')

@section('content')
    <style>
        .checkout-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .checkout-hero::before {
            display: none;
        }

        .checkout-hero>* {
            position: relative;
            z-index: 1;
        }

        .checkout-title {
            font-size: clamp(1.45rem, 2vw, 2rem);
            font-weight: 800;
            margin: 0;
        }

        .checkout-kicker {
            color: var(--accent);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            margin-bottom: .35rem;
            text-transform: uppercase;
        }

        .checkout-list {
            display: grid;
            gap: .85rem;
            padding: 1rem;
        }

        .checkout-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto auto;
            gap: .85rem;
            align-items: center;
            padding: .9rem;
            border: 1px solid var(--line);
            border-radius: .5rem;
            background: #fff;
        }

        .checkout-item-icon {
            width: 46px;
            height: 46px;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--brand);
            background: rgba(20, 184, 166, .12);
            font-size: 1.35rem;
        }

        .checkout-item-name {
            font-weight: 800;
            margin-bottom: .2rem;
        }

        .checkout-item-meta {
            color: var(--muted);
            font-size: .88rem;
        }

        .checkout-line-total {
            color: var(--brand);
            font-size: 1.05rem;
            font-weight: 800;
            text-align: right;
            white-space: nowrap;
        }

        .checkout-summary {
            position: sticky;
            top: 1rem;
            border: 0;
            overflow: hidden;
        }

        .checkout-summary-head {
            padding: 1.1rem;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), #2563eb);
        }

        .checkout-total {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: .65rem 0;
            border-bottom: 1px dashed #dbe4ef;
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .checkout-hero {
                align-items: stretch;
                flex-direction: column;
            }

            .checkout-item {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .checkout-line-total,
            .checkout-remove {
                grid-column: 1 / -1;
                text-align: left;
            }
        }
    </style>

    @php
        $itemCount = count($cart);
        $totalQuantity = collect($cart)->sum('quantity');
    @endphp

    <div class="checkout-hero">
        <div>
            <div class="checkout-kicker">Checkout</div>
            <h1 class="checkout-title">Review the order.</h1>
            <p class="page-subtitle mb-0">Confirm items, customer details, discounts, and payment flow.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="pos-chip"><i class="bi bi-cup-hot me-1"></i>{{ $totalQuantity }}
                cup{{ $totalQuantity === 1 ? '' : 's' }}</span>
            <span class="pos-chip"><i class="bi bi-basket2 me-1"></i>{{ $itemCount }}
                item{{ $itemCount === 1 ? '' : 's' }}</span>
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to POS
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="app-card overflow-hidden">
                <div class="app-card-header">
                    <div>
                        <h2 class="app-card-title">Order Items</h2>
                        <p class="text-muted small mb-0">A quick check before sending the order to payment.</p>
                    </div>
                    <span class="badge text-bg-light">{{ $itemCount }} items</span>
                </div>

                @if(empty($cart))
                    <div class="empty-state">
                        <div class="soft-icon bg-primary bg-opacity-10 text-primary mb-3 mx-auto">
                            <i class="bi bi-basket fs-4"></i>
                        </div>
                        Cart is empty.
                    </div>
                @else
                    <div class="checkout-list">
                        @foreach($cart as $index => $item)
                            <div class="checkout-item">
                                <div class="checkout-item-icon">
                                    <i class="bi bi-cup-hot"></i>
                                </div>
                                <div>
                                    <div class="checkout-item-name">{{ $item['name'] }}</div>
                                    <div class="checkout-item-meta">
                                        {{ $item['quantity'] }} x ${{ number_format($item['price'], 2) }}
                                        @if(!empty($item['size']))
                                            <span class="ms-1">Size: {{ $item['size'] }}</span>
                                        @endif
                                        @if(!empty($item['sugar']))
                                            <span class="ms-1">Sugar: {{ $item['sugar'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="checkout-line-total">
                                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                                <form method="POST" action="{{ route('pos.cart.item.destroy', $index) }}" class="checkout-remove">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <div class="col-lg-4">
            <section class="app-card checkout-summary">
                <div class="checkout-summary-head">
                    <div class="d-flex align-items-center gap-3">
                        <div class="soft-icon bg-white bg-opacity-25 text-white">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Order Total</div>
                            <div class="checkout-total">${{ number_format($total, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="summary-row">
                        <span class="text-muted">Items</span>
                        <strong>{{ $itemCount }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">Quantity</span>
                        <strong>{{ $totalQuantity }}</strong>
                    </div>
                    <div class="summary-row mb-3">
                        <span class="text-muted">Payment</span>
                        <strong>KHQR after order</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Order Type</label>
                        <select name="order_type" id="order-type" form="checkout-form" class="form-select mb-2" required>
                            <option value="takeaway" @selected(old('order_type', 'takeaway') === 'takeaway')>Takeaway</option>
                            <option value="dine_in" @selected(old('order_type') === 'dine_in')>Dine-in</option>
                            <option value="delivery" @selected(old('order_type') === 'delivery')>Delivery</option>
                        </select>
                        <div id="table-number-wrap" class="input-group mb-2" style="display:none;">
                            <span class="input-group-text"><i class="bi bi-grid-3x3-gap"></i></span>
                            <input type="text" name="table_number" id="table-number" form="checkout-form"
                                class="form-control" placeholder="Table number" value="{{ old('table_number') }}">
                        </div>
                        <div id="pickup-name-wrap" class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="pickup_name" id="pickup-name" form="checkout-form"
                                class="form-control" placeholder="Pickup or delivery name" value="{{ old('pickup_name') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Customer Loyalty</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="customer_phone" id="customer-phone" form="checkout-form"
                                class="form-control" placeholder="Customer phone"
                                value="{{ old('customer_phone') }}">
                        </div>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="customer_name" id="customer-name" form="checkout-form"
                                class="form-control" placeholder="Customer name"
                                value="{{ old('customer_name') }}">
                        </div>
                        <div id="loyalty-message" class="small text-muted">
                            Earn {{ $pointsPerDollar }} points per $1 paid. 1 point = ${{ number_format($pointValue, 2) }} discount.
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="redeem-points"
                                name="redeem_points" form="checkout-form" disabled>
                            <label class="form-check-label small" for="redeem-points" id="redeem-points-label">
                                Redeem available points
                            </label>
                        </div>
                    </div>

                    @if($discountAmount > 0)
                        <div class="summary-row"
                            style="background: rgba(16, 185, 129, .1); padding: .65rem; border-radius: .5rem; margin-bottom: 1rem;">
                            <span class="text-success">Discount Applied</span>
                            <strong class="text-success">-${{ number_format($discountAmount, 2) }}</strong>
                        </div>
                        <div class="summary-row"
                            style="padding-bottom: 1rem; padding-top: 1rem; border-bottom: 2px solid #dbe4ef;">
                            <span class="fw-bold">Final Total</span>
                            <strong class="text-primary"
                                style="font-size: 1.25rem;">${{ number_format($finalTotal, 2) }}</strong>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Promo Code</label>
                        <div class="input-group">
                            <input type="text" id="promo-code" class="form-control" placeholder="Enter code"
                                value="{{ $promoCode ?? '' }}">
                            <button class="btn btn-outline-secondary" type="button" id="apply-promo-btn">Apply</button>
                        </div>
                        <div id="promo-message"></div>
                    </div>

                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('pos.place') }}" id="checkout-form">
                            @csrf
                            <input type="hidden" name="promo_code" id="promo-code-hidden" value="{{ $promoCode ?? '' }}">
                            <button class="btn btn-primary w-100" @disabled(empty($cart))>
                                <i class="bi bi-check2-circle me-1"></i> Place Order
                            </button>
                        </form>

                        <form method="POST" action="{{ route('pos.cart.cancel') }}" class="confirm-cancel">
                            @csrf
                            <button class="btn btn-outline-danger w-100" @disabled(empty($cart))>
                                <i class="bi bi-x-circle me-1"></i> Cancel Cart
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('apply-promo-btn').addEventListener('click', async function () {
            const code = document.getElementById('promo-code').value.trim();
            const messageDiv = document.getElementById('promo-message');

            if (!code) {
                messageDiv.innerHTML = '<div class="alert alert-warning small mt-2 mb-0">Please enter a promo code</div>';
                return;
            }

            try {
                const response = await fetch('{{ route("pos.apply-promo") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        code: code,
                        total: {{ $total }}
                        })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('promo-code-hidden').value = code;
                    messageDiv.innerHTML = '<div class="alert alert-success small mt-2 mb-0">' + data.message + '</div>';
                    // Reload to show updated totals
                    setTimeout(() => location.reload(), 1000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger small mt-2 mb-0">' + data.message + '</div>';
                }
            } catch (error) {
                messageDiv.innerHTML = '<div class="alert alert-danger small mt-2 mb-0">Error applying promo code</div>';
            }
        });

        document.getElementById('promo-code').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                document.getElementById('apply-promo-btn').click();
            }
        });

        const customerPhoneInput = document.getElementById('customer-phone');
        const customerNameInput = document.getElementById('customer-name');
        const loyaltyMessage = document.getElementById('loyalty-message');
        const redeemCheckbox = document.getElementById('redeem-points');
        const redeemLabel = document.getElementById('redeem-points-label');
        let loyaltyLookupTimer = null;

        function resetLoyaltyRedeem(message) {
            redeemCheckbox.checked = false;
            redeemCheckbox.disabled = true;
            redeemLabel.textContent = 'Redeem available points';
            loyaltyMessage.className = 'small text-muted';
            loyaltyMessage.textContent = message || 'Enter a customer phone to check loyalty points.';
        }

        async function lookupCustomer() {
            const phone = customerPhoneInput.value.trim();

            if (!phone) {
                resetLoyaltyRedeem('Earn {{ $pointsPerDollar }} points per $1 paid. 1 point = ${{ number_format($pointValue, 2) }} discount.');
                return;
            }

            loyaltyMessage.className = 'small text-muted';
            loyaltyMessage.textContent = 'Checking loyalty points...';

            try {
                const url = new URL(@json(route('pos.customers.lookup')), window.location.origin);
                url.searchParams.set('phone', phone);
                url.searchParams.set('amount', @json($finalTotal));

                const response = await fetch(url.toString());
                const data = await response.json();

                if (data.found) {
                    if (data.name && !customerNameInput.value.trim()) {
                        customerNameInput.value = data.name;
                    }

                    loyaltyMessage.className = 'small text-success';
                    loyaltyMessage.textContent = data.name
                        ? data.name + ' has ' + data.points_balance + ' points.'
                        : 'Customer has ' + data.points_balance + ' points.';

                    if (Number(data.redeemable_discount) > 0) {
                        redeemCheckbox.disabled = false;
                        redeemLabel.textContent = 'Redeem ' + data.points_balance + ' points, up to $' + Number(data.redeemable_discount).toFixed(2);
                    } else {
                        resetLoyaltyRedeem('Customer found, but no redeemable points yet.');
                    }
                } else {
                    resetLoyaltyRedeem(data.message || 'New customer. Points will start after payment.');
                }
            } catch (error) {
                resetLoyaltyRedeem('Unable to check loyalty points right now.');
            }
        }

        customerPhoneInput.addEventListener('input', function () {
            clearTimeout(loyaltyLookupTimer);
            loyaltyLookupTimer = setTimeout(lookupCustomer, 450);
        });

        if (customerPhoneInput.value.trim()) {
            lookupCustomer();
        }

        const orderType = document.getElementById('order-type');
        const tableWrap = document.getElementById('table-number-wrap');
        const tableNumber = document.getElementById('table-number');
        const pickupWrap = document.getElementById('pickup-name-wrap');

        function syncOrderTypeFields() {
            const dineIn = orderType.value === 'dine_in';
            tableWrap.style.display = dineIn ? '' : 'none';
            pickupWrap.style.display = dineIn ? 'none' : '';
            tableNumber.required = dineIn;
        }

        orderType.addEventListener('change', syncOrderTypeFields);
        syncOrderTypeFields();
    </script>
@endsection
