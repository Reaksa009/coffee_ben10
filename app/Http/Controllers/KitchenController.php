<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(): View
    {
        return view('kitchen.index');
    }

    public function pickupDisplay(): View
    {
        return view('kitchen.pickup');
    }

    public function activeOrders(): JsonResponse
    {
        $orders = Order::with('items.product')
            ->where('status', 'paid')
            ->whereIn('preparation_status', ['queued', 'preparing', 'ready'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'display_order_label' => $order->display_order_label,
                    'order_type' => $order->order_type,
                    'order_type_label' => $order->order_type_label,
                    'service_label' => $order->service_label,
                    'preparation_status' => $order->preparation_status,
                    'created_at' => $order->created_at->toIso8601String(),
                    'elapsed_minutes' => (int) $order->created_at->diffInMinutes(now()),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product->name ?? 'Unknown Drink',
                            'quantity' => $item->quantity,
                            'size' => $item->size,
                            'sugar' => $item->sugar,
                        ];
                    }),
                ];
            });

        return response()->json($orders);
    }

    public function pickupState(): JsonResponse
    {
        $preparing = Order::where('status', 'paid')
            ->where('preparation_status', 'preparing')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'daily_order_number'])
            ->map(fn($o) => $o->display_order_number);

        $ready = Order::where('status', 'paid')
            ->where('preparation_status', 'ready')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'daily_order_number'])
            ->map(fn($o) => $o->display_order_number);

        return response()->json([
            'preparing' => $preparing,
            'ready' => $ready,
        ]);
    }

    public function updateStatus(Order $order, Request $request): JsonResponse
    {
        $data = $request->validate([
            'preparation_status' => ['required', 'in:queued,preparing,ready,completed'],
        ]);

        $oldStatus = $order->preparation_status;
        $newStatus = $data['preparation_status'];

        $order->update([
            'preparation_status' => $newStatus,
        ]);

        ActivityLogger::log(
            'order.preparation',
            "Updated order {$order->display_order_label} preparation status from '{$oldStatus}' to '{$newStatus}'",
            $order,
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );

        return response()->json(['success' => true]);
    }
}
