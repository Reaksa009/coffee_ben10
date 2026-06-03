<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopSetting extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'address',
        'phone',
        'receipt_footer',
        'currency',
        'receipt_width_mm',
        'tax_rate',
        'service_charge_rate',
    ];

    protected $casts = [
        'receipt_width_mm' => 'integer',
        'tax_rate' => 'float',
        'service_charge_rate' => 'float',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'shop_name' => config('app.name', 'Coffee Ben10'),
            'currency' => 'USD',
            'receipt_width_mm' => 80,
        ]);
    }
}
