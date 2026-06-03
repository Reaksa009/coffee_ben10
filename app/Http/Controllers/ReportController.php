<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now()->startOfDay();
        $thisMonth = Carbon::now()->startOfMonth();

        $todayStats = [
            'orders' => Order::whereBetween('created_at', [$today, $today->copy()->endOfDay()])->count(),
            'revenue' => Order::where('status', 'paid')->whereBetween('created_at', [$today, $today->copy()->endOfDay()])->sum('total_amount'),
            'discounts' => Order::whereBetween('created_at', [$today, $today->copy()->endOfDay()])->sum('discount_amount'),
        ];

        $monthStats = [
            'orders' => Order::whereBetween('created_at', [$thisMonth, now()])->count(),
            'revenue' => Order::where('status', 'paid')->whereBetween('created_at', [$thisMonth, now()])->sum('total_amount'),
            'discounts' => Order::whereBetween('created_at', [$thisMonth, now()])->sum('discount_amount'),
        ];

        $paymentMethods = Payment::where('created_at', '>=', $today)
            ->get()
            ->groupBy(fn ($payment) => $payment->payment_method ?: 'unknown')
            ->map(function ($payments, $method) {
                return (object) [
                    'payment_method' => $method,
                    'count' => $payments->count(),
                    'total' => $payments->sum('amount'),
                ];
            })
            ->values();

        $overviewTrend = $this->dailySalesTrend($today->copy()->subDays(6), $today->copy()->endOfDay());
        $paymentChart = $this->paymentMethodChartData($paymentMethods);

        return view('reports.index', compact(
            'todayStats',
            'monthStats',
            'paymentMethods',
            'overviewTrend',
            'paymentChart'
        ));
    }

    public function sales(Request $request): View
    {
        $period = $request->query('period', 'daily'); // daily, weekly, monthly
        $startDate = $request->query('start_date', Carbon::now()->subDays(30));
        $endDate = $request->query('end_date', now());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $orders = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('items.product', 'promo')
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_discounts' => $orders->sum('discount_amount'),
            'average_order' => $orders->count() ? $orders->sum('total_amount') / $orders->count() : 0,
        ];

        // Group by period
        $chartData = $this->groupByPeriod($orders, $period);

        $paymentBreakdown = $orders
            ->groupBy(fn ($order) => $order->payment_method ?: 'unknown')
            ->map(function ($orders, $method) {
                return [
                    'label' => ucfirst($method),
                    'orders' => $orders->count(),
                    'revenue' => (float) $orders->sum('total_amount'),
                ];
            })
            ->values()
            ->all();

        return view('reports.sales', compact(
            'orders',
            'summary',
            'chartData',
            'paymentBreakdown',
            'period',
            'startDate',
            'endDate'
        ));
    }

    public function products(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::now()->subDays(30));
        $endDate = $request->query('end_date', now());

        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $products = Product::with(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->get()
            ->map(function ($product) {
                $items = $product->orderItems;
                return [
                    'product' => $product,
                    'quantity_sold' => $items->sum('quantity'),
                    'revenue' => $items->sum('line_total'),
                ];
            })
            ->filter(fn($item) => $item['quantity_sold'] > 0)
            ->sortByDesc('revenue');

        $productChartData = $products
            ->take(8)
            ->values()
            ->map(function ($item) {
                return [
                    'label' => $item['product']->name,
                    'category' => $item['product']->category ?? 'Menu item',
                    'quantity' => (int) $item['quantity_sold'],
                    'revenue' => (float) $item['revenue'],
                ];
            })
            ->all();

        return view('reports.products', compact('products', 'productChartData', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'sales');
        $startDate = Carbon::parse($request->query('start_date', Carbon::now()->subDays(30)))->startOfDay();
        $endDate = Carbon::parse($request->query('end_date', now()))->endOfDay();

        if ($type === 'sales') {
            $orders = Order::where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with('items.product')
                ->get();

            $csv = "Date,Order Number,Order ID,Amount,Discount,Payment Method,Status\n";
            foreach ($orders as $order) {
                $csv .= sprintf(
                    "%s,%s,%s,%.2f,%.2f,%s,%s\n",
                    $order->created_at->format('Y-m-d H:i'),
                    $order->display_order_number,
                    $order->id,
                    $order->total_amount,
                    $order->discount_amount,
                    $order->payment_method ?? 'KHQR',
                    $order->status
                );
            }
        } else {
            $products = Product::with(['orderItems' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])->get();

            $csv = "Product,Category,Quantity Sold,Revenue,Stock\n";
            foreach ($products as $product) {
                $qty = $product->orderItems->sum('quantity');
                $revenue = $product->orderItems->sum(function ($item) {
                    return $item->line_total;
                });

                if ($qty > 0) {
                    $csv .= sprintf(
                        "%s,%s,%d,%.2f,%d\n",
                        $product->name,
                        $product->category ?? 'N/A',
                        $qty,
                        $revenue,
                        $product->stock
                    );
                }
            }
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function dailySalesTrend(Carbon $startDate, Carbon $endDate): array
    {
        $points = [];
        $cursor = $startDate->copy()->startOfDay();
        $lastDay = $endDate->copy()->endOfDay();

        while ($cursor->lessThanOrEqualTo($lastDay)) {
            $orders = Order::where('status', 'paid')
                ->whereBetween('created_at', [$cursor->copy()->startOfDay(), $cursor->copy()->endOfDay()]);

            $points[] = [
                'label' => $cursor->format('M d'),
                'date' => $cursor->format('Y-m-d'),
                'orders' => (int) (clone $orders)->count(),
                'revenue' => (float) (clone $orders)->sum('total_amount'),
            ];

            $cursor->addDay();
        }

        return $points;
    }

    private function paymentMethodChartData($paymentMethods): array
    {
        return [
            'labels' => $paymentMethods->map(fn ($method) => ucfirst($method->payment_method))->values()->all(),
            'totals' => $paymentMethods->map(fn ($method) => (float) $method->total)->values()->all(),
            'counts' => $paymentMethods->map(fn ($method) => (int) $method->count)->values()->all(),
        ];
    }

    private function groupByPeriod($orders, $period): array
    {
        $data = [];

        foreach ($orders as $order) {
            if ($period === 'daily') {
                $key = $order->created_at->format('Y-m-d');
                $label = $order->created_at->format('M d');
            } elseif ($period === 'weekly') {
                $key = $order->created_at->format('o-W');
                $label = 'Week ' . $order->created_at->format('W, Y');
            } else {
                $key = $order->created_at->format('Y-m');
                $label = $order->created_at->format('M Y');
            }

            if (!isset($data[$key])) {
                $data[$key] = ['label' => $label, 'revenue' => 0, 'count' => 0];
            }

            $data[$key]['revenue'] += $order->total_amount;
            $data[$key]['count']++;
        }

        ksort($data);

        return array_values($data);
    }
}
