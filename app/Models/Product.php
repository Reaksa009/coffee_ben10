<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
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

    public static function categoryOptions(): Collection
    {
        return static::query()
            ->whereNotNull('category')
            ->get(['category'])
            ->pluck('category')
            ->map(fn ($category) => trim((string) $category))
            ->filter()
            ->unique(fn ($category) => strtolower($category))
            ->sortBy(fn ($category) => strtolower($category))
            ->values();
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
        $category = trim((string) $value);

        $this->attributes['category'] = $category === '' ? null : $category;
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
