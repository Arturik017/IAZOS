<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->foreignId('seller_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'client_id', 'type'], 'conversations_seller_client_type_idx');
            $table->index(['seller_id', 'admin_id', 'type'], 'conversations_seller_admin_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
