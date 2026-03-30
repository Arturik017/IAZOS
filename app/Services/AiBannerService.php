<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AiBannerService
{
    public function generateFromProductImage(UploadedFile|string $image, string $userPrompt): string
    {
        $apiKey = config('services.openai.api_key');
        $model = config('services.openai.image_model', 'gpt-image-1.5');

        if (!$apiKey) {
            throw new RuntimeException('OPENAI_API_KEY lipsește din .env');
        }

        $prompt = $this->buildBannerPrompt($userPrompt);

        if ($image instanceof UploadedFile) {
            $imageStream = fopen($image->getRealPath(), 'r');
            $imageName = $image->getClientOriginalName() ?: 'product.png';
            $imageMime = $image->getMimeType() ?: 'image/png';
        } else {
            $absolutePath = Storage::disk('public')->path($image);

            if (!file_exists($absolutePath)) {
                throw new RuntimeException('Imaginea sursă nu există.');
            }

            $imageStream = fopen($absolutePath, 'r');
            $imageName = basename($absolutePath);
            $imageMime = mime_content_type($absolutePath) ?: 'image/png';
        }

        $response = Http::withToken($apiKey)
            ->attach('image', $imageStream, $imageName, ['Content-Type' => $imageMime])
            ->asMultipart()
            ->post('https://api.openai.com/v1/images/edits', [
                ['name' => 'model', 'contents' => $model],
                ['name' => 'prompt', 'contents' => $prompt],
                ['name' => 'size', 'contents' => '1536x1024'],
                ['name' => 'quality', 'contents' => 'medium'],
                ['name' => 'output_format', 'contents' => 'png'],
                ['name' => 'background', 'contents' => 'opaque'],
            ]);

        if (is_resource($imageStream)) {
            fclose($imageStream);
        }

        if (!$response->successful()) {
            throw new RuntimeException(
                'OpenAI error: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $b64 = data_get($response->json(), 'data.0.b64_json');

        if (!$b64) {
            throw new RuntimeException('Răspuns invalid de la OpenAI.');
        }

        $binary = base64_decode($b64, true);

        if ($binary === false) {
            throw new RuntimeException('Nu s-a putut decoda imaginea generată.');
        }

        $tmpPath = 'products/banners/tmp/' . Str::uuid() . '.png';
        Storage::disk('public')->put($tmpPath, $binary);

        return $tmpPath;
    }

    public function moveTempBannerToFinal(?string $tmpPath): ?string
    {
        if (!$tmpPath) {
            return null;
        }

        if (!Storage::disk('public')->exists($tmpPath)) {
            return null;
        }

        $finalPath = 'products/banners/' . Str::uuid() . '.png';
        Storage::disk('public')->move($tmpPath, $finalPath);

        return $finalPath;
    }

    public function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function buildBannerPrompt(string $userPrompt): string
    {
        return trim("
Creează un banner promoțional premium de marketplace pentru acest produs folosind imaginea trimisă ca referință principală.
Păstrează produsul recognoscibil și în centru.
Compoziție modernă, clean, high-end, comercială.
Aspect landscape 3:2.
Fără watermark.
Fără text lung inutil.
Evidențiază produsul clar, cu fundal profesional și iluminare bună.
Instrucțiuni suplimentare de la seller:
{$userPrompt}
");
    }
}