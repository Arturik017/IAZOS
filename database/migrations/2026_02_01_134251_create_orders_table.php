<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // client
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->text('customer_note')->nullable();

            // total
            $table->decimal('subtotal', 10, 2)->default(0);

            // status
            $table->string('status')->default('new'); // new, confirmed, shipped, canceled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
