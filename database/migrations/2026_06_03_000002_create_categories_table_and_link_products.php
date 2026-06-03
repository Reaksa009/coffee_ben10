<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $defaultCategories = [
        'Coffee',
        'Tea',
        'Frappe',
        'Smoothie',
        'Bakery',
        'Food',
        'Other',
    ];

    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->nullOnDelete();
        });

        $this->seedCategories();
        $this->moveProductCategoryNamesToIds();

        if (Schema::hasColumn('products', 'category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('products', 'category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });
        }

        if (Schema::hasColumn('products', 'category_id') && Schema::hasTable('categories')) {
            $categories = DB::table('categories')->pluck('name', 'id');

            DB::table('products')
                ->whereNotNull('category_id')
                ->get(['id', 'category_id'])
                ->each(function ($product) use ($categories) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['category' => $categories[$product->category_id] ?? null]);
                });
        }

        if (Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }

        Schema::dropIfExists('categories');
    }

    private function seedCategories(): void
    {
        $names = collect($this->defaultCategories);

        if (Schema::hasColumn('products', 'category')) {
            $names = $names->merge(DB::table('products')->whereNotNull('category')->pluck('category'));
        }

        $now = now();

        $names
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique(fn ($name) => strtolower($name))
            ->each(function ($name) use ($now) {
                DB::table('categories')->insertOrIgnore([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    private function moveProductCategoryNamesToIds(): void
    {
        if (! Schema::hasColumn('products', 'category')) {
            return;
        }

        $categoryIds = DB::table('categories')
            ->get(['id', 'name'])
            ->mapWithKeys(fn ($category) => [strtolower($category->name) => $category->id]);

        DB::table('products')
            ->whereNotNull('category')
            ->get(['id', 'category'])
            ->each(function ($product) use ($categoryIds) {
                $categoryName = strtolower(trim((string) $product->category));

                if ($categoryName === '') {
                    return;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['category_id' => $categoryIds[$categoryName] ?? null]);
            });
    }
};
