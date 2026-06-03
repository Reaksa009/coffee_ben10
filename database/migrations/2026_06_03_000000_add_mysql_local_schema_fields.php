<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('phone')->nullable()->unique();
                $table->string('email')->nullable();
                $table->integer('points_balance')->default(0);
                $table->integer('total_points_earned')->default(0);
                $table->integer('total_points_redeemed')->default(0);
                $table->decimal('total_spent', 10, 2)->default(0);
                $table->integer('visits')->default(0);
                $table->timestamp('last_order_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'image_data')) {
                $table->longText('image_data')->nullable()->after('image');
            }

            if (! Schema::hasColumn('products', 'image_mime')) {
                $table->string('image_mime')->nullable()->after('image_data');
            }

            if (! Schema::hasColumn('products', 'image_name')) {
                $table->string('image_name')->nullable()->after('image_mime');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }

            if (! Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }

            if (! Schema::hasColumn('orders', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 10, 2)->default(0)->after('customer_phone');
            }

            if (! Schema::hasColumn('orders', 'promo_discount_amount')) {
                $table->decimal('promo_discount_amount', 10, 2)->default(0)->after('discount_amount');
            }

            if (! Schema::hasColumn('orders', 'loyalty_discount_amount')) {
                $table->decimal('loyalty_discount_amount', 10, 2)->default(0)->after('promo_discount_amount');
            }

            if (! Schema::hasColumn('orders', 'loyalty_points_redeemed')) {
                $table->integer('loyalty_points_redeemed')->default(0)->after('loyalty_discount_amount');
            }

            if (! Schema::hasColumn('orders', 'loyalty_points_earned')) {
                $table->integer('loyalty_points_earned')->default(0)->after('loyalty_points_redeemed');
            }

            if (! Schema::hasColumn('orders', 'loyalty_awarded_at')) {
                $table->timestamp('loyalty_awarded_at')->nullable()->after('loyalty_points_earned');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }

            $columns = [
                'customer_name',
                'customer_phone',
                'subtotal_amount',
                'promo_discount_amount',
                'loyalty_discount_amount',
                'loyalty_points_redeemed',
                'loyalty_points_earned',
                'loyalty_awarded_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['image_data', 'image_mime', 'image_name'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('customers');
    }
};
