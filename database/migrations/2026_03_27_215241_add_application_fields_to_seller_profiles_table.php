<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'legal_name')) {
                $table->string('legal_name')->nullable()->after('shop_name');
            }

            if (!Schema::hasColumn('seller_profiles', 'idnp')) {
                $table->string('idnp')->nullable()->after('seller_type');
            }

            if (!Schema::hasColumn('seller_profiles', 'delivery_type')) {
                $table->string('delivery_type')->nullable()->after('company_idno');
                // courier / personal
            }

            if (!Schema::hasColumn('seller_profiles', 'courier_company')) {
                $table->string('courier_company')->nullable()->after('delivery_type');
            }

            if (!Schema::hasColumn('seller_profiles', 'courier_contract_details')) {
                $table->text('courier_contract_details')->nullable()->after('courier_company');
            }

            if (!Schema::hasColumn('seller_profiles', 'notes')) {
                $table->text('notes')->nullable()->after('courier_contract_details');
            }

            if (!Schema::hasColumn('seller_profiles', 'application_status')) {
                $table->string('application_status')->default('pending')->after('commission_percent');
                // pending / approved / rejected
            }

            if (!Schema::hasColumn('seller_profiles', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('application_status');
            }

            if (!Schema::hasColumn('seller_profiles', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }

            if (Schema::hasColumn('seller_profiles', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('seller_profiles', 'application_status')) {
                $table->dropColumn('application_status');
            }

            if (Schema::hasColumn('seller_profiles', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('seller_profiles', 'courier_contract_details')) {
                $table->dropColumn('courier_contract_details');
            }

            if (Schema::hasColumn('seller_profiles', 'courier_company')) {
                $table->dropColumn('courier_company');
            }

            if (Schema::hasColumn('seller_profiles', 'delivery_type')) {
                $table->dropColumn('delivery_type');
            }

            if (Schema::hasColumn('seller_profiles', 'idnp')) {
                $table->dropColumn('idnp');
            }

            if (Schema::hasColumn('seller_profiles', 'legal_name')) {
                $table->dropColumn('legal_name');
            }
        });
    }
};