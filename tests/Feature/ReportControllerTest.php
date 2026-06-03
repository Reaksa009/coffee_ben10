<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_pages_render_charts_with_sales_data(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->travelTo(Carbon::parse('2026-06-03 12:00:00'));

        $product = Product::create([
            'name' => 'Iced Latte',
            'category' => 'Coffee',
            'description' => 'Cold milk coffee',
            'price' => 4.00,
            'stock' => 20,
        ]);

        $order = Order::create([
            'user_id' => $manager->id,
            'order_date' => '2026-06-03',
            'daily_order_number' => 1,
            'subtotal_amount' => 8.00,
            'total_amount' => 8.00,
            'discount_amount' => 0,
            'status' => 'paid',
            'payment_method' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 4.00,
            'line_total' => 8.00,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'cash',
            'payment_method' => 'cash',
            'transaction_id' => 'CASH-TEST',
            'amount' => 8.00,
            'status' => 'paid',
        ]);

        $this->actingAs($manager)
            ->get(route('reports.index'))
            ->assertOk()
            ->assertSee('overviewTrendChart', false)
            ->assertSee('paymentMethodChart', false);

        $this->actingAs($manager)
            ->get(route('reports.sales', [
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-03',
                'period' => 'daily',
            ]))
            ->assertOk()
            ->assertSee('salesTrendChart', false)
            ->assertSee('salesPaymentChart', false);

        $this->actingAs($manager)
            ->get(route('reports.products', [
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-03',
            ]))
            ->assertOk()
            ->assertSee('productPerformanceChart', false)
            ->assertSee('Iced Latte');

        $this->travelBack();
    }
}
