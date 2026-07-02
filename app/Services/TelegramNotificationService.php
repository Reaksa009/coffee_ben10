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
                    'parse_mode' => 'HTML',
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
            '<b>🎉 ការបង់ប្រាក់បានជោគជ័យ!</b>',
            '=============================',
            '<b>📦 ការបញ្ជាទិញ:</b> <code>'.($order?->display_order_label ?? '#'.$payment->order_id).'</code>',
            '<b>💵 ចំនួនទឹកប្រាក់:</b> <code>$'.number_format((float) $payment->amount, 2).'</code>',
            '<b>💳 វិធីសាស្ត្រទូទាត់:</b> <code>'.strtoupper($payment->payment_method ?? $payment->provider ?? 'payment').'</code>',
            '<b>🧾 លេខប្រតិបត្តិការ:</b> <code>'.($payment->transaction_id ?: 'មិនមានដានឡើយ').'</code>',
            '<b>⏰ កាលបរិច្ឆេទ:</b> <code>'.now()->format('Y-m-d H:i:s').'</code>',
            '=============================',
        ];

        if ($order) {
            $lines[] = '<b>🔗 វិក្កយបត្រ:</b> '.route('pos.receipt', ['id' => $order->id]);

            if ($order->customer_name || $order->customer_phone) {
                $customer = trim(($order->customer_name ?: 'អតិថិជនមកផ្ទាល់').' '.($order->customer_phone ?: ''));
                $lines[] = '<b>👤 អតិថិជន:</b> '.$customer;
            }

            if ($order->items->isNotEmpty()) {
                $lines[] = '<b>🛒 ទំនិញបញ្ជាទិញ:</b>';

                foreach ($order->items as $item) {
                    $productName = $item->product?->name ?? 'ផលិតផល #'.$item->product_id;
                    $lines[] = '• '.$productName
                        .' <b>x'.$item->quantity.'</b>'
                        .' - <code>$'.number_format((float) $item->line_total, 2).'</code>';
                }
            }
        }

        return implode("\n", $lines);
    }
}
