<?php

namespace App\Services\Mobile;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalPush
{
    public static function dispatch(int $userId, array $payload): void
    {
        if (!config('mobile.onesignal_app_id') || !config('mobile.onesignal_rest_api_key')) {
            return;
        }

        try {
            app(self::class)->sendToUser($userId, $payload);
        } catch (\Throwable $e) {
            Log::warning('OneSignal push dispatch failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'payload' => $payload,
            ]);
        }
    }

    public static function dispatchBroadcast(array $payload): void
    {
        if (!config('mobile.onesignal_app_id') || !config('mobile.onesignal_rest_api_key')) {
            return;
        }

        try {
            app(self::class)->sendBroadcast($payload);
        } catch (\Throwable $e) {
            Log::warning('OneSignal broadcast failed: ' . $e->getMessage(), [
                'payload' => $payload,
            ]);
        }
    }

    public function sendToUser(int $userId, array $payload): bool
    {
        $appId = config('mobile.onesignal_app_id');
        $apiKey = config('mobile.onesignal_rest_api_key');

        if (!$appId || !$apiKey) {
            return false;
        }

        $body = [
            'app_id' => $appId,
            'target_channel' => 'push',
            'include_aliases' => [
                'external_id' => [(string) $userId],
            ],
            'headings' => [
                'en' => (string) ($payload['title'] ?? 'Bintang Wisata'),
            ],
            'contents' => [
                'en' => (string) ($payload['body'] ?? ''),
            ],
            'data' => array_merge($payload, [
                'user_id' => (string) $userId,
            ]),
        ];

        if (!empty($payload['url'])) {
            $body['url'] = $payload['url'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(15)->post('https://onesignal.com/api/v1/notifications', $body);

        if ($response->failed()) {
            Log::warning('OneSignal sendToUser failed HTTP ' . $response->status(), [
                'user_id' => $userId,
                'response' => $response->json() ?? $response->body(),
            ]);
            return false;
        }

        return true;
    }

    public function sendBroadcast(array $payload): bool
    {
        $appId = config('mobile.onesignal_app_id');
        $apiKey = config('mobile.onesignal_rest_api_key');

        if (!$appId || !$apiKey) {
            return false;
        }

        $body = [
            'app_id' => $appId,
            'included_segments' => ['Total Subscriptions'],
            'headings' => [
                'en' => (string) ($payload['title'] ?? 'Bintang Wisata'),
            ],
            'contents' => [
                'en' => (string) ($payload['body'] ?? ''),
            ],
            'data' => $payload,
        ];

        if (!empty($payload['url'])) {
            $body['url'] = $payload['url'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(15)->post('https://onesignal.com/api/v1/notifications', $body);

        if ($response->failed()) {
            Log::warning('OneSignal sendBroadcast failed HTTP ' . $response->status(), [
                'response' => $response->json() ?? $response->body(),
            ]);
            return false;
        }

        return true;
    }
}

