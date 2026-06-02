<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('verification_status')->default('pending')->after('status');
            $table->text('verification_error')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verification_error');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verification_error', 'verified_at']);
        });
    }
};
