<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'seller_id',
        'client_id',
        'admin_id',
        'product_id',
        'created_by',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ConversationMessage::class)->latestOfMany();
    }

    public function includesUser(int $userId): bool
    {
        return in_array($userId, array_filter([
            $this->seller_id,
            $this->client_id,
            $this->admin_id,
        ]), true);
    }

    public function otherParticipantFor(User $user): ?User
    {
        foreach ([$this->seller, $this->client, $this->admin] as $participant) {
            if ($participant && (int) $participant->id !== (int) $user->id) {
                return $participant;
            }
        }

        return null;
    }
}
