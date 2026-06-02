<?php

namespace App\Services;

use App\Models\Payment;
use Exception;

class PaymentVerificationService
{
    public function verify(Payment $payment): array
    {
        try {
            $order = $payment->order;
            if (!$order) {
                throw new Exception('Payment has no associated order');
            }

            // Verify payment status
            if ($payment->status !== 'paid') {
                throw new Exception('Payment status is not marked as paid');
            }

            // Verify amount matches order
            if ((float)$payment->amount !== (float)$order->total_amount) {
                throw new Exception(sprintf(
                    'Amount mismatch: payment=%s, order=%s',
                    $payment->amount,
                    $order->total_amount
                ));
            }

            // Verify transaction ID exists
            if (!$payment->transaction_id) {
                throw new Exception('No transaction ID recorded');
            }

            // Verify payment metadata exists
            if (!$payment->meta || empty($payment->meta)) {
                throw new Exception('No payment metadata available');
            }

            // Verify Bakong response if available
            $bakongResponse = $payment->meta['bakong_response'] ?? null;
            if ($bakongResponse) {
                if (($bakongResponse['responseCode'] ?? null) !== 0) {
                    throw new Exception(sprintf(
                        'Bakong verification failed: %s',
                        $bakongResponse['responseMessage'] ?? 'Unknown error'
                    ));
                }

                // Verify transaction hash
                if (!isset($bakongResponse['data']['hash'])) {
                    throw new Exception('No transaction hash in Bakong response');
                }
            }

            // Verify payment is not too old (within 24 hours)
            if ($payment->updated_at->diffInHours(now()) > 24) {
                throw new Exception('Payment is older than 24 hours');
            }

            // All verifications passed
            $payment->update([
                'verification_status' => 'verified',
                'verification_error' => null,
                'verified_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Payment verified successfully',
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
            ];
        } catch (Exception $e) {
            $payment->update([
                'verification_status' => 'failed',
                'verification_error' => $e->getMessage(),
                'verified_at' => now(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ];
        }
    }

    public function verifyAndComplete(Payment $payment): array
    {
        $verifyResult = $this->verify($payment);

        if (!$verifyResult['success']) {
            return $verifyResult;
        }

        // If verification passes, confirm order completion
        $payment->order()->update(['status' => 'completed']);

        return array_merge($verifyResult, [
            'order_status' => 'completed',
        ]);
    }
}
