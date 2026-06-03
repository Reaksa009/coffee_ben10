<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'size', 'sugar', 'unit_price', 'line_total'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
