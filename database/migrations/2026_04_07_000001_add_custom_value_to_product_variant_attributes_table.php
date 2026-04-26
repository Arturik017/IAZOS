<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_variant_attributes')) {
            return;
        }

        Schema::table('product_variant_attributes', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variant_attributes', 'custom_value')) {
                $table->string('custom_value')->nullable()->after('option_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_variant_attributes')) {
            return;
        }

        Schema::table('product_variant_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('product_variant_attributes', 'custom_value')) {
                $table->dropColumn('custom_value');
            }
        });
    }
};