<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product', 'customer')->orderByDesc('created_at')->paginate(15);
        $orders->getCollection()->each(function ($order) {
            $order->setAttribute('items_count', $order->items->count());
        });

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'payments', 'customer')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
