<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryAudit extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'audit_date',
        'notes',
        'total_variance_cost',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'total_variance_cost' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryAuditItem::class, 'inventory_audit_id');
    }
}
