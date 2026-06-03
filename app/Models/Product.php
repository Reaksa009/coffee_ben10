<?php

namespace App\Models;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\ProductIngredient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends DatabaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'category',
        'description',
        'coffee_size',
        'sugar',
        'price',
        'small_price',
        'medium_price',
        'large_price',
        'stock',
        'image',
        'image_data',
        'image_mime',
        'image_name',
    ];

    protected $casts = [
        'price' => 'float',
        'small_price' => 'float',
        'medium_price' => 'float',
        'large_price' => 'float',
        'stock' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recipeCost(): float
    {
        $ingredients = $this->relationLoaded('ingredients')
            ? $this->ingredients
            : $this->ingredients()->with('inventoryItem')->get();

        return round($ingredients->sum(function (ProductIngredient $ingredient) {
            return $ingredient->quantity * ($ingredient->inventoryItem?->unit_cost ?? 0);
        }), 4);
    }

    public function profitMargin(?float $price = null): ?float
    {
        $salePrice = $price ?? $this->medium_price ?? $this->price;

        if ($salePrice <= 0) {
            return null;
        }

        return round((($salePrice - $this->recipeCost()) / $salePrice) * 100, 2);
    }

    public function getCategoryNameAttribute(): ?string
    {
        if (! empty($this->attributes['category_id'])) {
            $category = $this->relationLoaded('category')
                ? $this->relations['category']
                : $this->getRelationValue('category');

            if ($category instanceof Category) {
                return $category->name;
            }
        }

        $legacyCategory = $this->attributes['category'] ?? null;

        if (is_string($legacyCategory)) {
            $legacyCategory = trim($legacyCategory);

            return $legacyCategory === '' ? null : $legacyCategory;
        }

        return null;
    }

    public static function categoryOptions(): Collection
    {
        return Category::options();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_data && $this->image_mime) {
            return route('products.image', ['product' => $this]);
        }

        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'data:') || filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function setPriceAttribute($value): void
    {
        $this->attributes['price'] = (float) $value;
    }

    public function setCategoryAttribute($value): void
    {
        $this->attributes['category_id'] = Category::findOrCreateByName($value)?->id;
    }

    public function setCategoryIdAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['category_id'] = null;

            return;
        }

        $this->attributes['category_id'] = $value;
    }

    public function setSmallPriceAttribute($value): void
    {
        $this->attributes['small_price'] = $this->nullableFloat($value);
    }

    public function setMediumPriceAttribute($value): void
    {
        $this->attributes['medium_price'] = $this->nullableFloat($value);
    }

    public function setLargePriceAttribute($value): void
    {
        $this->attributes['large_price'] = $this->nullableFloat($value);
    }

    public function setStockAttribute($value): void
    {
        $this->attributes['stock'] = max(0, (int) $value);
    }

    private function nullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
