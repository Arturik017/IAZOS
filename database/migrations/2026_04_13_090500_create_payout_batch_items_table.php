<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_batch_id')->constrained('payout_batches')->cascadeOnDelete();
            $table->foreignId('payout_request_id')->nullable()->constrained('payout_requests')->nullOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('MDL');
            $table->string('beneficiary_name')->nullable();
            $table->string('iban', 64)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_batch_items');
    }
};
