<?php

namespace App\Services\Maib;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;




class MaibService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.maib.base_url', 'https://api.maibmerchants.md'), '/');
    }

    public function token(): string
    {
        return Cache::remember('maib_access_token', now()->addSeconds(240), function () {
            $res = Http::asJson()
                ->timeout(30)
                ->post($this->baseUrl . '/v1/generate-token', [
                    'projectId' => config('services.maib.project_id'),
                    'projectSecret' => config('services.maib.project_secret'),
                ]);

            $res->throw();

            $json = $res->json();
            $token = data_get($json, 'result.accessToken');
            if (!$token) {
                throw new \RuntimeException('MAIB: lipsă accessToken');
            }

            return (string) $token;
        });
    }



    public function createPayment(array $payload): array
    {
        try {
            $res = Http::asJson()
                ->timeout(30)
                ->withToken($this->token())
                ->post($this->baseUrl . '/v1/pay', $payload);

            $res->throw();
            return (array) $res->json();

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // dacă tokenul a expirat, îl ștergem și încercăm o singură dată din nou
            if (($e->response?->status() === 401) && str_contains((string)($e->response?->header('WWW-Authenticate')), 'expired')) {
                Cache::forget('maib_access_token');

                $res2 = Http::asJson()
                    ->timeout(30)
                    ->withToken($this->token())
                    ->post($this->baseUrl . '/v1/pay', $payload);

                $res2->throw();
                return (array) $res2->json();
            }

            throw $e;
        }
    }


    public function paymentInfo(string $payId): array
    {
        $res = Http::timeout(30)
            ->withToken($this->token())
            ->get($this->baseUrl . '/v1/pay-info/' . urlencode($payId));
    
        $res->throw();
    
        return (array) $res->json();
    }
    
    public function paymentInfoSafe(string $payId): ?array
    {
        try {
            return $this->paymentInfo($payId);
        } catch (\Throwable $e) {
            Log::error('MAIB pay-info error', ['pay_id' => $payId, 'err' => $e->getMessage()]);
            return null;
        }
    }

    
    public function refund(string $payId, float $amount, string $reason = 'Refund'): array
    {
        // MAIB API nu folosește "reason", dar îl păstrăm ca parametru
        // fiindcă RefundController îl trimite.
        $payload = [
            'payId' => $payId,
            'refundAmount' => round($amount, 2), // exact cum cere MAIB
        ];
    
        try {
            $res = Http::asJson()
                ->timeout(30)
                ->withToken($this->token())
                ->post($this->baseUrl . '/v1/refund', $payload);
    
            $res->throw();
            $json = (array) $res->json();
    
            // Uneori vine 200 dar ok=false
            if (data_get($json, 'ok') === false) {
                $err = json_encode(data_get($json, 'errors', []), JSON_UNESCAPED_UNICODE);
                throw new \RuntimeException('MAIB refund rejected: ' . $err);
            }
    
            // Log extra (opțional, dar util)
            Log::info('MAIB REFUND OK', [
                'pay_id' => $payId,
                'amount' => $amount,
                'reason' => $reason,
            ]);
    
            return $json;
    
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // retry 1x dacă token expirat
            if (($e->response?->status() === 401) &&
                str_contains((string)($e->response?->header('WWW-Authenticate')), 'expired')) {
    
                Cache::forget('maib_access_token');
    
                $res2 = Http::asJson()
                    ->timeout(30)
                    ->withToken($this->token())
                    ->post($this->baseUrl . '/v1/refund', $payload);
    
                $res2->throw();
                $json2 = (array) $res2->json();
    
                if (data_get($json2, 'ok') === false) {
                    $err = json_encode(data_get($json2, 'errors', []), JSON_UNESCAPED_UNICODE);
                    throw new \RuntimeException('MAIB refund rejected: ' . $err);
                }
    
                return $json2;
            }
    
            throw $e;
        }
    }

    

}
