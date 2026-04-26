<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_attributes', function (Blueprint $table) {
            $table->string('unit_mode')->default('none')->after('type');
            // none | fixed | selectable | free

            $table->string('default_unit')->nullable()->after('unit_mode');

            $table->json('allowed_units')->nullable()->after('default_unit');
        });
    }

    public function down(): void
    {
        Schema::table('category_attributes', function (Blueprint $table) {
            $table->dropColumn(['unit_mode', 'default_unit', 'allowed_units']);
        });
    }
};