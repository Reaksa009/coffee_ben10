@extends('layouts.app')

@section('content')
    <style>
        /* ============================================
                               NEW MODERN DESIGN - Same functionality
                               ============================================ */

        :root {
            --primary-red: #262fdc;
            --primary-dark: #3389b8;
            --primary-soft: #fef2f2;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #6b7280;
            --gray-800: #1f2937;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f6 100%);
        }

        /* Modern Card Styles */
        .modern-card {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(226, 232, 240, 0.6);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .modern-card-header {
            padding: 1.25rem 1.5rem;
            background: white;
            border-bottom: 2px solid var(--gray-100);
        }

        .modern-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Page Header */
        .page-header-modern {
            background: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .receipt-title {
            font-size: 1.875rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-red));
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        .receipt-date {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Badge Styles */
        .badge-modern {
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-paid {
            background: #d1fae5;
            color: #059669;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Table Styles */
        .table-modern {
            width: 100%;
        }

        .table-modern th {
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .table-modern td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        /* Summary Card */
        .summary-card {
            background: linear-gradient(135deg, var(--primary-dark), #7f1d1d);
            color: white;
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.8;
        }

        .summary-amount {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0.5rem 0;
        }

        /* KHQR Button */
        .btn-khqr {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-red));
            color: white;
            border: none;
            border-radius: 3rem;
            padding: 0.875rem 1.5rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-khqr:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: white;
        }

        /* KHQR Ticket (same structure, modernized) */
        .khqr-ticket-modern {
            max-width: 360px;
            margin: 0 auto;
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .khqr-ticket-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-red));
            padding: 1rem 1.25rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .khqr-ticket-body-modern {
            padding: 1.5rem;
            text-align: center;
        }

        .khqr-frame-modern {
            display: inline-block;
            padding: 0.75rem;
            background: white;
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            margin: 0.5rem 0;
        }

        /* Empty State */
        .empty-state-modern {
            text-align: center;
            padding: 3rem;
            color: var(--gray-600);
        }

        /* Modal Modern */
        .modal-modern .modal-content {
            border-radius: 1.5rem;
            border: none;
            overflow: hidden;
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-red));
            color: white;
            border: none;
            padding: 1.25rem 1.5rem;
        }

        .modal-modern .btn-close-white {
            filter: brightness(0) invert(1);
        }

        .payment-success-card {
            border: 0;
            border-radius: .5rem;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .22);
            overflow: hidden;
        }

        .payment-success-accent {
            background: linear-gradient(90deg, #0f766e, #22c55e, #2563eb);
            height: 7px;
        }

        .payment-success-icon {
            align-items: center;
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: .5rem;
            color: #047857;
            display: inline-flex;
            height: 76px;
            justify-content: center;
            margin-bottom: 1.25rem;
            width: 76px;
        }

        .payment-success-kicker {
            color: #047857;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .06em;
            margin-bottom: .35rem;
            text-transform: uppercase;
        }

        .payment-success-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            display: grid;
            gap: .8rem;
            margin: 1.25rem 0;
            padding: 1rem;
            text-align: left;
        }

        .payment-success-summary-row {
            align-items: center;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .payment-success-summary-row span {
            color: var(--gray-600);
            font-size: .82rem;
        }

        .payment-success-summary-row strong {
            color: var(--gray-800);
            font-size: .95rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header-modern {
                padding: 1rem 1.25rem;
            }

            .receipt-title {
                font-size: 1.5rem;
            }

            .table-modern th,
            .table-modern td {
                padding: 0.75rem 1rem;
            }

            .summary-amount {
                font-size: 1.75rem;
            }
        }

        @media print {
            @page {
                margin: 12mm;
            }

            body {
                background: #fff !important;
            }

            .app-topbar,
            .sidebar,
            .offcanvas,
            .alert,
            .modal,
            .modal-backdrop,
            .no-print,
            #khqr-payment-box {
                display: none !important;
            }

            .container-fluid,
            .row {
                display: block !important;
            }

            main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .col-12,
            .col-md-10,
            .col-lg-8,
            .col-lg-4 {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }

            .page-header-modern,
            .modern-card,
            .summary-card {
                background: #fff !important;
                box-shadow: none !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0 !important;
                color: #111827 !important;
                page-break-inside: avoid;
            }

            .receipt-title {
                color: #111827 !important;
                background: none !important;
                -webkit-text-fill-color: #111827;
            }

            .table-modern th,
            .table-modern td {
                color: #111827 !important;
                border-color: #d1d5db !important;
            }
        }
    </style>

    @php
        $statusClass = match ($order->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'cancelled', 'failed' => 'danger',
            default => 'secondary',
        };

        $statusBadgeClass = match ($order->status) {
            'paid' => 'badge-paid',
            'pending' => 'badge-pending',
            'cancelled', 'failed' => 'badge-cancelled',
            default => '',
        };
        $subtotalAmount = $order->subtotal_amount ?? ($order->total_amount + $order->discount_amount);
        $promoDiscountAmount = $order->promo_discount_amount ?? ($order->promo ? $order->discount_amount : 0);
        $loyaltyDiscountAmount = $order->loyalty_discount_amount ?? 0;
    @endphp

    <!-- NEW PAGE HEADER DESIGN -->
    <div class="page-header-modern">
        <div>
            <h1 class="receipt-title">
                <i class="bi bi-receipt me-2"></i>
                Receipt {{ $order->display_order_label }}
            </h1>
            <div class="receipt-date">
                <i class="bi bi-calendar3 me-1"></i>
                Created {{ $order->created_at->format('M d, Y H:i') }}
            </div>
        </div>
        <div class="d-flex gap-3 no-print">
            <span class="badge-modern {{ $statusBadgeClass }}">
                <i
                    class="bi {{ $order->status === 'pending' ? 'bi-hourglass-split' : ($order->status === 'paid' ? 'bi-check-circle' : 'bi-x-circle') }} me-1"></i>
                {{ ucfirst($order->status) }}
            </span>
            @if($order->status === 'paid')
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Receipt
                </button>
            @endif
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to POS
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN - Order Items & Payments -->
        <div class="col-lg-8">
            <!-- Order Items Card (Modern) -->
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <div class="modern-card-title">
                        <i class="bi bi-bag-check fs-5" style="color: var(--primary-red);"></i>
                        Order Items
                        <span
                            class="badge bg-secondary bg-opacity-10 text-dark rounded-pill ms-2">{{ $order->items->count() }}
                            items</span>
                    </div>
                </div>
                @if($order->items->isEmpty())
                    <div class="empty-state-modern">
                        <i class="bi bi-bag-x fs-1 text-muted"></i>
                        <p class="mt-2">No item rows found for this order.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $item->product?->name ?? 'Product #' . $item->product_id }}
                                            @if($item->size)
                                                <div class="text-muted small">Size: {{ $item->size }}</div>
                                            @endif
                                            @if($item->sugar)
                                                <div class="text-muted small">Sugar: {{ $item->sugar }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Payments Card (Modern) -->
            <div class="modern-card">
                <div class="modern-card-header">
                    <div class="modern-card-title">
                        <i class="bi bi-credit-card-2-front fs-5"></i>
                        Payment Transactions
                    </div>
                </div>
                @if($order->payments->isEmpty())
                    <div class="empty-state-modern">
                        <i class="bi bi-credit-card fs-1 text-muted"></i>
                        <p class="mt-2">No payments recorded.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                    @php
                                        $paymentStatusClass = match ($payment->status) {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'cancelled', 'failed' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">
                                            @if(strtoupper($payment->provider ?? '-') === 'KHQR')
                                                <i class="bi bi-qr-code me-1" style="color: var(--primary-red);"></i>
                                            @endif
                                            {{ strtoupper($payment->provider ?? '-') }}
                                        </td>
                                        <td class="text-muted font-monospace small">{{ $payment->transaction_id ?? '-' }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td class="text-end">
                                            <span
                                                class="badge bg-{{ $paymentStatusClass }} bg-opacity-10 text-{{ $paymentStatusClass }} px-3 py-1 rounded-pill">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN - Summary & KHQR Payment -->
        <div class="col-lg-4">
            <!-- Summary Card (Gradient Modern) -->
            <div class="summary-card">
                <div class="summary-label">Final Total</div>
                <div class="summary-amount">${{ number_format($order->total_amount, 2) }}</div>
                <div class="d-flex justify-content-between small opacity-75 mt-3">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotalAmount, 2) }}</span>
                </div>
                @if($promoDiscountAmount > 0)
                    <div class="d-flex justify-content-between small mt-2">
                        <span>Promo @if($order->promo)({{ $order->promo->code }})@endif</span>
                        <span>-${{ number_format($promoDiscountAmount, 2) }}</span>
                    </div>
                @endif
                @if($loyaltyDiscountAmount > 0)
                    <div class="d-flex justify-content-between small mt-2">
                        <span>Loyalty {{ $order->loyalty_points_redeemed }} pts</span>
                        <span>-${{ number_format($loyaltyDiscountAmount, 2) }}</span>
                    </div>
                @endif
                @if($order->customer_name || $order->customer_phone)
                    <div
                        style="background: rgba(255, 255, 255, .14); padding: .75rem; border-radius: .5rem; margin: 1rem 0;">
                        <div class="small opacity-75">Customer</div>
                        <div class="fw-bold">{{ $order->customer_name ?: 'Walk-in Customer' }}</div>
                        <div class="small opacity-75">{{ $order->customer_phone }}</div>
                    </div>
                @endif
                @if($order->loyalty_points_earned)
                    <div
                        style="background: rgba(16, 185, 129, .16); padding: .65rem; border-radius: .5rem; margin-top: 1rem; text-align: center;">
                        <div class="small">Loyalty earned</div>
                        <div class="fw-bold">+{{ $order->loyalty_points_earned }} points</div>
                    </div>
                @endif
                <hr class="my-3 opacity-25">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small opacity-75">Order Status</span>
                    <span class="badge-modern {{ $statusBadgeClass }} bg-white bg-opacity-20">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <!-- KHQR Payment Section (Only when pending) -->
            @if($order->status === 'pending')
                <div class="modern-card" id="khqr-payment-box">
                    <div class="modern-card-header">
                        <div class="modern-card-title">
                            <i class="bi bi-qr-code-scan fs-5" style="color: var(--primary-red);"></i>
                            KHQR Instant Payment
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
                            <div class="bg-white rounded-circle p-2 shadow-sm">
                                <i class="bi bi-bank2 fs-4" style="color: var(--primary-red);"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Scan & Pay with Bakong</div>
                                <div class="small text-muted">Supported by all KHQR banks</div>
                            </div>
                        </div>
                        <button id="khqr-pay-btn" class="btn-khqr">
                            <i class="bi bi-qr-code-scan me-2"></i> Generate KHQR Code
                        </button>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Secure payment verification
                            </small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- KHQR MODAL (Same structure, modern styling) -->
    @if($order->status === 'pending')
        <div class="modal fade modal-modern" id="khqrPaymentModal" tabindex="-1" aria-labelledby="khqrPaymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold" id="khqrPaymentModalLabel">
                                <i class="bi bi-qr-code me-2"></i>KHQR Payment
                            </h5>
                            <div class="small opacity-75 mt-1">Order {{ $order->display_order_label }} -
                                ${{ number_format($order->total_amount, 2) }}</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="khqr-loader" class="text-center text-muted py-4" style="display:none">
                            <div class="spinner-border text-danger mb-3" role="status" aria-hidden="true"></div>
                            <div>Generating secure QR code...</div>
                        </div>
                        <div id="khqr-code"></div>
                        <div id="khqr-expiry" class="text-muted mt-3 small text-center"></div>
                        <p id="khqr-payment-link" class="mt-3 mb-0"></p>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="khqr-regenerate-btn" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-arrow-clockwise me-1"></i> Generate New
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- PAYMENT SUCCESS MODAL (Modern) -->
    <div class="modal fade payment-success-modal" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content payment-success-card">
                <div class="payment-success-accent"></div>
                <div class="modal-body text-center p-4 p-sm-5">
                    <div class="payment-success-icon">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                    <div class="payment-success-kicker">Payment complete</div>
                    <h4 class="mb-2 fw-bold" id="paymentSuccessModalLabel">Payment Successful</h4>
                    <p class="text-muted mb-0" id="paymentSuccessMessage">Payment successful.</p>

                    <div class="payment-success-summary">
                        <div class="payment-success-summary-row">
                            <span>Order</span>
                            <strong>{{ $order->display_order_label }}</strong>
                        </div>
                        <div class="payment-success-summary-row">
                            <span>Total paid</span>
                            <strong>${{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                        <div class="payment-success-summary-row">
                            <span>Status</span>
                            <strong>Ready for receipt</strong>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-center">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">
                            <i class="bi bi-check2 me-1"></i> Done
                        </button>
                        @if($order->status === 'paid')
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i> Print
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const shouldAutoPrintReceipt = @json((bool) session('print_receipt') || request()->boolean('print'));

        function showPaymentSuccess(message) {
            const messageEl = document.getElementById('paymentSuccessMessage');
            const modalEl = document.getElementById('paymentSuccessModal');

            if (messageEl) {
                messageEl.textContent = message || 'Payment successful.';
            }

            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showPaymentSuccess(@json(session('success')));
            });
        </script>
    @endif

    @if($order->status === 'paid')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!shouldAutoPrintReceipt) {
                    return;
                }

                setTimeout(function () {
                    window.print();
                }, 600);
            });
        </script>
    @endif

    @if($order->status === 'pending')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('khqr-pay-btn');
                const loader = document.getElementById('khqr-loader');
                const code = document.getElementById('khqr-code');
                const expiry = document.getElementById('khqr-expiry');
                const link = document.getElementById('khqr-payment-link');
                const modalEl = document.getElementById('khqrPaymentModal');
                const modal = modalEl && window.bootstrap ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
                const regenerateBtn = document.getElementById('khqr-regenerate-btn');
                const statusUrlTemplate = @json(route('payment.khqr.status', ['payment' => '__PAYMENT_ID__']));
                const khqrMerchantName = @json(config('khqr.account_name', config('app.name', 'POS')));
                const khqrOrderLabel = @json('Order ' . $order->display_order_label);
                const khqrAmountLabel = @json('$ ' . number_format($order->total_amount, 2));
                let expiryTimer = null;
                let statusTimer = null;

                function escapeHtml(value) {
                    return String(value).replace(/[&<>"']/g, function (char) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;',
                        }[char];
                    });
                }

                function clearExpiryTimer() {
                    if (expiryTimer) {
                        clearInterval(expiryTimer);
                        expiryTimer = null;
                    }
                }

                function clearStatusTimer() {
                    if (statusTimer) {
                        clearInterval(statusTimer);
                        statusTimer = null;
                    }
                }

                async function checkPaymentStatus(paymentId) {
                    const response = await fetch(statusUrlTemplate.replace('__PAYMENT_ID__', paymentId));
                    const data = await response.json();

                    if (data.status === 'paid') {
                        clearExpiryTimer();
                        clearStatusTimer();
                        const points = Number(data.loyalty_points_earned || 0);
                        const loyaltyMessage = points > 0 ? ' Earned ' + points + ' loyalty points.' : '';
                        showPaymentSuccess((data.message || 'Payment successful.') + loyaltyMessage);
                        setTimeout(function () {
                            window.location.href = data.receipt_url || @json(route('pos.receipt', ['id' => $order->id, 'print' => 1]));
                        }, 1400);
                    }
                }

                function startStatusPolling(paymentId) {
                    clearStatusTimer();

                    if (!paymentId) {
                        return;
                    }

                    checkPaymentStatus(paymentId).catch(console.error);
                    statusTimer = setInterval(function () {
                        checkPaymentStatus(paymentId).catch(console.error);
                    }, 5000);
                }

                function startExpiryTimer(expiresAt) {
                    clearExpiryTimer();

                    if (!expiresAt) {
                        expiry.textContent = '';
                        return;
                    }

                    const expiresAtMs = Date.parse(expiresAt);
                    const tick = function () {
                        const secondsLeft = Math.max(0, Math.ceil((expiresAtMs - Date.now()) / 1000));

                        if (secondsLeft <= 0) {
                            clearExpiryTimer();
                            clearStatusTimer();
                            code.innerHTML = '<div class="alert alert-warning">KHQR expired. Generate a new QR code.</div>';
                            expiry.textContent = '';
                            btn.disabled = false;
                            btn.textContent = 'Generate new KHQR';
                            if (regenerateBtn) {
                                regenerateBtn.disabled = false;
                            }
                            return;
                        }

                        expiry.innerHTML = '<span class="khqr-expiry-pill"><i class="bi bi-clock"></i> Expires in ' + secondsLeft + ' seconds</span>';
                    };

                    tick();
                    expiryTimer = setInterval(tick, 1000);
                }

                async function generateKHQR() {
                    clearExpiryTimer();
                    clearStatusTimer();
                    if (modal) {
                        modal.show();
                    }
                    btn.disabled = true;
                    btn.textContent = 'Generating KHQR...';
                    if (regenerateBtn) {
                        regenerateBtn.disabled = true;
                    }
                    loader.style.display = 'block';
                    code.innerHTML = '';
                    expiry.textContent = '';
                    link.innerHTML = '';

                    try {
                        const response = await fetch('{{ route('payment.khqr.create', $order->id) }}');
                        const data = await response.json();

                        loader.style.display = 'none';

                        if (!response.ok) {
                            throw new Error(data.error || data.message || 'Failed to generate KHQR payment.');
                        }

                        if (data.qr_image_url || data.qr) {
                            const qrSrc = data.qr_image_url
                                ? data.qr_image_url
                                : 'https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent(data.qr) + '&size=250x250';
                            code.innerHTML = [
                                '<div class="khqr-ticket-modern">',
                                '<div class="khqr-ticket-header">',
                                '<div class="fw-bold"><i class="bi bi-qr-code me-1"></i> KHQR</div>',
                                '<div class="fw-bold">' + escapeHtml(khqrAmountLabel) + '</div>',
                                '</div>',
                                '<div class="khqr-ticket-body-modern">',
                                '<div class="text-uppercase small text-muted fw-bold">' + escapeHtml(khqrMerchantName) + '</div>',
                                '<div class="fw-bold mb-3">' + escapeHtml(khqrOrderLabel) + '</div>',
                                '<div class="khqr-frame-modern">',
                                '<img src="' + qrSrc + '" alt="KHQR code" style="width:200px;height:auto;">',
                                '</div>',
                                '<div class="text-muted small mt-3">Scan with Bakong or any KHQR supported bank app</div>',
                                '</div>',
                                '</div>'
                            ].join('');

                            if (data.qr) {
                                const qrText = document.createElement('pre');
                                qrText.className = 'khqr-payload mt-3 text-start bg-light border rounded p-2 overflow-auto';
                                qrText.textContent = data.qr;
                                const details = document.createElement('details');
                                details.className = 'mt-3';
                                const summary = document.createElement('summary');
                                summary.className = 'small text-muted';
                                summary.textContent = 'Show KHQR payload';
                                details.appendChild(summary);
                                details.appendChild(qrText);
                                code.appendChild(details);
                            }
                        } else {
                            code.innerHTML = '<div class="alert alert-warning">Unable to generate KHQR code.</div>';
                        }

                        if (data.payment_url) {
                            link.innerHTML = '<a href="' + data.payment_url + '" class="btn btn-success w-100 mt-2 rounded-pill" target="_blank"><i class="bi bi-check2-circle me-1"></i> Open confirm page</a>';
                        }

                        startExpiryTimer(data.expires_at);
                        startStatusPolling(data.payment && data.payment.id);
                        btn.disabled = false;
                        btn.textContent = 'Generate new KHQR';
                        if (regenerateBtn) {
                            regenerateBtn.disabled = false;
                        }
                    } catch (error) {
                        clearExpiryTimer();
                        clearStatusTimer();
                        loader.style.display = 'none';
                        expiry.textContent = '';
                        code.innerHTML = '<div class="alert alert-danger"></div>';
                        code.querySelector('.alert').textContent = error.message || 'Failed to generate KHQR payment. Please try again.';
                        btn.disabled = false;
                        btn.textContent = 'Generate KHQR';
                        if (regenerateBtn) {
                            regenerateBtn.disabled = false;
                        }
                        console.error(error);
                    }
                }

                btn.addEventListener('click', generateKHQR);
                if (regenerateBtn) {
                    regenerateBtn.addEventListener('click', generateKHQR);
                }
            });
        </script>
    @endif
@endsection
