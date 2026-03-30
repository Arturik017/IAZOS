<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'ai_banner_prompt')) {
                $table->text('ai_banner_prompt')->nullable()->after('proof_path');
            }

            if (!Schema::hasColumn('products', 'ai_banner_path')) {
                $table->string('ai_banner_path')->nullable()->after('ai_banner_prompt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'ai_banner_path')) {
                $table->dropColumn('ai_banner_path');
            }

            if (Schema::hasColumn('products', 'ai_banner_prompt')) {
                $table->dropColumn('ai_banner_prompt');
            }
        });
    }
};