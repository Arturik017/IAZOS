<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_profile_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('terminal_id')->nullable();
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->string('payment_contact_email')->nullable();
            $table->string('settlement_iban')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('status')->default('missing');
            $table->text('notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payment_accounts');
    }
};
