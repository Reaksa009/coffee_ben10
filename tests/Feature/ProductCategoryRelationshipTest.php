<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::create(['name' => 'Seasonal']);

        $product = Product::create([
            'name' => 'Iced Latte',
            'category_id' => $category->id,
            'description' => 'Cold milk coffee',
            'price' => 4.00,
            'stock' => 20,
        ]);

        $this->assertTrue($product->fresh()->category->is($category));
        $this->assertSame('Seasonal', $product->fresh()->category_name);
    }

    public function test_product_still_accepts_category_name_input(): void
    {
        $product = Product::create([
            'name' => 'Green Tea',
            'category' => 'Tea',
            'description' => 'Fresh tea',
            'price' => 3.00,
            'stock' => 10,
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Tea']);
        $this->assertSame('Tea', $product->fresh()->category_name);
    }

    public function test_category_name_reads_legacy_category_column(): void
    {
        $product = new Product();
        $product->setRawAttributes(['category' => 'Coffee'], true);

        $this->assertSame('Coffee', $product->category_name);
    }
}
