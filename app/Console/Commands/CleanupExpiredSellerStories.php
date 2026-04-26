<?php

namespace App\Console\Commands;

use App\Models\SellerStory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredSellerStories extends Command
{
    protected $signature = 'stories:cleanup-expired';

    protected $description = 'Delete expired seller stories and their stored media files';

    public function handle(): int
    {
        if (!User::supportsSellerStories()) {
            $this->info('seller_stories table not available yet. Nothing to clean.');

            return self::SUCCESS;
        }

        $expiredStories = SellerStory::query()
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredStories->isEmpty()) {
            $this->info('No expired seller stories found.');

            return self::SUCCESS;
        }

        foreach ($expiredStories as $story) {
            Storage::disk('public')->delete($story->media_path);
            if ($story->thumbnail_path) {
                Storage::disk('public')->delete($story->thumbnail_path);
            }
            $story->delete();
        }

        $this->info('Expired seller stories deleted: ' . $expiredStories->count());

        return self::SUCCESS;
    }
}
