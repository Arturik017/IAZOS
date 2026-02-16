<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // ✅ category_id
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('image')
                    ->constrained('categories')
                    ->nullOnDelete();
            }

            // ✅ subcategory_id
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->foreignId('subcategory_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('categories')
                    ->nullOnDelete();
            }

            // ✅ is_promo
            if (!Schema::hasColumn('products', 'is_promo')) {
                $table->boolean('is_promo')
                    ->default(0)
                    ->after('subcategory_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // întâi drop la foreign keys (dacă există)
            if (Schema::hasColumn('products', 'subcategory_id')) {
                try { $table->dropForeign(['subcategory_id']); } catch (\Throwable $e) {}
                $table->dropColumn('subcategory_id');
            }

            if (Schema::hasColumn('products', 'category_id')) {
                try { $table->dropForeign(['category_id']); } catch (\Throwable $e) {}
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('products', 'is_promo')) {
                $table->dropColumn('is_promo');
            }
        });
    }
};
