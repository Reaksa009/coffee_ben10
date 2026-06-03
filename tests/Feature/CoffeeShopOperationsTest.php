<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductIngredient;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoffeeShopOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_render_coffee_shop_operations_pages(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        foreach ([
            route('inventory.index'),
            route('suppliers.index'),
            route('purchases.index'),
            route('purchases.create'),
            route('products.create'),
            route('reports.daily-close'),
            route('activity-logs.index'),
            route('backup.index'),
        ] as $url) {
            $this->actingAs($manager)->get($url)->assertOk();
        }
    }

    public function test_order_type_and_recipe_inventory_usage_are_recorded(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $product = Product::create([
            'name' => 'Latte',
            'description' => 'Milk coffee',
            'price' => 4.50,
            'stock' => 5,
        ]);
        $beans = InventoryItem::create([
            'name' => 'Coffee beans',
            'unit' => 'g',
            'quantity_on_hand' => 100,
            'low_stock_quantity' => 20,
            'unit_cost' => 0.03,
        ]);
        ProductIngredient::create([
            'product_id' => $product->id,
            'inventory_item_id' => $beans->id,
            'quantity' => 18,
            'unit' => 'g',
        ]);

        $this->actingAs($cashier)
            ->withSession([
                'cart' => [[
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 2,
                    'size' => 'Medium',
                ]],
            ])
            ->post(route('pos.place'), [
                'order_type' => 'dine_in',
                'table_number' => 'A4',
            ])
            ->assertRedirect()
            ->assertSessionMissing('error');

        $order = Order::first();

        $this->assertSame('dine_in', $order->order_type);
        $this->assertSame('A4', $order->table_number);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(64.0, $beans->fresh()->quantity_on_hand);
    }

    public function test_manager_can_create_product_without_recipe_rows(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->post(route('products.store'), [
                'name' => 'Iced Americano',
                'description' => 'Cold black coffee',
                'price' => 3.25,
                'stock' => 12,
                'ingredients' => [[
                    'inventory_item_id' => '',
                    'quantity' => '',
                    'unit' => '',
                ]],
            ])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Iced Americano',
            'stock' => 12,
        ]);
    }

    public function test_purchase_restock_updates_inventory_quantity_and_cost(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $supplier = Supplier::create(['name' => 'Bean Supplier']);
        $beans = InventoryItem::create([
            'name' => 'Arabica beans',
            'unit' => 'g',
            'quantity_on_hand' => 100,
            'low_stock_quantity' => 20,
            'unit_cost' => 0.02,
        ]);

        $this->actingAs($manager)
            ->post(route('purchases.store'), [
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-06-03',
                'items' => [[
                    'inventory_item_id' => $beans->id,
                    'quantity' => 100,
                    'unit_cost' => 0.04,
                ]],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $beans->refresh();

        $this->assertSame(200.0, $beans->quantity_on_hand);
        $this->assertSame(0.03, $beans->unit_cost);
        $this->assertDatabaseHas('purchase_items', [
            'inventory_item_id' => $beans->id,
            'quantity' => 100,
        ]);
    }

    public function test_manager_can_update_shop_settings_and_export_backup_csv(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        Product::create([
            'name' => 'Espresso',
            'description' => 'Fresh espresso',
            'price' => 3.50,
            'stock' => 10,
        ]);

        $this->actingAs($manager)
            ->put(route('shop-settings.update'), [
                'shop_name' => 'Coffee Test Shop',
                'address' => 'Phnom Penh',
                'phone' => '012345678',
                'receipt_footer' => 'Thank you',
                'currency' => 'USD',
                'receipt_width_mm' => 58,
                'tax_rate' => 0,
                'service_charge_rate' => 0,
            ])
            ->assertRedirect(route('shop-settings.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('shop_settings', [
            'shop_name' => 'Coffee Test Shop',
            'receipt_width_mm' => 58,
        ]);

        $this->actingAs($manager)
            ->get(route('backup.export', 'products'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('Espresso');
    }
}
