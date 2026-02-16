<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) reparăm datele existente
        DB::table('orders')
            ->whereNull('payment_status')
            ->orWhere('payment_status', '')
            ->update(['payment_status' => 'unpaid']);

        // 2) modificăm coloana
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('unpaid')->index()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 255)->nullable()->default(null)->change();
        });
    }
};

