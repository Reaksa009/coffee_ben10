<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_type')) {
                $table->string('order_type')->default('takeaway')->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'table_number')) {
                $table->string('table_number')->nullable()->after('order_type');
            }

            if (! Schema::hasColumn('orders', 'pickup_name')) {
                $table->string('pickup_name')->nullable()->after('table_number');
            }
        });

        if (! Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('unit', 30)->default('unit');
                $table->decimal('quantity_on_hand', 12, 3)->default(0);
                $table->decimal('low_stock_quantity', 12, 3)->default(0);
                $table->decimal('unit_cost', 10, 4)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_ingredients')) {
            Schema::create('product_ingredients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
                $table->decimal('quantity', 12, 3);
                $table->string('unit', 30)->default('unit');
                $table->timestamps();

                $table->unique(['product_id', 'inventory_item_id']);
            });
        }

        if (! Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('contact_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('reference')->nullable();
                $table->date('purchase_date');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('purchase_items')) {
            Schema::create('purchase_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained()->restrictOnDelete();
                $table->decimal('quantity', 12, 3);
                $table->decimal('unit_cost', 10, 4);
                $table->decimal('line_total', 12, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('shop_settings')) {
            Schema::create('shop_settings', function (Blueprint $table) {
                $table->id();
                $table->string('shop_name')->default('Coffee Ben10');
                $table->text('address')->nullable();
                $table->string('phone')->nullable();
                $table->text('receipt_footer')->nullable();
                $table->string('currency', 10)->default('USD');
                $table->unsignedSmallInteger('receipt_width_mm')->default(80);
                $table->decimal('tax_rate', 6, 3)->default(0);
                $table->decimal('service_charge_rate', 6, 3)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->string('subject_type')->nullable();
                $table->string('subject_id')->nullable();
                $table->string('description');
                $table->json('properties')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('shop_settings');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('inventory_items');

        Schema::table('orders', function (Blueprint $table) {
            foreach (['pickup_name', 'table_number', 'order_type'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
