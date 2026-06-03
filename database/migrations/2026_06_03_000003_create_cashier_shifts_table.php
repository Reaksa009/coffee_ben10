<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 10, 2)->default(0);
            $table->decimal('cash_in', 10, 2)->default(0);
            $table->decimal('cash_out', 10, 2)->default(0);
            $table->decimal('cash_sales_amount', 10, 2)->default(0);
            $table->decimal('expected_cash_amount', 10, 2)->default(0);
            $table->decimal('closing_cash', 10, 2)->nullable();
            $table->decimal('cash_difference', 10, 2)->default(0);
            $table->string('status')->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
