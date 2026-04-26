<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'checkout_uuid')) {
                $table->string('checkout_uuid')->nullable()->after('seller_id')->index();
            }

            if (!Schema::hasColumn('orders', 'checkout_group_id')) {
                $table->string('checkout_group_id')->nullable()->after('checkout_uuid')->index();
            }

            if (!Schema::hasColumn('orders', 'payment_flow')) {
                $table->string('payment_flow')->default('legacy_platform')->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('payment_flow');
            }

            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->text('payment_url')->nullable()->after('payment_provider');
            }

            if (!Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_url');
            }

            if (!Schema::hasColumn('orders', 'payment_link_generated_at')) {
                $table->timestamp('payment_link_generated_at')->nullable()->after('payment_reference');
            }

            if (!Schema::hasColumn('orders', 'commission_percent')) {
                $table->decimal('commission_percent', 5, 2)->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('orders', 'commission_amount')) {
                $table->decimal('commission_amount', 10, 2)->default(0)->after('commission_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'seller_id')) {
                $table->dropConstrainedForeignId('seller_id');
            }

            $columns = array_filter([
                Schema::hasColumn('orders', 'checkout_uuid') ? 'checkout_uuid' : null,
                Schema::hasColumn('orders', 'checkout_group_id') ? 'checkout_group_id' : null,
                Schema::hasColumn('orders', 'payment_flow') ? 'payment_flow' : null,
                Schema::hasColumn('orders', 'payment_provider') ? 'payment_provider' : null,
                Schema::hasColumn('orders', 'payment_url') ? 'payment_url' : null,
                Schema::hasColumn('orders', 'payment_reference') ? 'payment_reference' : null,
                Schema::hasColumn('orders', 'payment_link_generated_at') ? 'payment_link_generated_at' : null,
                Schema::hasColumn('orders', 'commission_percent') ? 'commission_percent' : null,
                Schema::hasColumn('orders', 'commission_amount') ? 'commission_amount' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
