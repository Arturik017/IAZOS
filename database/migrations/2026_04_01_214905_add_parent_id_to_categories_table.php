<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }

            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }

            if (!Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('slug');
            }

            if (!Schema::hasColumn('categories', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }

            if (Schema::hasColumn('categories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('categories', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};