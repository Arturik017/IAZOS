<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->text('body')->nullable()->change();
            $table->string('image_path')->nullable()->after('body');
            $table->timestamp('edited_at')->nullable()->after('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_messages', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'edited_at']);
            $table->text('body')->nullable(false)->change();
        });
    }
};
