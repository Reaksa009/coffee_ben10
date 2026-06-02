<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => 'pending',
        'discount_amount' => 0,
        'payment_method' => 'khqr',
    ];

    protected $fillable = ['user_id', 'total_amount', 'status', 'promo_id', 'discount_amount', 'payment_method'];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
}
