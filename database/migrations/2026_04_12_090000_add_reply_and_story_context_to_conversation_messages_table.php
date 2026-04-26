<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('conversation_messages', 'reply_to_message_id')) {
                $table->foreignId('reply_to_message_id')
                    ->nullable()
                    ->after('sender_id')
                    ->constrained('conversation_messages')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('conversation_messages', 'seller_story_id')) {
                $table->foreignId('seller_story_id')
                    ->nullable()
                    ->after('reply_to_message_id')
                    ->constrained('seller_stories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversation_messages', function (Blueprint $table) {
            if (Schema::hasColumn('conversation_messages', 'seller_story_id')) {
                $table->dropConstrainedForeignId('seller_story_id');
            }

            if (Schema::hasColumn('conversation_messages', 'reply_to_message_id')) {
                $table->dropConstrainedForeignId('reply_to_message_id');
            }
        });
    }
};
