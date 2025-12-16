<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class TripayService
{
    public function fetchChannels(array $credentials): array
    {
        $mode = $credentials['mode'] ?? 'sandbox';
        $apiKey = $credentials['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('TriPay api_key belum diisi.');
        }

        $base = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->get($base . '/merchant/payment-channel');

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal ambil channel TriPay: ' . $resp->body());
        }

        $json = $resp->json();
        $data = $json['data'] ?? [];

        // Normalisasi agar frontend gampang render
        // channel_code = $item['code']
        return array_values(array_map(function ($item) {
            return [
                'channel_code' => $item['code'] ?? null,
                'name' => $item['name'] ?? ($item['code'] ?? 'UNKNOWN'),
                'group' => $item['group'] ?? null,
                'icon_url' => $item['icon_url'] ?? null,
                'fee_customer' => $item['fee_customer'] ?? null,
                'active' => (bool)($item['active'] ?? true),
            ];
        }, $data));
    }

    public function createTransaction(array $credentials, array $payload): array
    {
        $mode = $credentials['mode'] ?? 'sandbox';
        $apiKey = $credentials['api_key'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;
        $merchantCode = $credentials['merchant_code'] ?? null;

        if (!$apiKey || !$privateKey || !$merchantCode) {
            throw new \RuntimeException('TriPay credential kurang: api_key/private_key/merchant_code wajib.');
        }

        $base = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';

        $merchantRef = $payload['merchant_ref'] ?? null;
        $amount = $payload['amount'] ?? null;

        if (!$merchantRef || !$amount) {
            throw new \RuntimeException('Payload TriPay invalid: merchant_ref dan amount wajib.');
        }

        $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $amount, $privateKey);

        $resp = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->asForm()->post($base . '/transaction/create', array_merge($payload, [
            'merchant_code' => $merchantCode,
            'signature' => $signature,
        ]));

        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal create transaksi TriPay: ' . $resp->body());
        }

        return $resp->json();
    }
}
