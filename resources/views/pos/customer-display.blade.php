<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Display - Coffee Ben10</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230f766e'/%3E%3Ctext x='32' y='39' font-family='Arial,sans-serif' font-size='22' font-weight='700' text-anchor='middle' fill='white'%3ECB%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --brand: #0f766e;
            --brand-dark: #115e59;
            --accent: #d97706;
            --ink: #111827;
            --muted: #64748b;
            --line: rgba(229, 218, 206, 0.4);
            --surface: #ffffff;
            --soft: #faf8f5;
            --surface-subtle: #fdfcfb;
            --shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        body {
            background: radial-gradient(circle at top left, #faf8f5, #f5efe6) !important;
            font-family: 'Quicksand', 'Nunito', sans-serif !important;
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        .display-logo-header {
            background: #ffffff;
            border-bottom: 2px solid rgba(229, 218, 206, 0.4);
            padding: 1.25rem 0;
            box-shadow: 0 4px 20px rgba(141, 91, 76, 0.03);
            z-index: 10;
        }

        .logo-mark {
            width: 46px;
            height: 46px;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            font-size: 1.4rem;
            box-shadow: 0 8px 20px rgba(15, 118, 110, .2);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.01em;
        }

        .customer-display-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.5rem;
        }

        .display-card {
            border: 2px solid rgba(229, 218, 206, 0.4);
            border-radius: 2rem;
            background: #ffffff;
            box-shadow: 0 15px 35px rgba(141, 91, 76, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .welcome-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 2rem;
            height: 100%;
            min-height: 500px;
            background: linear-gradient(135deg, #ffffff, #fdfbf7);
            position: relative;
        }

        .welcome-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .welcome-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            max-width: 480px;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .cafe-mascot {
            font-size: 6rem;
            animation: bounceMascot 3s ease-in-out infinite;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        @keyframes bounceMascot {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }

        .cart-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid rgba(229, 218, 206, 0.3);
            background-color: #fdfcfb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .cart-items-list {
            padding: 1.5rem;
            overflow-y: auto;
            max-height: 480px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .cart-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            border: 2px solid rgba(229, 218, 206, 0.2);
            border-radius: 1.5rem;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        .cart-item-row:hover {
            transform: translateX(5px);
            background: #fdfbf7;
            border-color: var(--brand);
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .item-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.15rem;
        }

        .item-options {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .item-option-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 0.75rem;
            background: #f1f5f9;
            color: #475569;
        }

        .item-qty-price {
            text-align: right;
        }

        .item-qty {
            font-weight: 700;
            color: #64748b;
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 800;
            color: var(--brand);
            font-size: 1.15rem;
        }

        .cart-totals {
            padding: 2rem;
            background: #fdfcfb;
            border-top: 2px solid rgba(229, 218, 206, 0.3);
            border-radius: 0 0 2rem 2rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .total-label {
            font-weight: 700;
            color: #64748b;
            font-size: 1.05rem;
        }

        .total-value {
            font-weight: 800;
            color: #1e293b;
            font-size: 1.15rem;
        }

        .grand-total {
            border-top: 2px dashed rgba(229, 218, 206, 0.5);
            margin-top: 1rem;
            padding-top: 1.25rem;
        }

        .grand-total .total-label {
            font-size: 1.3rem;
            color: #1e293b;
            font-weight: 800;
        }

        .grand-total .total-value {
            font-size: 2.1rem;
            color: var(--brand);
            font-weight: 800;
        }

        /* KHQR Ticket styling */
        .khqr-ticket {
            max-width: 360px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 1.5rem;
            border: 2px solid rgba(229, 218, 206, 0.4);
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(141, 91, 76, 0.05);
        }

        .khqr-header {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            padding: 1.25rem;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #ffcc00;
        }

        .khqr-logo-container {
            display: inline-flex;
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1rem;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 2px solid #ffffff;
        }

        .khqr-logo-kh {
            background: #ffffff;
            color: #d32f2f;
            padding: 0.1rem 0.5rem;
        }

        .khqr-logo-qr {
            background: #d32f2f;
            color: #ffffff;
            padding: 0.1rem 0.5rem;
        }

        .khqr-body {
            padding: 2rem;
            text-align: center;
            background: radial-gradient(circle at top, #fafafa, #ffffff);
        }

        .khqr-qr-frame {
            position: relative;
            display: inline-block;
            padding: 1rem;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 4px solid #d32f2f;
            box-shadow: 0 8px 20px rgba(211, 47, 47, 0.1);
            margin-bottom: 1.5rem;
        }

        .khqr-img {
            width: 200px;
            height: 200px;
            display: block;
        }

        .khqr-scan-line {
            position: absolute;
            top: 1rem;
            left: 1rem;
            width: calc(100% - 2rem);
            height: 4px;
            background: linear-gradient(to right, transparent, #ffcc00, transparent);
            animation: scanQRAnti 2s linear infinite;
        }

        @keyframes scanQRAnti {
            0% { top: 1rem; }
            50% { top: calc(100% - 1.25rem); }
            100% { top: 1rem; }
        }

        .khqr-amount {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .khqr-currency {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Success state celebration screen */
        .celebration-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 2rem;
            height: 100%;
            min-height: 500px;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
            border-radius: 2rem;
            position: relative;
            overflow: hidden;
            animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .celebration-icon {
            font-size: 5.5rem;
            color: #10b981;
            margin-bottom: 1.5rem;
            animation: pulseIcon 1.5s ease-in-out infinite alternate;
        }

        @keyframes pulseIcon {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }

        .celebration-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #064e3b;
            margin-bottom: 0.75rem;
        }

        .celebration-subtitle {
            font-size: 1.15rem;
            color: #047857;
            max-width: 440px;
            line-height: 1.5;
            margin-bottom: 2rem;
        }

        .celebration-summary {
            background: #ffffff;
            border: 2px solid rgba(16, 185, 129, 0.15);
            border-radius: 1.5rem;
            padding: 1.25rem 2rem;
            display: inline-flex;
            flex-direction: column;
            gap: 0.5rem;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.03);
        }

        .celebration-points {
            font-weight: 800;
            color: #059669;
            font-size: 1.25rem;
        }

        .display-footer {
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(229, 218, 206, 0.3);
            background: #ffffff;
            margin-top: auto;
        }

        @media (max-width: 991.98px) {
            .customer-display-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <!-- Logo Header (No Cashier Sidebar or Topbar) -->
    <header class="display-logo-header text-center">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <span class="logo-mark"><i class="bi bi-cup-hot"></i></span>
            <span class="logo-text">Coffee Ben10</span>
        </div>
    </header>

    <div class="container-fluid customer-display-container">
        <div class="row g-4 flex-grow-1">
            <!-- Left Side: Order details (Cart items or Receipt items) -->
            <div class="col-lg-7 d-flex flex-column">
                <div class="display-card flex-grow-1 d-flex flex-column">
                    <div class="cart-header">
                        <h2 class="cart-title">
                            <i class="bi bi-cart3 text-primary"></i>
                            Your Order
                        </h2>
                        <span id="order-status-badge" class="badge bg-secondary rounded-pill px-3 py-1 font-semibold">Active Cart</span>
                    </div>

                    <!-- Cart List Container -->
                    <div class="cart-items-list flex-grow-1" id="display-cart-items">
                        <!-- Items dynamically injected here -->
                    </div>

                    <!-- Totals Container -->
                    <div class="cart-totals">
                        <div class="total-row">
                            <span class="total-label">Subtotal</span>
                            <span class="total-value" id="display-subtotal">$0.00</span>
                        </div>
                        <div class="total-row" id="display-discount-row" style="display: none;">
                            <span class="total-label text-success">Discount</span>
                            <span class="total-value text-success" id="display-discount">-$0.00</span>
                        </div>
                        <div class="total-row grand-total">
                            <span class="total-label">Total to Pay</span>
                            <span class="total-value" id="display-grand-total">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Payment or Welcome Screen -->
            <div class="col-lg-5">
                <div class="display-card h-100" id="display-interaction-card">
                    <!-- Dynamic view states loaded by JS -->
                </div>
            </div>
        </div>
    </div>

    <footer class="display-footer">
        Copyright &copy; {{ date('Y') }} by Coffee Ben10. Powered by Bakong KHQR.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cashierId = "{{ $cashier->id }}";
            const stateUrl = `/customer-display/${cashierId}/state`;

            const itemsContainer = document.getElementById('display-cart-items');
            const subtotalText = document.getElementById('display-subtotal');
            const discountRow = document.getElementById('display-discount-row');
            const discountText = document.getElementById('display-discount');
            const grandTotalText = document.getElementById('display-grand-total');
            const statusBadge = document.getElementById('order-status-badge');
            const interactionCard = document.getElementById('display-interaction-card');

            let lastState = null;
            let successTimeout = null;

            // Audio for successful payment
            const successAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2013/2013-84.wav');
            successAudio.volume = 0.5;

            function renderState(state) {
                // If we are currently displaying a success message, let it persist
                if (lastState && lastState.order && lastState.order.status === 'pending' && state.order && state.order.status === 'paid') {
                    showSuccessScreen(state);
                    lastState = state;
                    return;
                }

                // If success screen is active and we shouldn't overwrite it, skip
                if (successTimeout !== null) {
                    lastState = state;
                    return;
                }

                lastState = state;

                // 1. Render items list
                let itemsHtml = '';
                const itemsList = state.order ? state.order.items : state.cart;

                if (itemsList.length === 0) {
                    itemsHtml = `
                        <div class="text-center text-muted my-auto py-5">
                            <i class="bi bi-cup-hot fs-1 opacity-25"></i>
                            <div class="mt-3 font-semibold text-muted">Your cart is empty.</div>
                            <div class="small">Items will show up as they are added by the cashier.</div>
                        </div>
                    `;
                } else {
                    itemsList.forEach((item, index) => {
                        const name = item.name;
                        const qty = item.quantity;
                        const size = item.size;
                        const sugar = item.sugar;
                        const price = item.price || (item.line_total / qty);
                        
                        let badges = '';
                        if (size) badges += `<span class="item-option-badge">${size}</span>`;
                        if (sugar) badges += `<span class="item-option-badge">${sugar} sugar</span>`;

                        itemsHtml += `
                            <div class="cart-item-row" style="animation: scaleIn 0.3s ease forwards; animation-delay: ${index * 50}ms;">
                                <div class="item-details">
                                    <div class="item-name">${name}</div>
                                    <div class="item-options">${badges}</div>
                                </div>
                                <div class="item-qty-price">
                                    <div class="item-qty">Qty: ${qty}</div>
                                    <div class="item-price">$${(price * qty).toFixed(2)}</div>
                                </div>
                            </div>
                        `;
                    });
                }
                itemsContainer.innerHTML = itemsHtml;

                // 2. Render totals
                const total = state.order ? state.order.total_amount : state.total;
                const discount = state.discount;
                const grandTotal = state.order ? state.order.total_amount : state.final_total;
                const subtotal = state.order ? (state.order.total_amount + discount) : state.total;

                subtotalText.innerText = `$${subtotal.toFixed(2)}`;
                if (discount > 0) {
                    discountRow.style.display = 'flex';
                    discountText.innerText = `-$${discount.toFixed(2)}`;
                } else {
                    discountRow.style.display = 'none';
                }
                grandTotalText.innerText = `$${grandTotal.toFixed(2)}`;

                // 3. Render Status badge
                if (state.order) {
                    if (state.order.status === 'paid') {
                        statusBadge.innerText = 'Paid 🎉';
                        statusBadge.className = 'badge bg-success rounded-pill px-3 py-1';
                    } else {
                        statusBadge.innerText = `Order: ${state.order.display_order_label}`;
                        statusBadge.className = 'badge bg-warning text-dark rounded-pill px-3 py-1';
                    }
                } else {
                    statusBadge.innerText = 'Active Cart';
                    statusBadge.className = 'badge bg-secondary rounded-pill px-3 py-1';
                }

                // 4. Render Right Panel Interaction State
                if (state.order && state.order.status === 'pending' && state.payment) {
                    renderPaymentScreen(state);
                } else if (state.order && state.order.status === 'paid') {
                    showSuccessScreen(state);
                } else {
                    renderWelcomeScreen();
                }
            }

            function renderWelcomeScreen() {
                interactionCard.innerHTML = `
                    <div class="welcome-section h-100">
                        <div class="cafe-mascot">☕✨</div>
                        <h1 class="welcome-title">Good Coffee, Better Vibes</h1>
                        <p class="welcome-subtitle">
                            Welcome to Coffee Ben10! Order at the counter and scanner to pay securely using Bakong KHQR.
                        </p>
                        <div class="d-flex gap-2">
                            <span class="badge text-bg-light border px-3 py-2 rounded-pill font-bold">🌸 Clean & Friendly</span>
                            <span class="badge text-bg-light border px-3 py-2 rounded-pill font-bold">⚡ KHQR Instant Pay</span>
                        </div>
                    </div>
                `;
            }

            function renderPaymentScreen(state) {
                const qrUrl = state.payment.qr_image_url 
                    ? state.payment.qr_image_url 
                    : (state.payment.qr_data ? 'https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent(state.payment.qr_data) + '&size=250x250' : null);
                const grandTotal = state.order.total_amount;

                interactionCard.innerHTML = `
                    <div class="p-4 p-sm-5 d-flex flex-column align-items-center justify-content-center h-100">
                        <div class="khqr-ticket">
                            <div class="khqr-header">
                                <div class="khqr-logo-container">
                                    <span class="khqr-logo-kh">KH</span>
                                    <span class="khqr-logo-qr">QR</span>
                                </div>
                                <span class="fw-bold small">SCAN TO PAY</span>
                            </div>
                            <div class="khqr-body">
                                <div class="khqr-qr-frame">
                                    <div class="khqr-scan-line"></div>
                                    ${qrUrl ? `<img src="${qrUrl}" class="khqr-img" alt="KHQR Payment QR">` : `
                                        <div class="d-flex flex-column align-items-center justify-content-center" style="width:200px; height:200px;">
                                            <div class="spinner-border text-danger mb-2" role="status"></div>
                                            <div class="small text-muted">Preparing QR...</div>
                                        </div>
                                    `}
                                </div>
                                <div class="khqr-amount">$${grandTotal.toFixed(2)}</div>
                                <div class="khqr-currency">US Dollars (USD)</div>
                                <div class="mt-3 small text-muted">
                                    <i class="bi bi-info-circle me-1"></i> Scan with Bakong or any Cambodian Banking App
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function showSuccessScreen(state) {
                if (successTimeout !== null) return;

                // Play success sound
                try {
                    successAudio.play();
                } catch (e) {
                    console.log('Audio autoplay blocked by browser config.');
                }

                interactionCard.innerHTML = `
                    <div class="celebration-screen h-100">
                        <div class="celebration-icon">🎉✨</div>
                        <h2 class="celebration-title">Thank You!</h2>
                        <p class="celebration-subtitle">
                            Payment of <strong>$${state.order ? state.order.total_amount.toFixed(2) : state.final_total.toFixed(2)}</strong> was received and verified successfully.
                        </p>
                        <div class="celebration-summary">
                            <div class="small text-muted text-uppercase font-bold">Order Confirmed</div>
                            <div class="font-bold fs-5">${state.order ? state.order.display_order_label : ''}</div>
                            <div class="celebration-points">Have a wonderful day! 🌸</div>
                        </div>
                    </div>
                `;

                // Lock success screen for 8 seconds, then return to standard loop
                successTimeout = setTimeout(() => {
                    successTimeout = null;
                    pollState();
                }, 8000);
            }

            function pollState() {
                fetch(stateUrl)
                    .then(response => response.json())
                    .then(data => {
                        renderState(data);
                    })
                    .catch(err => {
                        console.error("Error fetching customer display state: ", err);
                    });
            }

            // Start polling loop every 1.5 seconds
            setInterval(() => {
                if (successTimeout === null) {
                    pollState();
                }
            }, 1500);

            // Initial call
            pollState();
        });
    </script>
</body>
</html>
