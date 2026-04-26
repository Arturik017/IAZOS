<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryVideoStorage
{
    public static function store(UploadedFile $file, string $disk = 'public'): array
    {
        if (!self::supportsFfmpeg()) {
            return [
                'media_path' => ImageStorage::storeOriginalMedia($file, 'stories/videos', $disk),
                'thumbnail_path' => null,
                'was_transcoded' => false,
            ];
        }

        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName !== '' ? $baseName : 'story-video';
        $targetName = $baseName . '-' . Str::random(12);
        $relativeVideoPath = 'stories/videos/' . $targetName . '.mp4';
        $relativeThumbnailPath = 'stories/videos/' . $targetName . '.webp';

        $sourcePath = $file->getRealPath();
        $temporaryVideo = tempnam(sys_get_temp_dir(), 'iaz_story_video_');
        $temporaryThumbnail = tempnam(sys_get_temp_dir(), 'iaz_story_thumb_');

        if (!$sourcePath || $temporaryVideo === false || $temporaryThumbnail === false) {
            return [
                'media_path' => ImageStorage::storeOriginalMedia($file, 'stories/videos', $disk),
                'thumbnail_path' => null,
                'was_transcoded' => false,
            ];
        }

        $videoOutput = $temporaryVideo . '.mp4';
        $thumbOutput = $temporaryThumbnail . '.webp';
        @unlink($temporaryVideo);
        @unlink($temporaryThumbnail);

        $videoCommand = sprintf(
            'ffmpeg -y -i %s -vf "scale=\'min(720,iw)\':-2:force_original_aspect_ratio=decrease" -c:v libx264 -preset veryfast -crf 30 -movflags +faststart -c:a aac -b:a 96k %s 2>&1',
            escapeshellarg($sourcePath),
            escapeshellarg($videoOutput)
        );

        exec($videoCommand, $videoResult, $videoCode);

        if ($videoCode !== 0 || !is_file($videoOutput)) {
            @unlink($videoOutput);
            @unlink($thumbOutput);

            return [
                'media_path' => ImageStorage::storeOriginalMedia($file, 'stories/videos', $disk),
                'thumbnail_path' => null,
                'was_transcoded' => false,
            ];
        }

        $thumbCommand = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01.000 -vframes 1 -vf "scale=\'min(720,iw)\':-2:force_original_aspect_ratio=decrease" %s 2>&1',
            escapeshellarg($videoOutput),
            escapeshellarg($thumbOutput)
        );

        exec($thumbCommand, $thumbResult, $thumbCode);

        Storage::disk($disk)->put($relativeVideoPath, fopen($videoOutput, 'rb'));
        if (is_file($thumbOutput)) {
            Storage::disk($disk)->put($relativeThumbnailPath, fopen($thumbOutput, 'rb'));
        }

        @unlink($videoOutput);
        @unlink($thumbOutput);

        return [
            'media_path' => $relativeVideoPath,
            'thumbnail_path' => Storage::disk($disk)->exists($relativeThumbnailPath) ? $relativeThumbnailPath : null,
            'was_transcoded' => true,
        ];
    }

    public static function supportsFfmpeg(): bool
    {
        $output = [];
        $code = 1;
        @exec('ffmpeg -version 2>NUL', $output, $code);

        return $code === 0;
    }
}
