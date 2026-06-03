<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class CashierShift extends DatabaseModel
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'cash_in',
        'cash_out',
        'cash_sales_amount',
        'expected_cash_amount',
        'closing_cash',
        'cash_difference',
        'status',
        'notes',
    ];

    protected $attributes = [
        'opening_cash' => 0,
        'cash_in' => 0,
        'cash_out' => 0,
        'cash_sales_amount' => 0,
        'expected_cash_amount' => 0,
        'cash_difference' => 0,
        'status' => self::STATUS_OPEN,
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'float',
        'cash_in' => 'float',
        'cash_out' => 'float',
        'cash_sales_amount' => 'float',
        'expected_cash_amount' => 'float',
        'closing_cash' => 'float',
        'cash_difference' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN && $this->closed_at === null;
    }

    public function cashSalesAmount(?Carbon $until = null): float
    {
        $until ??= now();

        return Payment::with('order')
            ->where('payment_method', 'cash')
            ->where('status', 'paid')
            ->where('created_at', '>=', $this->opened_at)
            ->where('created_at', '<=', $until)
            ->get()
            ->filter(fn (Payment $payment) => (string) $payment->order?->user_id === (string) $this->user_id)
            ->sum('amount');
    }

    public function expectedCash(?float $cashSalesAmount = null): float
    {
        $cashSalesAmount ??= $this->cashSalesAmount($this->closed_at);

        return round($this->opening_cash + $cashSalesAmount + $this->cash_in - $this->cash_out, 2);
    }
}
