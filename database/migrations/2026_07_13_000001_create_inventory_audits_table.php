<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventory_audits')) {
            Schema::create('inventory_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->date('audit_date');
                $table->text('notes')->nullable();
                $table->decimal('total_variance_cost', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('inventory_audit_items')) {
            Schema::create('inventory_audit_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->decimal('theoretical_quantity', 12, 3);
                $table->decimal('physical_quantity', 12, 3);
                $table->decimal('variance_quantity', 12, 3);
                $table->decimal('unit_cost', 10, 4);
                $table->decimal('variance_cost', 12, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
    }
};
