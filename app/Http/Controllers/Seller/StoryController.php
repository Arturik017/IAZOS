<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerStory;
use App\Support\ImageStorage;
use App\Support\StoryVideoStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    public function index()
    {
        $seller = $this->seller();

        if (!\App\Models\User::supportsSellerStories()) {
            return view('seller.stories.index', [
                'activeStories' => collect(),
                'recentExpiredStories' => collect(),
            ])->with('error', 'Story-urile vor deveni active dupa rularea migrarii noi.');
        }

        $activeStories = SellerStory::query()
            ->where('seller_id', $seller->id)
            ->active()
            ->latest()
            ->get();

        $recentExpiredStories = SellerStory::query()
            ->where('seller_id', $seller->id)
            ->where('expires_at', '<=', now())
            ->latest()
            ->limit(8)
            ->get();

        return view('seller.stories.index', compact('activeStories', 'recentExpiredStories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $seller = $this->seller();

        if (!\App\Models\User::supportsSellerStories()) {
            return back()->with('error', 'Story-urile vor deveni active dupa rularea migrarii noi.');
        }

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

        SellerStory::create([
            'seller_id' => $seller->id,
            'media_type' => $isVideo ? 'video' : 'image',
            'media_path' => $mediaPayload['media_path'],
            'thumbnail_path' => $mediaPayload['thumbnail_path'] ?? null,
            'caption' => $data['caption'] ?? null,
            'expires_at' => now()->addDay(),
        ]);

        return back()->with('success', 'Story-ul a fost publicat si va fi vizibil pe site si in aplicatia mobila.');
    }

    public function destroy(SellerStory $story): RedirectResponse
    {
        $seller = $this->seller();

        if (!\App\Models\User::supportsSellerStories()) {
            return back()->with('error', 'Story-urile vor deveni active dupa rularea migrarii noi.');
        }

        if ((int) $story->seller_id !== (int) $seller->id) {
            abort(403);
        }

        Storage::disk('public')->delete($story->media_path);
        if ($story->thumbnail_path) {
            Storage::disk('public')->delete($story->thumbnail_path);
        }

        $story->delete();

        return back()->with('success', 'Story-ul a fost sters.');
    }

    private function seller()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        return $user;
    }
}
