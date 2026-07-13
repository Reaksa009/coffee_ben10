<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryAuditItem extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'inventory_audit_id',
        'inventory_item_id',
        'theoretical_quantity',
        'physical_quantity',
        'variance_quantity',
        'unit_cost',
        'variance_cost',
    ];

    protected $casts = [
        'theoretical_quantity' => 'float',
        'physical_quantity' => 'float',
        'variance_quantity' => 'float',
        'unit_cost' => 'float',
        'variance_cost' => 'float',
    ];

    public function audit()
    {
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
