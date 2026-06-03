<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_date')) {
                $table->date('order_date')->nullable()->after('customer_phone');
            }

            if (! Schema::hasColumn('orders', 'daily_order_number')) {
                $table->unsignedInteger('daily_order_number')->nullable()->after('order_date');
            }
        });

        $this->backfillDailyOrderNumbers();

        Schema::table('orders', function (Blueprint $table) {
            $table->unique(['order_date', 'daily_order_number'], 'orders_order_date_daily_order_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique('orders_order_date_daily_order_number_unique');

            foreach (['daily_order_number', 'order_date'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function backfillDailyOrderNumbers(): void
    {
        $ordersByDate = DB::table('orders')
            ->select('id', 'created_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->toDateString();
            });

        foreach ($ordersByDate as $date => $orders) {
            foreach ($orders->values() as $index => $order) {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update([
                        'order_date' => $date,
                        'daily_order_number' => $index + 1,
                    ]);
            }
        }
    }
};
