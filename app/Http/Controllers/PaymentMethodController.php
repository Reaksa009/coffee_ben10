<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentMethodController extends Controller
{
    public function process(Request $request)
    {
        $orderId = $request->query('order');
        $method = $request->query('method', 'khqr');

        $order = Order::findOrFail($orderId);

        if ($method === 'cash') {
            return $this->processCash($order);
        } elseif ($method === 'card') {
            return $this->processCard($order);
        } elseif ($method === 'wallet') {
            return $this->processWallet($order);
        }

        return redirect()->back()->with('error', 'Invalid payment method');
    }

    private function processCash($order)
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'cash',
            'payment_method' => 'cash',
            'amount' => $order->total_amount,
            'status' => 'paid',
            'transaction_id' => 'CASH-' . $order->id . '-' . time(),
            'meta' => [
                'confirmed_at' => now()->toDateTimeString(),
                'payment_method' => 'cash',
                'notes' => 'Cash payment received',
            ],
        ]);

        $payment->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        $order->update(['status' => 'paid', 'payment_method' => 'cash']);

        return redirect()
            ->route('pos.receipt', ['id' => $order->id])
            ->with('success', 'Payment received. Order complete.');
    }

    private function processCard($order)
    {
        // Simulate card payment (in production, integrate with payment gateway)
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'card',
            'payment_method' => 'card',
            'amount' => $order->total_amount,
            'status' => 'paid',
            'transaction_id' => 'CARD-' . uniqid(),
            'meta' => [
                'confirmed_at' => now()->toDateTimeString(),
                'payment_method' => 'card',
                'last_four' => '****',
                'notes' => 'Card payment processed',
            ],
        ]);

        $payment->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        $order->update(['status' => 'paid', 'payment_method' => 'card']);

        return redirect()
            ->route('pos.receipt', ['id' => $order->id])
            ->with('success', 'Card payment processed successfully.');
    }

    private function processWallet($order)
    {
        // Simulate wallet payment (e-wallet, mobile money, etc.)
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'wallet',
            'payment_method' => 'wallet',
            'amount' => $order->total_amount,
            'status' => 'paid',
            'transaction_id' => 'WALLET-' . uniqid(),
            'meta' => [
                'confirmed_at' => now()->toDateTimeString(),
                'payment_method' => 'wallet',
                'wallet_type' => 'digital_wallet',
                'notes' => 'Digital wallet payment received',
            ],
        ]);

        $payment->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        $order->update(['status' => 'paid', 'payment_method' => 'wallet']);

        return redirect()
            ->route('pos.receipt', ['id' => $order->id])
            ->with('success', 'Wallet payment processed successfully.');
    }
}
