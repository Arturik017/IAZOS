<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    public static function public(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = ltrim($path, '/');
        $directPublicPath = public_path($normalized);
        $publicStoragePath = public_path('storage/' . $normalized);
        $basenamePublicPath = public_path(basename($normalized));
        $storagePublicPath = storage_path('app/public/' . $normalized);

        if (is_file($directPublicPath)) {
            return asset($normalized);
        }

        if (is_file($publicStoragePath)) {
            return asset('storage/' . $normalized);
        }

        // Legacy fallback for files that were uploaded into the public root.
        if (is_file($basenamePublicPath)) {
            return asset(basename($normalized));
        }

        if ((is_file($storagePublicPath) || Storage::disk('public')->exists($normalized)) && Route::has('media.public')) {
            return route('media.public', ['path' => $normalized]);
        }

        return null;
    }
}
