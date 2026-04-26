<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SellerApplication;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class SellerAccountDeletionService
{
    public function deleteApplicationAndRelatedData(SellerApplication $application): array
    {
        $user = User::where('email', $application->email)->where('role', 'seller')->first();

        $files = [];
        $deletedUserId = null;
        $productIds = [];

        if ($user) {
            $deletedUserId = $user->id;
            $files = $this->collectSellerFiles($user);
            $productIds = Product::query()
                ->where('seller_id', $user->id)
                ->pluck('id')
                ->all();
        }

        DB::transaction(function () use ($application, $user, $productIds) {
            if ($user) {
                if (class_exists(PersonalAccessToken::class)) {
                    PersonalAccessToken::query()
                        ->where('tokenable_type', User::class)
                        ->where('tokenable_id', $user->id)
                        ->delete();
                }

                if (!empty($productIds)) {
                    DB::table('products')
                        ->whereIn('id', $productIds)
                        ->delete();
                }

                $user->delete();
            }

            $application->delete();
        });

        if (!empty($productIds)) {
            Product::query()
                ->whereIn('id', $productIds)
                ->delete();
        }

        $orphanedDeletedCount = $this->cleanupOrphanedMarketplaceProducts();

        $this->deleteFiles($files);
        Cache::flush();

        return [
            'deleted_user_id' => $deletedUserId,
            'deleted_email' => $application->email,
            'deleted_files_count' => count($files),
            'deleted_orphaned_products_count' => $orphanedDeletedCount,
        ];
    }

    public function cleanupOrphanedMarketplaceProducts(): int
    {
        $orphanedProducts = Product::query()
            ->whereNull('seller_id')
            ->where(function ($query) {
                $query
                    ->whereNotNull('primary_image')
                    ->orWhereNotNull('proof_path')
                    ->orWhere('has_variants', true);
            })
            ->with(['images', 'variants', 'reviews.images'])
            ->get();

        if ($orphanedProducts->isEmpty()) {
            return 0;
        }

        $files = [];
        $productIds = $orphanedProducts->pluck('id')->all();

        foreach ($orphanedProducts as $product) {
            foreach (['image', 'primary_image', 'proof_path', 'ai_banner_path'] as $field) {
                if (!empty($product->{$field})) {
                    $files[] = $product->{$field};
                }
            }

            foreach ($product->images as $image) {
                if ($image->path) {
                    $files[] = $image->path;
                }
            }

            foreach ($product->variants as $variant) {
                if (!empty($variant->image)) {
                    $files[] = $variant->image;
                }
            }

            foreach ($product->reviews as $review) {
                foreach ($review->images as $image) {
                    if ($image->image_path) {
                        $files[] = $image->image_path;
                    }
                }
            }
        }

        DB::table('products')
            ->whereIn('id', $productIds)
            ->delete();

        $this->deleteFiles(array_values(array_unique(array_filter($files))));

        return count($productIds);
    }

    private function collectSellerFiles(User $user): array
    {
        $files = [];

        $profile = $user->sellerProfile;
        if ($profile?->avatar_path) {
            $files[] = $profile->avatar_path;
        }

        $stories = $user->stories()->get();
        foreach ($stories as $story) {
            if ($story->media_path) {
                $files[] = $story->media_path;
            }
            if ($story->thumbnail_path) {
                $files[] = $story->thumbnail_path;
            }
        }

        $products = Product::query()
            ->where('seller_id', $user->id)
            ->with(['images', 'variants', 'reviews.images'])
            ->get();

        foreach ($products as $product) {
            foreach (['image', 'primary_image', 'proof_path', 'ai_banner_path'] as $field) {
                if (!empty($product->{$field})) {
                    $files[] = $product->{$field};
                }
            }

            foreach ($product->images as $image) {
                if ($image->path) {
                    $files[] = $image->path;
                }
            }

            foreach ($product->variants as $variant) {
                if (!empty($variant->image)) {
                    $files[] = $variant->image;
                }
            }

            foreach ($product->reviews as $review) {
                foreach ($review->images as $image) {
                    if ($image->image_path) {
                        $files[] = $image->image_path;
                    }
                }
            }
        }

        $reviewImages = ProductReview::query()
            ->where('user_id', $user->id)
            ->with('images')
            ->get()
            ->flatMap(fn (ProductReview $review) => $review->images->pluck('image_path'))
            ->all();

        $files = array_merge($files, $reviewImages);

        $conversationImages = Conversation::query()
            ->where('seller_id', $user->id)
            ->with('messages:id,conversation_id,image_path')
            ->get()
            ->flatMap(fn (Conversation $conversation) => $conversation->messages->pluck('image_path'))
            ->filter()
            ->all();

        $sentImages = ConversationMessage::query()
            ->where('sender_id', $user->id)
            ->whereNotNull('image_path')
            ->pluck('image_path')
            ->all();

        $files = array_merge($files, $conversationImages, $sentImages);

        return array_values(array_unique(array_filter($files)));
    }

    private function deleteFiles(array $files): void
    {
        foreach ($files as $path) {
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable) {
            }
        }
    }
}
