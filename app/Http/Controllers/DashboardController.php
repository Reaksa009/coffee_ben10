<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $totalProducts = Product::count();
        $recentOrders = Order::with('items.product')->orderBy('created_at', 'desc')->limit(8)->get();
        $lowStock = Product::where('stock', '<=', 5)->orderBy('stock')->limit(8)->get();
        $recentPayments = Payment::with('order')->orderBy('created_at', 'desc')->limit(8)->get();
        $salesChart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $orders = Order::where('status', 'paid')->whereDate('created_at', $date);

            return [
                'label' => $date->format('D'),
                'date' => $date->format('M d'),
                'orders' => (clone $orders)->count(),
                'revenue' => (float) (clone $orders)->sum('total_amount'),
            ];
        });
        $maxChartRevenue = max(1, $salesChart->max('revenue'));

        return view('dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'totalRevenue',
            'totalProducts',
            'recentOrders',
            'lowStock',
            'recentPayments',
            'salesChart',
            'maxChartRevenue'
        ));
    }
}
