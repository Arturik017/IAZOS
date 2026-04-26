<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function public(string $path)
    {
        $normalized = ltrim($path, '/');
        $physicalPath = storage_path('app/public/' . $normalized);

        if (is_file($physicalPath)) {
            return response()->file($physicalPath);
        }

        abort_unless(Storage::disk('public')->exists($normalized), 404);

        return Storage::disk('public')->response($normalized);
    }
}
