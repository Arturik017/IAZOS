<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'gross_amount')) {
                $table->decimal('gross_amount', 12, 2)->default(0)->after('qty');
            }

            if (!Schema::hasColumn('order_items', 'platform_commission_percent')) {
                $table->decimal('platform_commission_percent', 5, 2)->default(0)->after('gross_amount');
            }

            if (!Schema::hasColumn('order_items', 'platform_commission_amount')) {
                $table->decimal('platform_commission_amount', 12, 2)->default(0)->after('platform_commission_percent');
            }

            if (!Schema::hasColumn('order_items', 'seller_net_amount')) {
                $table->decimal('seller_net_amount', 12, 2)->default(0)->after('platform_commission_amount');
            }

            if (!Schema::hasColumn('order_items', 'financial_status')) {
                $table->string('financial_status')->default('unpaid')->after('seller_net_amount');
            }

            if (!Schema::hasColumn('order_items', 'financial_status_updated_at')) {
                $table->timestamp('financial_status_updated_at')->nullable()->after('financial_status');
            }

            if (!Schema::hasColumn('order_items', 'admin_release_status')) {
                $table->string('admin_release_status')->default('not_requested')->after('financial_status_updated_at');
            }

            if (!Schema::hasColumn('order_items', 'delivered_reported_at')) {
                $table->timestamp('delivered_reported_at')->nullable()->after('admin_release_status');
            }

            if (!Schema::hasColumn('order_items', 'admin_released_at')) {
                $table->timestamp('admin_released_at')->nullable()->after('delivered_reported_at');
            }

            if (!Schema::hasColumn('order_items', 'admin_released_by')) {
                $table->foreignId('admin_released_by')->nullable()->after('admin_released_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'admin_release_note')) {
                $table->string('admin_release_note', 255)->nullable()->after('admin_released_by');
            }

            if (!Schema::hasColumn('order_items', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('admin_release_note');
            }

            if (!Schema::hasColumn('order_items', 'refunded_by')) {
                $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'refund_reason')) {
                $table->string('refund_reason', 255)->nullable()->after('refunded_by');
            }
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'payout_beneficiary_name')) {
                $table->string('payout_beneficiary_name')->nullable()->after('legal_name');
            }

            if (!Schema::hasColumn('seller_profiles', 'payout_iban')) {
                $table->string('payout_iban', 64)->nullable()->after('payout_beneficiary_name');
            }

            if (!Schema::hasColumn('seller_profiles', 'payout_bank_name')) {
                $table->string('payout_bank_name')->nullable()->after('payout_iban');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            foreach (['refunded_by', 'admin_released_by'] as $foreignId) {
                if (Schema::hasColumn('order_items', $foreignId)) {
                    $table->dropConstrainedForeignId($foreignId);
                }
            }

            foreach ([
                'refund_reason',
                'refunded_at',
                'admin_release_note',
                'admin_released_at',
                'delivered_reported_at',
                'admin_release_status',
                'financial_status_updated_at',
                'financial_status',
                'seller_net_amount',
                'platform_commission_amount',
                'platform_commission_percent',
                'gross_amount',
            ] as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('seller_profiles', function (Blueprint $table) {
            foreach (['payout_bank_name', 'payout_iban', 'payout_beneficiary_name'] as $column) {
                if (Schema::hasColumn('seller_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
