<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // create sample products
        $p1 = Product::create(['name' => 'Coffee Latte', 'description' => 'Nice latte', 'price' => 2.5, 'stock' => 20]);
        $p2 = Product::create(['name' => 'Cool Chill', 'description' => 'Chilled drink', 'price' => 0.1, 'stock' => 50]);

        $order = Order::create(['user_id' => null, 'total_amount' => 5.1, 'status' => 'completed']);

        OrderItem::create(['order_id' => $order->id, 'product_id' => $p1->id, 'quantity' => 2, 'unit_price' => 2.5, 'line_total' => 5.0]);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $p2->id, 'quantity' => 1, 'unit_price' => 0.1, 'line_total' => 0.1]);

        Payment::create(['order_id' => $order->id, 'provider' => 'cash', 'amount' => 5.1, 'status' => 'paid', 'meta' => []]);
    }
}
