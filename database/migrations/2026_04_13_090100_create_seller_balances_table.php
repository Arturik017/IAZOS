<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('pending_amount', 12, 2)->default(0);
            $table->decimal('available_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_balances');
    }
};
