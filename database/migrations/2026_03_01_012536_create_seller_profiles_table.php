<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('shop_name');
            $table->string('phone')->nullable();
            $table->string('pickup_address')->nullable();

            // individual (freelancer) / company
            $table->string('seller_type')->default('individual'); // individual | company
            $table->string('company_idno')->nullable();

            // comision per seller (default 10%)
            $table->decimal('commission_percent', 5, 2)->default(10.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};