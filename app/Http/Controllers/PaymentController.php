<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\KHQRService;
use App\Services\LoyaltyService;
use App\Services\PaymentVerificationService;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;
use KHQR\BakongKHQR;

class PaymentController extends Controller
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

    public function index(Request $request)
    {
        $payments = Payment::with('order')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('method'), fn ($query) => $query->where('payment_method', $request->query('method')))
            ->when($request->filled('verification'), fn ($query) => $query->where('verification_status', $request->query('verification')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => Payment::sum('amount'),
            'paid' => Payment::where('status', 'paid')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed_verifications' => Payment::where('verification_status', 'failed')->count(),
        ];

        $methods = Payment::query()
            ->select('payment_method')
            ->whereNotNull('payment_method')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        return view('payments.index', compact('payments', 'summary', 'methods'));
    }

    public function show(Payment $payment)
    {
        $payment->load('order.items.product');

        return view('payments.show', compact('payment'));
    }

    public function createKHQRPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        try {
            $resp = $this->khqr->createPaymentRequest($order);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Unable to generate KHQR payment.',
                'error' => $e->getMessage(),
            ], 422);
        }

        // create pending payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => $resp['provider'] ?? 'khqr',
            'payment_method' => 'khqr',
            'amount' => $order->total_amount,
            'status' => 'pending',
            'meta' => $resp,
        ]);

        return response()->json([
            'payment' => $payment,
            'qr' => $resp['qr_data'] ?? null,
            'qr_image_url' => $resp['qr_image_url'] ?? null,
            'raw_payload' => $resp['raw_payload'] ?? null,
            'payment_url' => $resp['payment_url'] ?? null,
            'khqr_email' => $resp['credential'] ?? null,
            'expires_at' => $resp['expires_at'] ?? null,
            'expires_in_seconds' => $resp['expires_in_seconds'] ?? null,
        ]);
    }

    public function checkKHQRPayment(Payment $payment)
    {
        if ($payment->status === 'paid') {
            // Run verification if not already verified
            if ($payment->verification_status !== 'verified') {
                $verifyResult = $this->verificationService->verify($payment);
                if (! $verifyResult['success']) {
                    return response()->json([
                        'status' => 'paid',
                        'verification_status' => 'failed',
                        'message' => 'Payment received but verification failed: '.$verifyResult['error'],
                        'error' => $verifyResult['error'],
                    ], 422);
                }

                $payment->refresh();
            }

            $loyaltyResult = $this->loyalty->awardForPaidOrder($payment->order);
            $this->sendTelegramAlertOnce($payment->fresh());

            return response()->json([
                'status' => 'paid',
                'verification_status' => $payment->verification_status,
                'message' => 'Payment successful and verified.',
                'verified_at' => $payment->verified_at,
                'loyalty_points_earned' => $loyaltyResult['points'],
                'receipt_url' => $this->receiptUrlFor($payment),
            ]);
        }

        $md5 = data_get($payment->meta, 'md5');
        $token = config('khqr.api_token');
        $usesKhqrLink = $this->usesKhqrLinkProvider($payment);

        if (! $md5 || (! $usesKhqrLink && ! $token)) {
            return response()->json([
                'status' => 'pending',
                'message' => 'Payment is still pending.',
            ]);
        }

        try {
            if ($usesKhqrLink) {
                $response = $this->khqr->checkPaymentStatus($md5);
            } else {
                $response = (new BakongKHQR($token))->checkTransactionByMD5($md5);
            }
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'pending',
                'message' => 'Payment is still pending.',
            ]);
        }

        if ($this->isPaidKhqrResponse($response)) {
            $payment->update([
                'status' => 'paid',
                'transaction_id' => $this->transactionIdFromResponse($response, $payment),
                'meta' => array_merge($payment->meta ?? [], [
                    'confirmed_at' => now()->toDateTimeString(),
                    $usesKhqrLink ? 'khqr_link_response' : 'bakong_response' => $response,
                ]),
            ]);

            // Run verification after payment is marked as paid
            $verifyResult = $this->verificationService->verify($payment);

            if (! $verifyResult['success']) {
                return response()->json([
                    'status' => 'paid',
                    'verification_status' => 'failed',
                    'message' => 'Payment received but verification failed: '.$verifyResult['error'],
                    'error' => $verifyResult['error'],
                ], 422);
            }

            $payment->refresh();

            $order = $payment->order;
            if ($order) {
                $order->status = 'paid';
                $order->save();
            }

            $loyaltyResult = $this->loyalty->awardForPaidOrder($order);
            $this->sendTelegramAlertOnce($payment->fresh());

            return response()->json([
                'status' => 'paid',
                'verification_status' => $payment->verification_status,
                'message' => 'Payment successful and verified.',
                'transaction_id' => $payment->transaction_id,
                'verified_at' => $payment->verified_at,
                'loyalty_points_earned' => $loyaltyResult['points'],
                'receipt_url' => $this->receiptUrlFor($payment),
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => $response['responseMessage'] ?? 'Payment is still pending.',
        ]);
    }

    private function usesKhqrLinkProvider(Payment $payment): bool
    {
        return $payment->provider === 'khqr_link'
            || data_get($payment->meta, 'provider') === 'khqr_link'
            || data_get($payment->meta, 'khqr_link_response') !== null;
    }

    private function isPaidKhqrResponse(array $response): bool
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

    private function transactionIdFromResponse(array $response, Payment $payment): string
    {
        return data_get($response, 'data.hash')
            ?? data_get($response, 'hash')
            ?? data_get($response, 'tran')
            ?? 'KHQR-'.$payment->id;
    }

    private function receiptUrlFor(Payment $payment): ?string
    {
        if (! $payment->order_id) {
            return null;
        }

        return route('pos.receipt', ['id' => $payment->order_id, 'print' => 1]);
    }

    private function sendTelegramAlertOnce(Payment $payment): void
    {
        $payment->loadMissing('order.items.product');

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

    public function verifyPayment(Payment $payment)
    {
        $result = $this->verificationService->verify($payment);

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    // Endpoint to confirm payment for the local/simulated provider.
    public function confirm(Request $request)
    {
        $orderId = $request->query('order');
        $order = Order::findOrFail($orderId);

        if (config('khqr.provider') === 'khqr_link') {
            return redirect()
                ->route('pos.receipt', ['id' => $order->id])
                ->with('error', 'Payment must be confirmed by KHQR Link verification.');
        }

        $payment = $order->payments()->latest()->first();
        if ($payment) {
            $payment->update(['status' => 'paid', 'transaction_id' => 'SIM-'.$payment->id, 'meta' => array_merge($payment->meta ?? [], ['confirmed_at' => now()->toDateTimeString()])]);
            $order->update(['status' => 'paid']);
            $this->loyalty->awardForPaidOrder($order->fresh());
            $this->sendTelegramAlertOnce($payment->fresh());
        }

        return redirect()
            ->route('pos.receipt', ['id' => $order->id])
            ->with('print_receipt', true)
            ->with('success', 'Payment successful.');
    }
}
