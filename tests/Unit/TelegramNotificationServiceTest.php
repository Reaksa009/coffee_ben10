<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Payment;
use App\Services\TelegramNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_skips_when_telegram_is_not_configured(): void
    {
        Http::fake();
        config([
            'services.telegram.bot_token' => null,
            'services.telegram.chat_id' => null,
        ]);

        $payment = new Payment([
            'amount' => 4.50,
            'payment_method' => 'khqr',
        ]);

        $this->assertFalse(app(TelegramNotificationService::class)->sendPaymentSuccess($payment));
        Http::assertNothingSent();
    }

    public function test_it_sends_a_payment_success_message(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);
        config([
            'services.telegram.bot_token' => 'telegram-token',
            'services.telegram.chat_id' => '123456',
        ]);

        $order = Order::create([
            'subtotal_amount' => 4.50,
            'total_amount' => 4.50,
            'status' => 'paid',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'khqr',
            'payment_method' => 'khqr',
            'transaction_id' => 'TX-123',
            'amount' => 4.50,
            'status' => 'paid',
            'meta' => ['confirmed_at' => now()->toDateTimeString()],
        ]);

        $this->assertTrue(app(TelegramNotificationService::class)->sendPaymentSuccess($payment));

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://api.telegram.org/bottelegram-token/sendMessage'
                && $request['chat_id'] === '123456'
                && str_contains($request['text'], 'Payment successful')
                && str_contains($request['text'], 'Order: #'.$order->id)
                && str_contains($request['text'], 'Transaction: TX-123');
        });
    }
}
