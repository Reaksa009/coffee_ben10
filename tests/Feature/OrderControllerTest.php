<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_orders_can_be_filtered_by_today_yesterday_and_custom_day(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->travelTo(Carbon::parse('2026-06-05 10:00:00'));

        $this->createOrderForDay('2026-06-05', 1, 10.00);
        $this->createOrderForDay('2026-06-04', 1, 20.00);
        $this->createOrderForDay('2026-06-03', 1, 30.00);

        $this->actingAs($manager)
            ->get(route('orders.index', ['day' => 'today']))
            ->assertOk()
            ->assertSee('Today, Jun 05, 2026')
            ->assertSee('$10.00')
            ->assertDontSee('$20.00')
            ->assertDontSee('$30.00');

        $this->actingAs($manager)
            ->get(route('orders.index', ['day' => 'yesterday']))
            ->assertOk()
            ->assertSee('Yesterday, Jun 04, 2026')
            ->assertSee('$20.00')
            ->assertDontSee('$10.00')
            ->assertDontSee('$30.00');

        $this->actingAs($manager)
            ->get(route('orders.index', [
                'day' => 'custom',
                'date' => '2026-06-03',
            ]))
            ->assertOk()
            ->assertSee('Jun 03, 2026')
            ->assertSee('$30.00')
            ->assertDontSee('$10.00')
            ->assertDontSee('$20.00');

        $this->travelBack();
    }

    private function createOrderForDay(string $date, int $dailyOrderNumber, float $totalAmount): Order
    {
        $createdAt = Carbon::parse($date.' 09:00:00');

        $order = Order::create([
            'order_date' => $date,
            'daily_order_number' => $dailyOrderNumber,
            'subtotal_amount' => $totalAmount,
            'total_amount' => $totalAmount,
            'status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $order->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $order;
    }
}
