<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'seller_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function sellerPaymentAccount(): HasOne
    {
        return $this->hasOneThrough(
            SellerPaymentAccount::class,
            SellerProfile::class,
            'user_id',
            'seller_profile_id',
            'id',
            'id'
        );
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'seller_id');
    }

    public function sellerBalance(): HasOne
    {
        return $this->hasOne(SellerBalance::class, 'seller_id');
    }

    public function financialLedgerEntries(): HasMany
    {
        return $this->hasMany(FinancialLedgerEntry::class, 'seller_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class, 'seller_id');
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function productQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function sellerReviewsReceived(): HasMany
    {
        return $this->hasMany(SellerReview::class, 'seller_id');
    }

    public function sellerReviewsWritten(): HasMany
    {
        return $this->hasMany(SellerReview::class, 'user_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'seller_followers',
            'seller_id',
            'user_id'
        )->withTimestamps();
    }

    public function followedSellers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'seller_followers',
            'user_id',
            'seller_id'
        )->withTimestamps();
    }

    public function stories(): HasMany
    {
        return $this->hasMany(SellerStory::class, 'seller_id');
    }

    public function likedStories(): BelongsToMany
    {
        return $this->belongsToMany(SellerStory::class, 'seller_story_likes', 'user_id', 'story_id')->withTimestamps();
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')->withTimestamps();
    }

    public function sentConversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'sender_id');
    }

    public static function supportsSellerFollowers(): bool
    {
        return Schema::hasTable('seller_followers');
    }

    public static function supportsSellerStories(): bool
    {
        return Schema::hasTable('seller_stories');
    }

    public static function supportsSellerStoryLikes(): bool
    {
        return Schema::hasTable('seller_story_likes');
    }

    public static function supportsMessaging(): bool
    {
        return Schema::hasTable('conversations') && Schema::hasTable('conversation_messages');
    }

    public function isFollowingSeller(int $sellerId): bool
    {
        if (!self::supportsSellerFollowers()) {
            return false;
        }

        return $this->followedSellers()->where('seller_id', $sellerId)->exists();
    }

    public function averageSellerRating(): float
    {
        return round((float) ($this->sellerReviewsReceived()->avg('rating') ?? 0), 1);
    }

    public function sellerReviewsCount(): int
    {
        return (int) $this->sellerReviewsReceived()->count();
    }
}
