<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        [$dayFilter, $selectedDate] = $this->resolveDayFilter($request);

        $ordersQuery = Order::with('items.product', 'customer');

        if ($selectedDate) {
            $this->applyDayFilter($ordersQuery, $selectedDate);
        }

        if ($selectedDate) {
            $ordersQuery->orderByDesc('daily_order_number');
        }

        $orders = $ordersQuery
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $orders->getCollection()->each(function ($order) {
            $order->setAttribute('items_count', $order->items->count());
        });

        $selectedDateValue = ($selectedDate ?: now())->toDateString();
        $activeDayLabel = $this->activeDayLabel($dayFilter, $selectedDate);

        return view('orders.index', compact(
            'orders',
            'dayFilter',
            'selectedDateValue',
            'activeDayLabel'
        ));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'payments', 'customer')->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    private function resolveDayFilter(Request $request): array
    {
        $dayFilter = $request->query('day', 'all');

        if (! in_array($dayFilter, ['all', 'today', 'yesterday', 'custom'], true)) {
            $dayFilter = 'all';
        }

        if ($dayFilter === 'today') {
            return ['today', now()->startOfDay()];
        }

        if ($dayFilter === 'yesterday') {
            return ['yesterday', now()->subDay()->startOfDay()];
        }

        if ($request->filled('date')) {
            try {
                return ['custom', Carbon::parse($request->query('date'))->startOfDay()];
            } catch (\Throwable) {
                return ['all', null];
            }
        }

        return ['all', null];
    }

    private function applyDayFilter(Builder $query, Carbon $date): void
    {
        $dateString = $date->toDateString();

        $query->where(function (Builder $query) use ($dateString) {
            $query->where('order_date', $dateString)
                ->orWhere(function (Builder $query) use ($dateString) {
                    $query->whereNull('order_date')
                        ->whereDate('created_at', $dateString);
                });
        });
    }

    private function activeDayLabel(string $dayFilter, ?Carbon $selectedDate): string
    {
        if (! $selectedDate) {
            return 'All days';
        }

        return match ($dayFilter) {
            'today' => 'Today, '.$selectedDate->format('M d, Y'),
            'yesterday' => 'Yesterday, '.$selectedDate->format('M d, Y'),
            default => $selectedDate->format('M d, Y'),
        };
    }
}
