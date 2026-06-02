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

        return view('reports.index', compact('todayStats', 'monthStats', 'paymentMethods'));
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

        return view('reports.sales', compact('orders', 'summary', 'chartData', 'period', 'startDate', 'endDate'));
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

        return view('reports.products', compact('products', 'startDate', 'endDate'));
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

            $csv = "Date,Order ID,Amount,Discount,Payment Method,Status\n";
            foreach ($orders as $order) {
                $csv .= sprintf(
                    "%s,%s,%.2f,%.2f,%s,%s\n",
                    $order->created_at->format('Y-m-d H:i'),
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

    private function groupByPeriod($orders, $period)
    {
        $data = [];

        foreach ($orders as $order) {
            if ($period === 'daily') {
                $key = $order->created_at->format('Y-m-d');
            } elseif ($period === 'weekly') {
                $key = 'Week ' . $order->created_at->format('W Y');
            } else {
                $key = $order->created_at->format('Y-m');
            }

            if (!isset($data[$key])) {
                $data[$key] = ['revenue' => 0, 'count' => 0];
            }

            $data[$key]['revenue'] += $order->total_amount;
            $data[$key]['count']++;
        }

        return $data;
    }
}
