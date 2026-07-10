<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\KHQRService;
use App\Services\LoyaltyService;
use App\Services\PaymentVerificationService;
use App\Services\TelegramNotificationService;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use KHQR\BakongKHQR;

class CustomerDisplayController extends Controller
{
    protected $khqr;
    protected $verificationService;
    protected $loyalty;
    protected $telegram;

    public function __construct(
        KHQRService $khqr,
        PaymentVerificationService $verificationService,
        LoyaltyService $loyalty,
        TelegramNotificationService $telegram
    ) {
        $this->khqr = $khqr;
        $this->verificationService = $verificationService;
        $this->loyalty = $loyalty;
        $this->telegram = $telegram;
    }

    public function show($cashierId): View
    {
        $cashier = User::findOrFail($cashierId);
        return view('pos.customer-display', compact('cashier'));
    }

    public function state($cashierId): JsonResponse
    {
        $cashier = User::findOrFail($cashierId);
        $stateData = json_decode($cashier->customer_display_state, true) ?: [
            'cart' => [],
            'total' => 0,
            'discount' => 0,
            'final_total' => 0,
            'order_id' => null,
        ];

        $response = [
            'cart' => $stateData['cart'] ?? [],
            'total' => (float) ($stateData['total'] ?? 0),
            'discount' => (float) ($stateData['discount'] ?? 0),
            'final_total' => (float) ($stateData['final_total'] ?? 0),
            'order_id' => $stateData['order_id'] ?? null,
            'order' => null,
            'payment' => null,
        ];

        if ($response['order_id']) {
            $order = Order::with('items.product')->find($response['order_id']);
            if ($order) {
                $response['order'] = [
                    'id' => $order->id,
                    'display_order_label' => $order->display_order_label,
                    'status' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product->name ?? 'Product',
                            'quantity' => $item->quantity,
                            'size' => $item->size,
                            'sugar' => $item->sugar,
                            'line_total' => (float) $item->line_total,
                        ];
                    })->toArray(),
                ];

                // Check payment status or create new KHQR request if pending
                if ($order->status === 'pending') {
                    $payment = Payment::where('order_id', $order->id)
                        ->where('payment_method', 'khqr')
                        ->latest()
                        ->first();

                    if (!$payment) {
                        try {
                            $resp = $this->khqr->createPaymentRequest($order);
                            $payment = Payment::create([
                                'order_id' => $order->id,
                                'provider' => $resp['provider'] ?? 'khqr',
                                'payment_method' => 'khqr',
                                'amount' => $order->total_amount,
                                'status' => 'pending',
                                'meta' => $resp,
                            ]);
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    } else {
                        // Check if payment is verified or check Bakong API
                        $this->verifyPaymentIfNeeded($payment);
                    }

                    if ($payment) {
                        $payment->refresh();
                        $response['payment'] = [
                            'id' => $payment->id,
                            'status' => $payment->status,
                            'qr_image_url' => data_get($payment->meta, 'qr_image_url'),
                            'qr_data' => data_get($payment->meta, 'qr_data'),
                            'raw_payload' => data_get($payment->meta, 'raw_payload'),
                            'payment_url' => data_get($payment->meta, 'payment_url'),
                            'expires_in_seconds' => data_get($payment->meta, 'expires_in_seconds'),
                        ];
                        // If payment status changed to paid, reload order status
                        if ($payment->status === 'paid') {
                            $response['order']['status'] = 'paid';
                        }
                    }
                }
            }
        }

        return response()->json($response);
    }

    private function verifyPaymentIfNeeded(Payment $payment): void
    {
        if ($payment->status === 'paid') {
            return;
        }

        $md5 = data_get($payment->meta, 'md5');
        $token = config('khqr.api_token');
        $usesKhqrLink = $payment->provider === 'khqr_link'
            || data_get($payment->meta, 'provider') === 'khqr_link';

        if (!$md5 || (!$usesKhqrLink && !$token)) {
            return;
        }

        try {
            if ($usesKhqrLink) {
                $response = $this->khqr->checkPaymentStatus($md5);
            } else {
                $response = (new BakongKHQR($token))->checkTransactionByMD5($md5);
            }

            if ($this->isPaidResponse($response)) {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => data_get($response, 'data.hash')
                        ?? data_get($response, 'hash')
                        ?? data_get($response, 'tran')
                        ?? 'KHQR-'.$payment->id,
                    'meta' => array_merge($payment->meta ?? [], [
                        'confirmed_at' => now()->toDateTimeString(),
                        $usesKhqrLink ? 'khqr_link_response' : 'bakong_response' => $response,
                    ]),
                ]);

                // Run verification
                $verifyResult = $this->verificationService->verify($payment);
                if ($verifyResult['success']) {
                    $payment->refresh();
                    $order = $payment->order;
                    if ($order) {
                        $order->status = 'paid';
                        $order->save();

                        $this->loyalty->awardForPaidOrder($order);
                        $this->sendTelegramAlertOnce($payment);

                        ActivityLogger::log('payment.processed', 'Processed customer-facing KHQR payment for order ' . $order->display_order_label, $payment, [
                            'order_id' => $order->id,
                            'amount' => $payment->amount,
                            'payment_method' => 'khqr',
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function isPaidResponse(array $response): bool
    {
        if (($response['responseCode'] ?? null) !== 0) {
            return false;
        }

        if (array_key_exists('verified', $response)) {
            return ($response['verified'] ?? false) === true
                && strtoupper((string) ($response['status'] ?? '')) === 'COMPLETED';
        }

        return true;
    }

    private function sendTelegramAlertOnce(Payment $payment): void
    {
        $meta = $payment->meta ?? [];
        if (isset($meta['telegram_alerted_at'])) {
            return;
        }

        if (! $this->telegram->sendPaymentSuccess($payment)) {
            return;
        }

        $payment->forceFill([
            'meta' => array_merge($meta, [
                'telegram_alerted_at' => now()->toDateTimeString(),
            ]),
        ])->save();
    }
}
