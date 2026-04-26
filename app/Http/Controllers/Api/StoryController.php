<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerStory;
use App\Models\User;
use App\Support\ImageStorage;
use App\Support\MediaUrl;
use App\Support\StoryVideoStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!User::supportsSellerStories()) {
            return response()->json([
                'ok' => true,
                'groups' => [],
                'message' => 'Seller stories not available until migrations are applied.',
            ]);
        }

        $stories = SellerStory::query()
            ->with(['seller.sellerProfile'])
            ->active()
            ->latest()
            ->get()
            ->groupBy('seller_id');

        $followedIds = collect();
        if ($request->user() && User::supportsSellerFollowers()) {
            $followedIds = $request->user()->followedSellers()->pluck('users.id');
        }

        $groups = $stories->map(function ($items) use ($followedIds, $request) {
            $seller = $items->first()->seller;
            if (User::supportsSellerFollowers()) {
                $seller->loadCount('followers');
            }
            if (User::supportsSellerStoryLikes()) {
                $items->loadCount('likes');
            }

            return [
                'seller' => [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'shop_name' => $seller->sellerProfile->shop_name ?? $seller->name,
                    'avatar_url' => MediaUrl::public($seller->sellerProfile->avatar_path ?? null),
                    'seller_url' => route('seller.public.show', $seller),
                    'followers_count' => method_exists($seller, 'getAttribute') ? (int) ($seller->followers_count ?? 0) : 0,
                    'is_following' => $request->user() && User::supportsSellerFollowers()
                        ? $request->user()->isFollowingSeller((int) $seller->id)
                        : false,
                ],
                'is_followed_priority' => $followedIds->contains($seller->id),
                'stories' => $items->map(fn (SellerStory $story) => $this->storyPayload($story))->values(),
            ];
        })->sortByDesc('is_followed_priority')->values();

        return response()->json([
            'ok' => true,
            'groups' => $groups,
        ]);
    }

    public function sellerStories(User $user, Request $request): JsonResponse
    {
        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(404);
        }

        if (!User::supportsSellerStories()) {
            return response()->json([
                'ok' => true,
                'seller' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'shop_name' => $user->sellerProfile->shop_name ?? $user->name,
                    'avatar_url' => MediaUrl::public($user->sellerProfile->avatar_path ?? null),
                    'is_following' => $request->user() && User::supportsSellerFollowers()
                        ? $request->user()->isFollowingSeller((int) $user->id)
                        : false,
                ],
                'stories' => [],
                'message' => 'Seller stories not available until migrations are applied.',
            ]);
        }

        $stories = SellerStory::query()
            ->with(['seller.sellerProfile'])
            ->when(User::supportsSellerStoryLikes(), fn ($query) => $query->withCount('likes'))
            ->where('seller_id', $user->id)
            ->active()
            ->latest()
            ->get();
        if (User::supportsSellerFollowers()) {
            $user->loadCount('followers');
        }

        return response()->json([
            'ok' => true,
            'seller' => [
                'id' => $user->id,
                'name' => $user->name,
                'shop_name' => $user->sellerProfile->shop_name ?? $user->name,
                'avatar_url' => MediaUrl::public($user->sellerProfile->avatar_path ?? null),
                'seller_url' => route('seller.public.show', $user),
                'is_following' => $request->user() && User::supportsSellerFollowers()
                    ? $request->user()->isFollowingSeller((int) $user->id)
                    : false,
            ],
            'stories' => $stories->map(fn (SellerStory $story) => $this->storyPayload($story))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!User::supportsSellerStories()) {
            return response()->json([
                'ok' => false,
                'message' => 'Seller stories not available until migrations are applied.',
            ], 409);
        }

        $seller = $this->seller($request);

        $data = $request->validate([
            'media' => ['required', 'file', 'max:25600', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/bmp,video/mp4,video/webm,video/quicktime'],
            'caption' => ['nullable', 'string', 'max:280'],
        ]);

        $file = $request->file('media');
        $mime = (string) $file->getMimeType();
        $isVideo = str_starts_with($mime, 'video/');

        $mediaPayload = $isVideo
            ? StoryVideoStorage::store($file, 'public')
            : [
                'media_path' => ImageStorage::storeWebp($file, 'stories/images', 'public', 80, 'media', 1080, 1920),
                'thumbnail_path' => null,
            ];

        $story = SellerStory::create([
            'seller_id' => $seller->id,
            'media_type' => $isVideo ? 'video' : 'image',
            'media_path' => $mediaPayload['media_path'],
            'thumbnail_path' => $mediaPayload['thumbnail_path'] ?? null,
            'caption' => $data['caption'] ?? null,
            'expires_at' => now()->addDay(),
        ]);

        $story->load('seller.sellerProfile');

        return response()->json([
            'ok' => true,
            'message' => 'Story-ul a fost publicat.',
            'story' => $this->storyPayload($story),
        ], 201);
    }

    public function destroy(Request $request, SellerStory $story): JsonResponse
    {
        if (!User::supportsSellerStories()) {
            return response()->json([
                'ok' => false,
                'message' => 'Seller stories not available until migrations are applied.',
            ], 409);
        }

        $seller = $this->seller($request);

        if ((int) $story->seller_id !== (int) $seller->id) {
            abort(403);
        }

        Storage::disk('public')->delete($story->media_path);
        if ($story->thumbnail_path) {
            Storage::disk('public')->delete($story->thumbnail_path);
        }

        $story->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Story-ul a fost sters.',
        ]);
    }

    private function storyPayload(SellerStory $story): array
    {
        return [
            'id' => $story->id,
            'media_type' => $story->media_type,
            'media_url' => MediaUrl::public($story->media_path),
            'thumbnail_url' => MediaUrl::public($story->thumbnail_path),
            'caption' => $story->caption,
            'likes_count' => User::supportsSellerStoryLikes() ? (int) ($story->likes_count ?? 0) : 0,
            'is_liked' => request()->user() && User::supportsSellerStoryLikes()
                ? $story->likes()->where('user_id', request()->user()->id)->exists()
                : false,
            'expires_at' => $story->expires_at?->toIso8601String(),
            'created_at' => $story->created_at?->toIso8601String(),
        ];
    }

    private function seller(Request $request): User
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        return $user;
    }
}
