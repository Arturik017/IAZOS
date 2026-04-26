<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('target_status', 20);
            $table->string('status', 30)->default('requested');
            $table->string('client_reason', 255);
            $table->text('client_note')->nullable();
            $table->text('seller_response')->nullable();
            $table->string('seller_recommended_status', 20)->nullable();
            $table->timestamp('seller_responded_at')->nullable();
            $table->text('admin_decision_note')->nullable();
            $table->string('resolved_financial_status', 20)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
