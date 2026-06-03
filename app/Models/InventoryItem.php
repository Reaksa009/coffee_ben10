<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'quantity_on_hand',
        'low_stock_quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'quantity_on_hand' => 'float',
        'low_stock_quantity' => 'float',
        'unit_cost' => 'float',
    ];

    public function productIngredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_on_hand <= $this->low_stock_quantity;
    }
}
