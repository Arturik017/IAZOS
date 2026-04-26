<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('notes');
            $table->boolean('has_online_payments_enabled')->default(false)->after('payment_provider');
            $table->string('merchant_id')->nullable()->after('has_online_payments_enabled');
            $table->string('terminal_id')->nullable()->after('merchant_id');
            $table->text('api_key')->nullable()->after('terminal_id');
            $table->text('secret_key')->nullable()->after('api_key');
            $table->string('payment_contact_email')->nullable()->after('secret_key');
            $table->string('settlement_iban')->nullable()->after('payment_contact_email');
            $table->text('payment_notes')->nullable()->after('settlement_iban');
        });
    }

    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'has_online_payments_enabled',
                'merchant_id',
                'terminal_id',
                'api_key',
                'secret_key',
                'payment_contact_email',
                'settlement_iban',
                'payment_notes',
            ]);
        });
    }
};
