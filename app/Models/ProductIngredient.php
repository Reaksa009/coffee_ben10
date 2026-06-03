<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductIngredient extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'inventory_item_id',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function getLineCostAttribute(): float
    {
        return round($this->quantity * ($this->inventoryItem?->unit_cost ?? 0), 4);
    }
}
