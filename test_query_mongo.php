<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Manually override config to MongoDB
config(['database.default' => 'mongodb']);
config(['database.connections.mongodb' => [
    'driver' => 'mongodb',
    'dsn' => 'mongodb+srv://coffee_admin:StoreAdminPassword123!@coffe-ben10.uxx9vhy.mongodb.net/?retryWrites=true&w=majority',
    'database' => 'laravel',
]]);

$products = App\Models\Product::all();
echo "Found " . $products->count() . " products:\n";
foreach ($products as $p) {
    echo "- Name: " . $p->name . "\n";
    echo "  Attributes: " . json_encode($p->getAttributes()) . "\n";
    echo "  Stock: " . var_export($p->stock, true) . "\n";
    echo "  Category: " . $p->category_name . "\n";
}
