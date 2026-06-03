<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentMethodControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_payment_sends_telegram_alert_when_configured(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true]),
        ]);

        config([
            'services.telegram.bot_token' => 'telegram-token',
            'services.telegram.chat_id' => '123456',
        ]);

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 2.00,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('payment.process', ['order' => $order->id, 'method' => 'cash']))
            ->assertRedirect(route('pos.receipt', ['id' => $order->id]));

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://api.telegram.org/bottelegram-token/sendMessage'
                && $request['chat_id'] === '123456'
                && str_contains($request['text'], 'Payment successful')
                && str_contains($request['text'], 'Order: '.$order->fresh()->display_order_label);
        });

        $payment = $order->payments()->first();

        $this->assertNotNull($payment);
        $this->assertNotNull($payment->meta['telegram_alerted_at'] ?? null);
    }
}
