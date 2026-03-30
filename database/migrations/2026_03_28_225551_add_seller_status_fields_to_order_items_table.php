<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'seller_status')) {
                $table->string('seller_status')->default('pending')->after('seller_id');
            }

            if (!Schema::hasColumn('order_items', 'seller_status_updated_at')) {
                $table->timestamp('seller_status_updated_at')->nullable()->after('seller_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'seller_status_updated_at')) {
                $table->dropColumn('seller_status_updated_at');
            }

            if (Schema::hasColumn('order_items', 'seller_status')) {
                $table->dropColumn('seller_status');
            }
        });
    }
};