<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->nullable();       // sat/oras/municipiu
            $table->string('postal_code')->nullable();
            $table->timestamps();

            $table->index(['district_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localities');
    }
};
