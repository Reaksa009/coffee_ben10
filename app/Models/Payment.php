<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'meta',
        'verification_status',
        'verification_error',
        'verified_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getVerificationStatus()
    {
        return [
            'status' => $this->verification_status,
            'error' => $this->verification_error,
            'verified_at' => $this->verified_at,
        ];
    }
}
