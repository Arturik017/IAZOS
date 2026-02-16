<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            // $table->string('pay_id')->nullable()->index();
            // $table->string('payment_status')->default('unpaid')->index(); // unpaid|pending|paid|failed|refunded

            // Detalii tranzacție (din callback/payment-info)
            $table->string('maib_status')->nullable();        // OK/FAIL etc
            $table->string('maib_status_code')->nullable();   // 000 etc
            $table->string('maib_status_message')->nullable();// Approved etc
            $table->string('maib_rrn')->nullable();
            $table->string('maib_approval')->nullable();
            $table->string('maib_card')->nullable();          // masked
            // $table->timestamp('paid_at')->nullable();
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'pay_id','payment_status',
                'maib_status','maib_status_code','maib_status_message',
                'maib_rrn','maib_approval','maib_card','paid_at'
            ]);
        });
    }
};

