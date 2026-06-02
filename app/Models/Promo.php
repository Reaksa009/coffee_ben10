<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'usage_limit',
        'times_used',
        'min_order_amount',
        'valid_from',
        'valid_until',
        'active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'active' => 'boolean',
    ];

    public function isValid()
    {
        if (!$this->active) return false;
        if ($this->usage_limit && $this->times_used >= $this->usage_limit) return false;
        if ($this->valid_from && now() < $this->valid_from) return false;
        if ($this->valid_until && now() > $this->valid_until->copy()->endOfDay()) return false;
        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) return 0;
        if ($this->min_order_amount && $amount < $this->min_order_amount) return 0;

        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }
        return $this->discount_value;
    }
}
