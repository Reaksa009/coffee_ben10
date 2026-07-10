<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDisplayControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([User::class, Product::class, Order::class, OrderItem::class, Payment::class] as $model) {
            $model::query()->delete();
        }
    }

    public function test_guest_can_access_customer_display_and_state(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $response = $this->get(route('customer-display.show', ['cashier' => $cashier->id]));
        $response->assertStatus(200);

        $responseState = $this->get(route('customer-display.state', ['cashier' => $cashier->id]));
        $responseState->assertStatus(200);
        $responseState->assertJsonStructure([
            'cart',
            'total',
            'discount',
            'final_total',
            'order_id',
            'order',
            'payment',
        ]);
    }

    public function test_cashier_cart_actions_update_customer_display_state(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $product = Product::create([
            'name' => 'Caramel Macchiato',
            'description' => 'Rich caramel flavor',
            'price' => 5.50,
            'stock' => 10,
        ]);

        // 1. Add item to cart
        $response = $this->actingAs($cashier)
            ->post(route('pos.add'), [
                'product_id' => $product->id,
                'quantity' => 2,
                'size' => 'Medium',
                'sugar' => '50%',
            ]);
        $response->assertRedirect();

        // Check if customer_display_state contains the added item
        $cashier->refresh();
        $this->assertNotNull($cashier->customer_display_state);
        
        $state = json_decode($cashier->customer_display_state, true);
        $this->assertCount(1, $state['cart']);
        $this->assertEquals(11.00, $state['total']);

        // Check state endpoint returns correct values
        $responseState = $this->get(route('customer-display.state', ['cashier' => $cashier->id]));
        $responseState->assertStatus(200);
        $responseState->assertJsonFragment(['name' => 'Caramel Macchiato', 'quantity' => 2]);

        // 2. Clear cart
        $responseCancel = $this->actingAs($cashier)
            ->post(route('pos.cart.cancel'));
        $responseCancel->assertRedirect();

        $cashier->refresh();
        $stateCancel = json_decode($cashier->customer_display_state, true);
        $this->assertEmpty($stateCancel['cart']);
        $this->assertEquals(0, $stateCancel['total']);
    }
}
