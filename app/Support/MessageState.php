<?php

namespace App\Support;

use App\Models\ConversationMessage;
use Illuminate\Support\Facades\Schema;

class MessageState
{
    public static function supported(): bool
    {
        return Schema::hasTable('conversations') && Schema::hasTable('conversation_messages');
    }

    public static function supportsMessageMedia(): bool
    {
        return self::supported()
            && Schema::hasColumns('conversation_messages', ['image_path', 'edited_at']);
    }

    public static function supportsThreadingAndStoryContext(): bool
    {
        return self::supported()
            && Schema::hasColumns('conversation_messages', ['reply_to_message_id', 'seller_story_id']);
    }

    public static function unreadCount(): int
    {
        if (!auth()->check() || !self::supported()) {
            return 0;
        }

        $userId = (int) auth()->id();

        return ConversationMessage::query()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->whereHas('conversation', function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhere('client_id', $userId)
                    ->orWhere('admin_id', $userId);
            })
            ->count();
    }
}
