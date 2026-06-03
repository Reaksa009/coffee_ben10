<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class Category extends DatabaseModel
{
    use HasFactory;

    public const DEFAULT_NAMES = [
        'Coffee',
        'Tea',
        'Frappe',
        'Smoothie',
        'Bakery',
        'Food',
        'Other',
    ];

    protected $fillable = [
        'name',
    ];

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = trim((string) $value);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function options(): Collection
    {
        return static::query()
            ->orderBy('name')
            ->pluck('name');
    }

    public static function idsForName(?string $name): Collection
    {
        $name = trim((string) $name);

        if ($name === '') {
            return collect();
        }

        return static::query()
            ->where('name', $name)
            ->get()
            ->map(fn (self $category) => $category->getKey())
            ->values();
    }

    public static function findOrCreateByName(?string $name): ?self
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        $existing = static::query()
            ->get(['id', 'name'])
            ->first(fn (self $category) => strtolower($category->name) === strtolower($name));

        return $existing ?? static::create(['name' => $name]);
    }
}
