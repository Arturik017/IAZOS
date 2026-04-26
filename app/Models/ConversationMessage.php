<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversationMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'reply_to_message_id',
        'seller_story_id',
        'body',
        'image_path',
        'read_at',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'edited_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_to_message_id');
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(SellerStory::class, 'seller_story_id');
    }
}
