<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Seed a category
$category = App\Models\Category::firstOrCreate(['name' => 'Coffee']);

// Seed a product
$product = App\Models\Product::create([
    'name' => 'Espresso',
    'category_id' => $category->id,
    'description' => 'Rich and bold espresso',
    'price' => 2.5,
    'stock' => 100,
]);

echo "Created Product:\n";
print_r($product->toArray());

$all = App\Models\Product::all();
echo "\nAll products in database:\n";
foreach ($all as $p) {
    echo "- Name: " . $p->name . ", Stock: " . $p->stock . ", Category: " . ($p->category?->name ?? 'None') . "\n";
}
