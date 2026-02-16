<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'pay_id')) {
            $table->string('pay_id')->nullable()->index();
        }
        if (!Schema::hasColumn('orders', 'payment_status')) {
            $table->string('payment_status')->default('unpaid')->index();
        }
    });
}

    public function down(): void {
    Schema::table('orders', function (Blueprint $table) {
        if (Schema::hasColumn('orders', 'pay_id')) {
            $table->dropColumn('pay_id');
        }
        if (Schema::hasColumn('orders', 'payment_status')) {
            $table->dropColumn('payment_status');
        }
    });
}

};
