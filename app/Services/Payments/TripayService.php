<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class TripayService
{
    public function fetchChannels(array $credentials): array
    {
        $mode = strtolower(trim((string)($credentials['mode'] ?? 'sandbox')));

        $apiKey = $credentials['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('TriPay api_key belum diisi.');
        }

        $base = $mode === 'production'
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';

        $resp = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])
            ->get($base . '/merchant/payment-channel');


        if (!$resp->ok()) {
            throw new \RuntimeException('Gagal ambil channel TriPay: ' . $resp->body());
        }

        $json = $resp->json();

        // ✅ kalau TriPay balikin success=false tapi HTTP 200
        if (isset($json['success']) && $json['success'] === false) {
            $msg = $json['message'] ?? 'Unknown error';
            throw new \RuntimeException("TriPay response gagal: {$msg}");
        }

        // ✅ fallback kalau struktur beda
        $data = $json['data'] ?? [];
        if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        // fallback kalau ternyata list-nya ada di key lain
        if (is_array($data) && isset($data['payment_channels']) && is_array($data['payment_channels'])) {
            $data = $data['payment_channels'];
        }

        // fallback kalau json langsung list (jarang tapi possible)
        if ($data === [] && is_array($json) && isset($json[0])) {
            $data = $json;
        }


        // ✅ kalau masih kosong, lempar error dengan konteks message
        if (!is_array($data) || count($data) === 0) {
            $msg = $json['message'] ?? 'data kosong';
            $snippet = substr($resp->body(), 0, 800); // ambil 800 char pertama
            throw new \RuntimeException("TriPay channel kosong ({$msg}). Response: {$snippet}");
        }


        // Normalisasi agar frontend gampang render
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
        $mode = strtolower(trim((string)($credentials['mode'] ?? 'sandbox')));

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
