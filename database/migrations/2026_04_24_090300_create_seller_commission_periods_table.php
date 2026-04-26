<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_commission_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('deadline_at');
            $table->decimal('gross_sales_amount', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('status')->default('in_progress');
            $table->text('seller_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['seller_id', 'period_start', 'period_end'], 'seller_commission_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_commission_periods');
    }
};
