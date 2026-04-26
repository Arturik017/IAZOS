<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('category_attribute_options')->nullOnDelete();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 12, 2)->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'category_attribute_id', 'option_id'], 'prod_attr_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};