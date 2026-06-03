<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Order extends DatabaseModel
{
    use HasFactory;

    protected $attributes = [
        'status' => 'pending',
        'discount_amount' => 0,
        'promo_discount_amount' => 0,
        'loyalty_discount_amount' => 0,
        'loyalty_points_redeemed' => 0,
        'loyalty_points_earned' => 0,
        'payment_method' => 'khqr',
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'order_date',
        'daily_order_number',
        'subtotal_amount',
        'total_amount',
        'status',
        'promo_id',
        'discount_amount',
        'promo_discount_amount',
        'loyalty_discount_amount',
        'loyalty_points_redeemed',
        'loyalty_points_earned',
        'loyalty_awarded_at',
        'payment_method',
    ];

    protected $casts = [
        'subtotal_amount' => 'float',
        'total_amount' => 'float',
        'discount_amount' => 'float',
        'promo_discount_amount' => 'float',
        'loyalty_discount_amount' => 'float',
        'loyalty_points_redeemed' => 'integer',
        'loyalty_points_earned' => 'integer',
        'loyalty_awarded_at' => 'datetime',
        'order_date' => 'date',
        'daily_order_number' => 'integer',
    ];

    public function getDisplayOrderNumberAttribute(): int
    {
        return (int) ($this->daily_order_number ?: $this->id);
    }

    public function getDisplayOrderLabelAttribute(): string
    {
        return '#'.$this->display_order_number;
    }

    public function setOrderDateAttribute($value): void
    {
        $this->attributes['order_date'] = $value ? Carbon::parse($value)->toDateString() : null;
    }

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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
