<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_applications', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            $table->string('shop_name');
            $table->string('legal_name')->nullable();

            $table->string('seller_type')->default('individual'); // individual | company
            $table->string('idnp')->nullable();
            $table->string('company_idno')->nullable();

            $table->string('pickup_address')->nullable();

            $table->string('delivery_type')->nullable(); // courier | personal
            $table->string('courier_company')->nullable();
            $table->text('courier_contract_details')->nullable();

            $table->text('notes')->nullable();

            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_applications');
    }
};