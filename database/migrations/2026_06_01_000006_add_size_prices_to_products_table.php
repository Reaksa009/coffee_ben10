<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('small_price', 10, 2)->nullable()->after('price');
            $table->decimal('medium_price', 10, 2)->nullable()->after('small_price');
            $table->decimal('large_price', 10, 2)->nullable()->after('medium_price');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['small_price', 'medium_price', 'large_price']);
        });
    }
};
