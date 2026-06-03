<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_and_update_categories(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);

        $this->actingAs($manager)
            ->post(route('categories.store'), ['name' => ' Seasonal Drinks '])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $category = Category::where('name', 'Seasonal Drinks')->firstOrFail();

        $this->actingAs($manager)
            ->put(route('categories.update', $category), ['name' => 'Limited Menu'])
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Limited Menu',
        ]);
    }

    public function test_cashier_cannot_access_categories(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $this->actingAs($cashier)
            ->get(route('categories.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'You do not have permission to access that page.');
    }

    public function test_manager_cannot_delete_categories(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $category = Category::create(['name' => 'Manager Protected']);

        $this->actingAs($manager)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_category_without_deleting_products(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name' => 'To Delete']);
        $product = Product::create([
            'name' => 'Category Item',
            'category_id' => $category->id,
            'description' => 'A categorized item',
            'price' => 2.50,
            'stock' => 5,
        ]);

        $this->actingAs($admin)
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => null,
        ]);
    }
}
