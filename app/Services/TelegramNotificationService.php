<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\InventoryItem;
use App\Models\CashierShift;
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

    public function sendLowStockAlert(InventoryItem $item): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        $lines = [
            '<b>⚠️ ការព្រមាន៖ គ្រឿងផ្សំជិតអស់ពីស្តុក! (Low Stock Alert)</b>',
            '=============================',
            '<b>📋 គ្រឿងផ្សំ:</b> <code>' . $item->name . '</code>',
            '<b>📉 ចំនួននៅសល់:</b> <code>' . number_format($item->quantity_on_hand, 3) . ' ' . $item->unit . '</code>',
            '<b>🚨 កម្រិតកំណត់ទាប:</b> <code>' . number_format($item->low_stock_quantity, 3) . ' ' . $item->unit . '</code>',
            '<b>⏰ កាលបរិច្ឆេទ:</b> <code>' . now()->format('Y-m-d H:i:s') . '</code>',
            '=============================',
        ];

        try {
            $response = Http::timeout(8)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => implode("\n", $lines),
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            return $response->successful();
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }

    public function sendShiftCloseSummary(CashierShift $shift): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        $lines = [
            '<b>🏁 ការបិទវេនអ្នកគិតលុយ (Shift Closed)</b>',
            '=============================',
            '<b>👤 បុគ្គលិក:</b> <code>' . ($shift->user?->name ?? 'Unknown') . '</code>',
            '<b>⏰ ម៉ោងបើក:</b> <code>' . ($shift->opened_at?->format('Y-m-d H:i:s') ?? '-') . '</code>',
            '<b>⏰ ម៉ោងបិទ:</b> <code>' . ($shift->closed_at?->format('Y-m-d H:i:s') ?? '-') . '</code>',
            '-----------------------------',
            '<b>💵 ទឹកប្រាក់បើកថត (Opening):</b> <code>$' . number_format($shift->opening_cash, 2) . '</code>',
            '<b>💵 ការលក់ជាសាច់ប្រាក់ (Cash Sales):</b> <code>$' . number_format($shift->cash_sales_amount, 2) . '</code>',
            '<b>💵 ទឹកប្រាក់រំពឹងទុក (Expected):</b> <code>$' . number_format($shift->expected_cash_amount, 2) . '</code>',
            '<b>💵 ទឹកប្រាក់រាប់ជាក់ស្តែង (Actual):</b> <code>$' . number_format($shift->closing_cash, 2) . '</code>',
            '<b>🚨 ទឹកប្រាក់ខុសគ្នា (Difference):</b> <code>$' . number_format($shift->cash_difference, 2) . '</code>',
            '=============================',
        ];

        if ($shift->notes) {
            $lines[] = '<b>📝 កំណត់សម្គាល់:</b> <i>' . e($shift->notes) . '</i>';
        }

        try {
            $response = Http::timeout(8)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => implode("\n", $lines),
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            return $response->successful();
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }
}
