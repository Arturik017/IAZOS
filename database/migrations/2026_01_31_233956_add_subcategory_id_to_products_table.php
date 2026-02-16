<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // dacă ai deja category_id, atunci doar adaugă subcategory_id
            $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');

            // opțional (recomandat) dacă vrei constrângeri:
            // $table->foreign('subcategory_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // dacă ai pus foreign, întâi îl ștergi:
            // $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
        });
    }
};
