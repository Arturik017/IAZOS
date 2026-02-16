<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // adaugă doar dacă nu există deja în migrarea ta inițială
            if (!Schema::hasColumn('banners', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (!Schema::hasColumn('banners', 'kicker')) {
                $table->string('kicker')->nullable()->after('subtitle');
            }
            if (!Schema::hasColumn('banners', 'link')) {
                $table->string('link')->nullable()->after('kicker');
            }
            if (!Schema::hasColumn('banners', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('link');
            }
            if (!Schema::hasColumn('banners', 'status')) {
                $table->boolean('status')->default(1)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // în down le poți scoate
            if (Schema::hasColumn('banners', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('banners', 'sort_order')) $table->dropColumn('sort_order');
            if (Schema::hasColumn('banners', 'link')) $table->dropColumn('link');
            if (Schema::hasColumn('banners', 'kicker')) $table->dropColumn('kicker');
            if (Schema::hasColumn('banners', 'subtitle')) $table->dropColumn('subtitle');
        });
    }
};
