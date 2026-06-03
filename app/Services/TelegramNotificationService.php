<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotificationService
{
    public function sendPaymentSuccess(Payment $payment): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        try {
            $response = Http::timeout(8)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $this->paymentSuccessMessage($payment),
                    'disable_web_page_preview' => true,
                ]);

            if ($response->failed()) {
                Log::warning('Telegram payment notification failed.', [
                    'payment_id' => $payment->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function paymentSuccessMessage(Payment $payment): string
    {
        $payment->loadMissing('order.items.product');

        $order = $payment->order;
        $lines = [
            'Payment successful',
            'Order: '.($order?->display_order_label ?? '#'.$payment->order_id),
            'Amount: $'.number_format((float) $payment->amount, 2),
            'Method: '.strtoupper($payment->payment_method ?? $payment->provider ?? 'payment'),
            'Transaction: '.($payment->transaction_id ?: 'Not recorded'),
            'Paid at: '.now()->format('Y-m-d H:i:s'),
        ];

        if ($order) {
            $lines[] = 'Receipt: '.route('pos.receipt', ['id' => $order->id]);

            if ($order->customer_name || $order->customer_phone) {
                $customer = trim(($order->customer_name ?: 'Walk-in Customer').' '.($order->customer_phone ?: ''));
                $lines[] = 'Customer: '.$customer;
            }

            if ($order->items->isNotEmpty()) {
                $lines[] = 'Items:';

                foreach ($order->items as $item) {
                    $productName = $item->product?->name ?? 'Product #'.$item->product_id;
                    $lines[] = '- '.$productName
                        .' x'.$item->quantity
                        .' - $'.number_format((float) $item->line_total, 2);
                }
            }
        }

        return implode("\n", $lines);
    }
}
