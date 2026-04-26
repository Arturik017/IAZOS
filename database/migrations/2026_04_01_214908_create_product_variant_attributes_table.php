<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_attribute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('category_attribute_options')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['product_variant_id', 'category_attribute_id'],
                'prod_variant_attr_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};