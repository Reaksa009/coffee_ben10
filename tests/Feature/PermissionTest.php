<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_cannot_access_back_office_pages(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $response = $this->actingAs($cashier)->get(route('products.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'You do not have permission to access that page.');

        $this->actingAs($cashier)
            ->get(route('users.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_manager_can_access_products_but_cannot_delete_them(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $product = Product::create([
            'name' => 'Latte',
            'description' => 'Coffee with milk',
            'price' => 4.50,
            'stock' => 10,
        ]);
        $promo = Promo::create([
            'code' => 'MANAGER10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
        ]);

        $this->actingAs($manager)
            ->get(route('products.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($manager)
            ->delete(route('promos.destroy', $promo))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('promos', ['id' => $promo->id]);

        $this->actingAs($manager)
            ->get(route('users.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_delete_products_and_promos(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = Product::create([
            'name' => 'Mocha',
            'description' => 'Chocolate coffee',
            'price' => 5.00,
            'stock' => 8,
        ]);
        $promo = Promo::create([
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
        ]);

        $this->actingAs($admin)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $this->actingAs($admin)
            ->delete(route('promos.destroy', $promo))
            ->assertRedirect(route('promos.index'));

        $this->assertDatabaseMissing('promos', ['id' => $promo->id]);
    }

    public function test_admin_can_set_cashier_permission_and_change_password(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $staff = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee($staff->email);

        $this->actingAs($admin)
            ->patch(route('users.role.update', $staff), [
                'role' => User::ROLE_CASHIER,
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertSame(User::ROLE_CASHIER, $staff->fresh()->role);

        $this->actingAs($admin)
            ->patch(route('users.password.update', $staff), [
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-secure-password', $staff->fresh()->password));
    }
}
