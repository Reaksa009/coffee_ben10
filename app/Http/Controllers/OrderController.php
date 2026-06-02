<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')->withCount('items')->orderByDesc('created_at')->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'payments')->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
