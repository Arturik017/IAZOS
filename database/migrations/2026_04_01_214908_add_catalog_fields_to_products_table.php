<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'base_price')) {
                $table->decimal('base_price', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('products', 'has_variants')) {
                $table->boolean('has_variants')->default(false);
            }

            if (!Schema::hasColumn('products', 'primary_image')) {
                $table->string('primary_image')->nullable();
            }

            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('products', 'base_price')) {
                $columnsToDrop[] = 'base_price';
            }

            if (Schema::hasColumn('products', 'has_variants')) {
                $columnsToDrop[] = 'has_variants';
            }

            if (Schema::hasColumn('products', 'primary_image')) {
                $columnsToDrop[] = 'primary_image';
            }

            if (Schema::hasColumn('products', 'brand')) {
                $columnsToDrop[] = 'brand';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};