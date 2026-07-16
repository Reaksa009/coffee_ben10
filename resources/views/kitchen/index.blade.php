<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barista KDS - Coffee Ben10</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230f766e'/%3E%3Ctext x='32' y='39' font-family='Arial,sans-serif' font-size='22' font-weight='700' text-anchor='middle' fill='white'%3ECB%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Quicksand:wght@400;500;600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
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
            
            --queued-color: #d97706;
            --queued-bg: #fffbeb;
            --queued-border: #fde68a;
            
            --preparing-color: #2563eb;
            --preparing-bg: #eff6ff;
            --preparing-border: #bfdbfe;
            
            --ready-color: #16a34a;
            --ready-bg: #f0fdf4;
            --ready-border: #bbf7d0;
        }

        body {
            background-color: var(--soft);
            font-family: 'Battambang', 'Quicksand', 'Nunito', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        .kds-header {
            background-color: #ffffff;
            border-bottom: 2px solid var(--line);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 800;
            color: var(--brand);
            font-size: 1.4rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: #16a34a;
            background-color: #f0fdf4;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            border: 1px solid #bbf7d0;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background-color: #16a34a;
            border-radius: 999px;
            animation: pulseDot 1.5s infinite alternate;
        }

        @keyframes pulseDot {
            from { opacity: 0.3; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1.2); }
        }

        .kds-container {
            flex-grow: 1;
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
        }

        .kds-column {
            flex: 1;
            min-width: 320px;
            max-width: 450px;
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 1.5rem;
            border: 2px solid var(--line);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .column-header {
            padding: 1.25rem;
            font-weight: 800;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--line);
        }

        .column-queued .column-header { background-color: var(--queued-bg); color: var(--queued-color); border-bottom-color: var(--queued-border); }
        .column-preparing .column-header { background-color: var(--preparing-bg); color: var(--preparing-color); border-bottom-color: var(--preparing-border); }
        .column-ready .column-header { background-color: var(--ready-bg); color: var(--ready-color); border-bottom-color: var(--ready-border); }

        .orders-list {
            flex-grow: 1;
            padding: 1rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-height: calc(100vh - 180px);
        }

        .order-card {
            background-color: #ffffff;
            border: 2px solid var(--line);
            border-radius: 1.25rem;
            padding: 1.25rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.01);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: slideInCard 0.3s ease forwards;
        }

        @keyframes slideInCard {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        }

        .order-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .order-label {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--ink);
        }

        .order-type-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 0.75rem;
        }

        .type-dine-in { background-color: #fee2e2; color: #ef4444; }
        .type-takeaway { background-color: #e0f2fe; color: #0284c7; }
        .type-delivery { background-color: #f3e8ff; color: #7c3aed; }

        .order-timer {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .timer-warning {
            color: #ef4444 !important;
            animation: pulseTimer 1s infinite alternate;
        }

        @keyframes pulseTimer {
            from { opacity: 0.7; }
            to { opacity: 1; }
        }

        .order-items {
            list-style: none;
            padding: 0;
            margin: 0 0 1.25rem 0;
            border-top: 1px dashed var(--line);
            border-bottom: 1px dashed var(--line);
            padding: 0.75rem 0;
        }

        .order-item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.4rem 0;
        }

        .item-name-qty {
            font-weight: 700;
            color: var(--ink);
        }

        .item-qty-badge {
            font-size: 0.85rem;
            background-color: #f1f5f9;
            color: #475569;
            padding: 0.1rem 0.4rem;
            border-radius: 0.35rem;
            margin-right: 0.35rem;
            font-weight: 800;
        }

        .item-options {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 0.15rem;
            font-weight: 600;
            display: flex;
            gap: 0.35rem;
        }

        .option-badge {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
        }

        .order-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-kds {
            flex-grow: 1;
            font-weight: 700;
            border-radius: 0.75rem;
            padding: 0.55rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            transition: all 0.2s;
        }

        .btn-queued {
            background-color: var(--queued-color);
            border-color: var(--queued-color);
            color: white;
        }
        .btn-queued:hover {
            background-color: #b45309;
            border-color: #b45309;
        }

        .btn-preparing {
            background-color: var(--preparing-color);
            border-color: var(--preparing-color);
            color: white;
        }
        .btn-preparing:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-ready {
            background-color: var(--ready-color);
            border-color: var(--ready-color);
            color: white;
        }
        .btn-ready:hover {
            background-color: #15803d;
            border-color: #15803d;
        }

        .count-badge {
            background-color: rgba(0, 0, 0, 0.08);
            padding: 0.15rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <header class="kds-header">
        <a href="{{ route('dashboard') }}" class="header-logo">
            <span class="logo-icon"><i class="bi bi-cup-hot-fill"></i></span>
            <div>
                <span class="d-block" style="line-height: 1">Coffee Ben10</span>
                <span class="small text-muted font-bold" style="font-size: 0.75rem">BARISTA KDS PANEL</span>
            </div>
        </a>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('kitchen.pickup') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 font-semibold">
                <i class="bi bi-tv me-1"></i> Customer Pickup View
            </a>
            <div class="live-indicator">
                <span class="live-dot"></span>
                <span>LIVE SYNC</span>
            </div>
        </div>
    </header>

    <div class="kds-container">
        <!-- Queued Column -->
        <div class="kds-column column-queued">
            <div class="column-header">
                <span>Queued (New)</span>
                <span class="count-badge" id="queued-count">0</span>
            </div>
            <div class="orders-list" id="list-queued">
                <!-- Injected dynamically -->
            </div>
        </div>

        <!-- Preparing Column -->
        <div class="kds-column column-preparing">
            <div class="column-header">
                <span>Preparing</span>
                <span class="count-badge" id="preparing-count">0</span>
            </div>
            <div class="orders-list" id="list-preparing">
                <!-- Injected dynamically -->
            </div>
        </div>

        <!-- Ready Column -->
        <div class="kds-column column-ready">
            <div class="column-header">
                <span>Ready for Pickup</span>
                <span class="count-badge" id="ready-count">0</span>
            </div>
            <div class="orders-list" id="list-ready">
                <!-- Injected dynamically -->
            </div>
        </div>
    </div>

    <!-- Audio chime on new order -->
    <audio id="bell-chime" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-84.wav" preload="auto"></audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('bell-chime');
            let knownOrderIds = new Set();
            let isFirstLoad = true;

            function playChime() {
                try {
                    bell.play();
                } catch (e) {
                    console.log('Chime blocked by browser autoplay policy');
                }
            }

            function updateKds() {
                fetch('/kitchen/orders')
                    .then(r => r.json())
                    .then(orders => {
                        renderKds(orders);
                    })
                    .catch(e => console.error('KDS poll error:', e));
            }

            function renderKds(orders) {
                const lists = {
                    queued: document.getElementById('list-queued'),
                    preparing: document.getElementById('list-preparing'),
                    ready: document.getElementById('list-ready')
                };

                const counts = {
                    queued: document.getElementById('queued-count'),
                    preparing: document.getElementById('preparing-count'),
                    ready: document.getElementById('ready-count')
                };

                // Clear previous lists
                lists.queued.innerHTML = '';
                lists.preparing.innerHTML = '';
                lists.ready.innerHTML = '';

                let numbers = { queued: 0, preparing: 0, ready: 0 };
                let hasNewOrder = false;

                orders.forEach(order => {
                    const status = order.preparation_status || 'queued';
                    if (lists[status]) {
                        numbers[status]++;
                        
                        // Check if it's a new queued order
                        if (status === 'queued' && !knownOrderIds.has(order.id)) {
                            knownOrderIds.add(order.id);
                            if (!isFirstLoad) {
                                hasNewOrder = true;
                            }
                        }

                        // Add to set anyway
                        knownOrderIds.add(order.id);

                        const cardHtml = createOrderCard(order);
                        lists[status].insertAdjacentHTML('beforeend', cardHtml);
                    }
                });

                // Update column count badges
                counts.queued.innerText = numbers.queued;
                counts.preparing.innerText = numbers.preparing;
                counts.ready.innerText = numbers.ready;

                // Play sound if a new order arrived in the queue
                if (hasNewOrder) {
                    playChime();
                }

                isFirstLoad = false;

                // Set fallback messages if list empty
                Object.keys(lists).forEach(key => {
                    if (numbers[key] === 0) {
                        lists[key].innerHTML = `
                            <div class="text-center text-muted my-auto py-5 opacity-50">
                                <i class="bi ${key === 'queued' ? 'bi-inbox' : key === 'preparing' ? 'bi-hourglass-split' : 'bi-check-circle'} fs-1"></i>
                                <div class="mt-2 font-semibold">No orders here</div>
                            </div>
                        `;
                    }
                });
            }

            function createOrderCard(order) {
                const typeLabel = order.order_type_label;
                let serviceInfo = order.service_label ? `<span class="badge text-bg-light border font-bold">${order.service_label}</span>` : '';
                
                let typeClass = 'type-takeaway';
                if (order.order_type === 'dine_in') typeClass = 'type-dine-in';
                if (order.order_type === 'delivery') typeClass = 'type-delivery';

                // Timer calculation/warning
                const elapsed = Math.round(order.elapsed_minutes);
                const timerClass = elapsed >= 10 ? 'timer-warning' : '';
                const timeText = elapsed === 0 ? 'Just now' : `${elapsed}m ago`;

                // Items list
                let itemsHtml = '';
                order.items.forEach(item => {
                    let badges = '';
                    if (item.size) badges += `<span class="option-badge">${item.size}</span>`;
                    if (item.sugar) badges += `<span class="option-badge">${item.sugar} sugar</span>`;

                    itemsHtml += `
                        <li class="order-item-row">
                            <div>
                                <span class="item-qty-badge">${item.quantity}x</span>
                                <span class="item-name-qty">${item.name}</span>
                                <div class="item-options">${badges}</div>
                            </div>
                        </li>
                    `;
                });

                // Button mapping based on status
                let actionBtn = '';
                const status = order.preparation_status || 'queued';
                if (status === 'queued') {
                    actionBtn = `<button class="btn btn-kds btn-queued" onclick="changeStatus(${order.id}, 'preparing')"><i class="bi bi-play-fill"></i> Start Making</button>`;
                } else if (status === 'preparing') {
                    actionBtn = `<button class="btn btn-kds btn-preparing" onclick="changeStatus(${order.id}, 'ready')"><i class="bi bi-check2-circle"></i> Ready</button>`;
                } else if (status === 'ready') {
                    actionBtn = `<button class="btn btn-kds btn-ready" onclick="changeStatus(${order.id}, 'completed')"><i class="bi bi-box-arrow-right"></i> Serve / Close</button>`;
                }

                return `
                    <div class="order-card" id="order-card-${order.id}">
                        <div class="order-card-header">
                            <span class="order-label">${order.display_order_label}</span>
                            <span class="order-type-badge ${typeClass}">${typeLabel}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            ${serviceInfo}
                            <div class="order-timer ${timerClass}">
                                <i class="bi bi-clock"></i> ${timeText}
                            </div>
                        </div>
                        <ul class="order-items">
                            ${itemsHtml}
                        </ul>
                        <div class="order-actions">
                            ${actionBtn}
                        </div>
                    </div>
                `;
            }

            // Expose changeStatus to global scope
            window.changeStatus = function(orderId, newStatus) {
                // Instantly visual response before fetch completes
                const card = document.getElementById(`order-card-${orderId}`);
                if (card) {
                    card.style.opacity = '0.5';
                    card.style.transform = 'scale(0.95)';
                }

                fetch(`/kitchen/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ preparation_status: newStatus })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        updateKds();
                    }
                })
                .catch(e => {
                    console.error(e);
                    if (card) {
                        card.style.opacity = '1';
                        card.style.transform = 'none';
                    }
                });
            };

            // Start polling loop every 3 seconds
            updateKds();
            setInterval(updateKds, 3000);
        });
    </script>
</body>
</html>
