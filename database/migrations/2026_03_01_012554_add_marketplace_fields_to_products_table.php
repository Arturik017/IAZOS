<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'seller_id')) {
                $table->foreignId('seller_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('id');
            }

            // Livrarea inclusă în preț (tu vrei să fie regula)
            if (!Schema::hasColumn('products', 'shipping_included')) {
                $table->boolean('shipping_included')->default(true)->after('final_price');
            }

            // Moderare / anti-contrabandă (minim)
            if (!Schema::hasColumn('products', 'is_approved')) {
                $table->boolean('is_approved')->default(true)->after('status');
            }

            // Dovadă proveniență (factură etc.) pentru categorii riscante (când vrei)
            if (!Schema::hasColumn('products', 'proof_path')) {
                $table->string('proof_path')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'seller_id')) {
                $table->dropConstrainedForeignId('seller_id');
            }
            if (Schema::hasColumn('products', 'shipping_included')) {
                $table->dropColumn('shipping_included');
            }
            if (Schema::hasColumn('products', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            if (Schema::hasColumn('products', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
        });
    }
};