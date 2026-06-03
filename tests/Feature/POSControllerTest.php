<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class POSControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([User::class, Product::class, Order::class, OrderItem::class, Payment::class, Promo::class] as $model) {
            $model::query()->delete();
        }
    }

    public function test_place_order_reduces_product_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Espresso',
            'description' => 'Fresh espresso',
            'price' => 4.50,
            'stock' => 5,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [[
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 2,
                    'size' => 'Medium',
                ]],
            ])
            ->post(route('pos.place'));

        $response->assertRedirect();
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_place_order_rejects_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Latte',
            'description' => 'Creamy latte',
            'price' => 5.00,
            'stock' => 1,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'cart' => [[
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 2,
                    'size' => 'Medium',
                ]],
            ])
            ->post(route('pos.place'));

        $response->assertRedirect(route('pos.index'));
        $this->assertSame(1, $product->fresh()->stock);
        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
        ]);
    }

    public function test_daily_order_number_resets_each_day(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Americano',
            'description' => 'Fresh americano',
            'price' => 3.00,
            'stock' => 10,
        ]);

        $this->travelTo(Carbon::parse('2026-06-03 09:00:00'));
        $this->placeSingleItemOrder($user, $product);
        $this->assertDatabaseCount('orders', 1);

        $this->travelTo(Carbon::parse('2026-06-03 10:00:00'));
        $this->placeSingleItemOrder($user, $product);
        $this->assertDatabaseCount('orders', 2);

        $this->travelTo(Carbon::parse('2026-06-04 09:00:00'));
        $this->placeSingleItemOrder($user, $product);
        $this->assertDatabaseCount('orders', 3);

        $orders = Order::orderBy('id')->get();

        $this->assertSame('2026-06-03', $orders[0]->order_date->toDateString());
        $this->assertSame(1, $orders[0]->daily_order_number);
        $this->assertSame('2026-06-03', $orders[1]->order_date->toDateString());
        $this->assertSame(2, $orders[1]->daily_order_number);
        $this->assertSame('2026-06-04', $orders[2]->order_date->toDateString());
        $this->assertSame(1, $orders[2]->daily_order_number);
    }

    private function placeSingleItemOrder(User $user, Product $product): void
    {
        $this->actingAs($user)
            ->withSession([
                'cart' => [[
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                    'size' => 'Medium',
                ]],
            ])
            ->post(route('pos.place'))
            ->assertRedirect()
            ->assertSessionMissing('error');
    }
}
