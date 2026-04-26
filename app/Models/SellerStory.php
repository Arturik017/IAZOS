<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class SellerStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'media_type',
        'media_path',
        'thumbnail_path',
        'caption',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'seller_story_likes', 'story_id', 'user_id')->withTimestamps();
    }

    public function conversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'seller_story_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at instanceof Carbon
            ? $this->expires_at->isPast()
            : false;
    }
}
