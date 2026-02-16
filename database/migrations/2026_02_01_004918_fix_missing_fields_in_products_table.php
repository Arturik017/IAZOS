<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('image');
            }

            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
            }

            if (!Schema::hasColumn('products', 'is_promo')) {
                $table->boolean('is_promo')->default(0)->after('subcategory_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category_id','subcategory_id','is_promo']);
        });
    }
};
