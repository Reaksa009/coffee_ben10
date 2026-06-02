<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\KHQRService;
use KHQR\BakongKHQR;
use Tests\TestCase;

class KHQRServiceTest extends TestCase
{
    public function test_it_generates_a_valid_khqr_payload(): void
    {
        $order = new Order(['total_amount' => 5.10]);
        $order->id = 123;

        $response = app(KHQRService::class)->createPaymentRequest($order);

        $this->assertSame('khqr', $response['provider']);
        $this->assertNotEmpty($response['qr_data']);
        $this->assertTrue(BakongKHQR::verify($response['qr_data'])->isValid);
        $this->assertSame(config('khqr.bakong_account_id'), $response['credential']);
        $this->assertSame(config('khqr.bakong_account_id'), BakongKHQR::decode($response['qr_data'])->data['bakongAccountID']);
        $this->assertStringStartsWith('K123-', $response['raw_payload']['reference']);
        $this->assertLessThanOrEqual(25, strlen($response['raw_payload']['reference']));
        $this->assertNotEmpty($response['expires_at']);
        $this->assertStringContainsString('0113', BakongKHQR::decode($response['qr_data'])->data['timestamp']);
    }
}
