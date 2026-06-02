<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'coffee_size',
        'sugar',
        'price',
        'small_price',
        'medium_price',
        'large_price',
        'stock',
        'image',
    ];

    protected $casts = [
        'price' => 'float',
        'small_price' => 'float',
        'medium_price' => 'float',
        'large_price' => 'float',
        'stock' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
