<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('media_type', 20);
            $table->string('media_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('caption', 280)->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_stories');
    }
};
