<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageStorage
{
    public static function storeWebp(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $quality = 82,
        string $field = 'image',
        int $maxWidth = 1600,
        int $maxHeight = 1600
    ): string {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagewebp')) {
            return self::storeOriginal($file, $directory, $disk);
        }

        $contents = $file->get();
        $imageInfo = @getimagesizefromstring($contents);

        if (!$imageInfo || !isset($imageInfo['mime'])) {
            throw ValidationException::withMessages([
                $field => 'Fisierul incarcat nu poate fi procesat ca imagine.',
            ]);
        }

        if ($imageInfo['mime'] === 'image/svg+xml') {
            return self::storeOriginal($file, $directory, $disk);
        }

        $image = @imagecreatefromstring($contents);

        if (!$image) {
            return self::storeOriginal($file, $directory, $disk);
        }

        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($image);
        }

        $image = self::resizeImageResource($image, (int) ($imageInfo[0] ?? 0), (int) ($imageInfo[1] ?? 0), $maxWidth, $maxHeight);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $directory = trim($directory, '/');
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName !== '' ? $baseName : 'image';
        $path = $directory . '/' . $baseName . '-' . Str::random(12) . '.webp';

        $temporaryPath = tempnam(sys_get_temp_dir(), 'iaz_webp_');

        if ($temporaryPath === false) {
            imagedestroy($image);

            throw ValidationException::withMessages([
                $field => 'Nu s-a putut pregati conversia imaginii in WEBP.',
            ]);
        }

        $converted = imagewebp($image, $temporaryPath, $quality);
        imagedestroy($image);

        if (!$converted) {
            @unlink($temporaryPath);
            return self::storeOriginal($file, $directory, $disk);
        }

        $stream = fopen($temporaryPath, 'rb');

        if ($stream === false) {
            @unlink($temporaryPath);

            throw ValidationException::withMessages([
                $field => 'Nu s-a putut salva imaginea convertita.',
            ]);
        }

        Storage::disk($disk)->put($path, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        @unlink($temporaryPath);

        return $path;
    }

    private static function storeOriginal(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $directory = trim($directory, '/');
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName !== '' ? $baseName : 'image';
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');

        return $file->storeAs($directory, $baseName . '-' . Str::random(12) . '.' . $extension, $disk);
    }

    public static function storeOriginalMedia(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return self::storeOriginal($file, $directory, $disk);
    }

    private static function resizeImageResource($image, int $width, int $height, int $maxWidth, int $maxHeight)
    {
        if ($width <= 0 || $height <= 0 || ($width <= $maxWidth && $height <= $maxHeight)) {
            return $image;
        }

        $scale = min($maxWidth / $width, $maxHeight / $height);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}
