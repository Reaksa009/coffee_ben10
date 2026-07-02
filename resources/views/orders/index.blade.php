@extends('layouts.app')

@section('content')
    <style>
        .order-filter-bar {
            align-items: center;
            background: #f8fafc;
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            padding: .9rem 1.1rem;
        }

        .order-filter-left {
            display: grid;
            gap: .55rem;
            min-width: 0;
        }

        .order-filter-label {
            color: var(--muted);
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .order-day-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        .order-day-tab {
            align-items: center;
            background: #fff;
            border: 1px solid #dbe3ee;
            border-radius: .5rem;
            color: #334155;
            display: inline-flex;
            font-size: .86rem;
            font-weight: 800;
            gap: .4rem;
            min-height: 36px;
            padding: .45rem .72rem;
            text-decoration: none;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
            white-space: nowrap;
        }

        .order-day-tab:hover {
            background: rgba(15, 118, 110, .08);
            border-color: rgba(15, 118, 110, .28);
            color: var(--brand-dark);
        }

        .order-day-tab.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .order-filter-summary {
            color: var(--muted);
            font-size: .86rem;
        }

        .order-filter-date {
            align-items: end;
            display: flex;
            flex-wrap: wrap;
            gap: .55rem;
            justify-content: flex-end;
        }

        .order-date-field {
            min-width: 170px;
        }

        @media (max-width: 767.98px) {
            .order-filter-bar {
                align-items: stretch;
                flex-direction: column;
            }

            .order-filter-date {
                justify-content: stretch;
            }

            .order-date-field,
            .order-filter-date .btn {
                width: 100%;
            }
        }
    </style>

    @php
        $statusClass = function ($status) {
            return match ($status) {
                'paid' => 'success',
                'pending' => 'warning',
                'cancelled', 'failed' => 'danger',
                default => 'secondary',
            };
        };

        $dayTabs = [
            ['key' => 'all', 'label' => 'All', 'icon' => 'bi-list-ul', 'url' => route('orders.index')],
            ['key' => 'today', 'label' => 'Today', 'icon' => 'bi-sun', 'url' => route('orders.index', ['day' => 'today'])],
            ['key' => 'yesterday', 'label' => 'Yesterday', 'icon' => 'bi-clock-history', 'url' => route('orders.index', ['day' => 'yesterday'])],
        ];
    @endphp

    <div class="page-head">
        <div>
            <h1 class="page-title">Orders</h1>
            <p class="page-subtitle">Review recent sales, payment status, and order details.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> New Sale
        </a>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Order History</h2>
            <span class="badge text-bg-light">{{ $orders->total() }} total</span>
        </div>

        <div class="order-filter-bar">
            <div class="order-filter-left">
                <div class="order-filter-label">Sort by day</div>
                <div class="order-day-tabs" role="group" aria-label="Order day filter">
                    @foreach($dayTabs as $tab)
                        <a href="{{ $tab['url'] }}"
                           class="order-day-tab {{ $dayFilter === $tab['key'] ? 'active' : '' }}"
                           @if($dayFilter === $tab['key']) aria-current="page" @endif>
                            <i class="bi {{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                        </a>
                    @endforeach
                </div>
                <div class="order-filter-summary">
                    {{ $activeDayLabel }}
                    <span class="mx-1">.</span>
                    {{ $orders->total() }} order{{ $orders->total() === 1 ? '' : 's' }}
                </div>
            </div>

            <form method="GET" action="{{ route('orders.index') }}" class="order-filter-date">
                <input type="hidden" name="day" value="custom">
                <div class="order-date-field">
                    <label for="order-date" class="form-label small text-muted mb-1">Date</label>
                    <input type="date"
                           id="order-date"
                           name="date"
                           value="{{ $selectedDateValue }}"
                           class="form-control form-control-sm">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-calendar-day me-1"></i> View
                </button>
            </form>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state">No orders yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->display_order_label }}</td>
                                <td>
                                    @if($order->customer)
                                        <div class="fw-semibold">{{ $order->customer->name ?: 'Walk-in Customer' }}</div>
                                        <div class="small text-muted">{{ $order->customer->phone }}</div>
                                    @else
                                        <span class="text-muted">Walk-in</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->order_type_label }}</div>
                                    @if($order->service_label)
                                        <div class="small text-muted">{{ $order->service_label }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->items_count }} items</div>
                                    <div class="small text-muted">
                                        @forelse($order->items->take(3) as $item)
                                            {{ $item->quantity }}x {{ $item->product?->name ?? 'Product #' . $item->product_id }}@if(! $loop->last), @endif
                                        @empty
                                            No item rows
                                        @endforelse
                                        @if($order->items_count > 3)
                                            +{{ $order->items_count - 3 }} more
                                        @endif
                                    </div>
                                </td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $statusClass($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        <a href="{{ route('pos.receipt', ['id' => $order->id, 'print' => 1]) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-printer me-1"></i> Print
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="d-flex justify-content-center mt-4">{{ $orders->links() }}</div>
@endsection
