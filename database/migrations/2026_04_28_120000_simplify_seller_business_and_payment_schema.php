<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $columns = [];

            foreach (['seller_type', 'idnp', 'pickup_address', 'payment_provider'] as $column) {
                if (Schema::hasColumn('seller_applications', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            $columns = [];

            foreach (['seller_type', 'idnp', 'pickup_address'] as $column) {
                if (Schema::hasColumn('seller_profiles', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('seller_payment_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('seller_payment_accounts', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_applications', 'seller_type')) {
                $table->string('seller_type')->default('company')->after('legal_name');
            }

            if (!Schema::hasColumn('seller_applications', 'idnp')) {
                $table->string('idnp')->nullable()->after('seller_type');
            }

            if (!Schema::hasColumn('seller_applications', 'pickup_address')) {
                $table->string('pickup_address')->nullable()->after('company_idno');
            }

            if (!Schema::hasColumn('seller_applications', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('notes');
            }
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'seller_type')) {
                $table->string('seller_type')->default('company')->after('phone');
            }

            if (!Schema::hasColumn('seller_profiles', 'idnp')) {
                $table->string('idnp')->nullable()->after('seller_type');
            }

            if (!Schema::hasColumn('seller_profiles', 'pickup_address')) {
                $table->string('pickup_address')->nullable()->after('phone');
            }
        });

        Schema::table('seller_payment_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_payment_accounts', 'provider')) {
                $table->string('provider')->nullable()->after('seller_profile_id');
            }
        });
    }
};
