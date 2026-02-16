<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            // Adaugă coloanele MAIB TX doar dacă nu există deja
            if (!Schema::hasColumn('orders', 'maib_status')) {
                $table->string('maib_status')->nullable();          // OK / FAIL
            }
            if (!Schema::hasColumn('orders', 'maib_status_code')) {
                $table->string('maib_status_code')->nullable();     // 000 etc
            }
            if (!Schema::hasColumn('orders', 'maib_status_message')) {
                $table->string('maib_status_message')->nullable();  // Approved etc
            }
            if (!Schema::hasColumn('orders', 'maib_rrn')) {
                $table->string('maib_rrn')->nullable();
            }
            if (!Schema::hasColumn('orders', 'maib_approval')) {
                $table->string('maib_approval')->nullable();
            }
            if (!Schema::hasColumn('orders', 'maib_card')) {
                $table->string('maib_card')->nullable();            // masked card
            }
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            // Ștergem doar coloanele MAIB TX, dacă există
            if (Schema::hasColumn('orders', 'maib_status')) {
                $table->dropColumn('maib_status');
            }
            if (Schema::hasColumn('orders', 'maib_status_code')) {
                $table->dropColumn('maib_status_code');
            }
            if (Schema::hasColumn('orders', 'maib_status_message')) {
                $table->dropColumn('maib_status_message');
            }
            if (Schema::hasColumn('orders', 'maib_rrn')) {
                $table->dropColumn('maib_rrn');
            }
            if (Schema::hasColumn('orders', 'maib_approval')) {
                $table->dropColumn('maib_approval');
            }
            if (Schema::hasColumn('orders', 'maib_card')) {
                $table->dropColumn('maib_card');
            }
            if (Schema::hasColumn('orders', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
