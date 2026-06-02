<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $attributes = [
        'points_balance' => 0,
        'total_points_earned' => 0,
        'total_points_redeemed' => 0,
        'total_spent' => 0,
        'visits' => 0,
    ];

    protected $fillable = [
        'name',
        'phone',
        'email',
        'points_balance',
        'total_points_earned',
        'total_points_redeemed',
        'total_spent',
        'visits',
        'last_order_at',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'total_points_earned' => 'integer',
        'total_points_redeemed' => 'integer',
        'total_spent' => 'float',
        'visits' => 'integer',
        'last_order_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function setPhoneAttribute($value): void
    {
        $phone = trim((string) $value);

        $this->attributes['phone'] = $phone === '' ? null : $phone;
    }

    public function setNameAttribute($value): void
    {
        $name = trim((string) $value);

        $this->attributes['name'] = $name === '' ? null : $name;
    }
}
