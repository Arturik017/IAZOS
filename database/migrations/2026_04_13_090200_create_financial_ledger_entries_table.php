<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
            $table->unsignedBigInteger('payout_request_id')->nullable();
            $table->unsignedBigInteger('payout_batch_id')->nullable();
            $table->string('type');
            $table->string('bucket')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('MDL');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_ledger_entries');
    }
};
