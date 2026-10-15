<?php
namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class XenditService
{
    public function createOrRecover(array $credentials, array $payload, bool $recover = false): array
    {
        $key = $credentials['secret_key'] ?? '';
        if (!$key) throw new RuntimeException('Xendit belum dikonfigurasi oleh admin.');
        if ($recover && ($invoice = $this->find($key, $payload))) return $invoice;
        try {
            $response = Http::withBasicAuth($key, '')->acceptJson()->withOptions(['connect_timeout'=>5])->timeout(15)
                ->post('https://api.xendit.co/v2/invoices', $payload);
        } catch (ConnectionException $e) {
            // The provider may have created the invoice even if its response was lost.
            if ($invoice = $this->find($key, $payload)) return $invoice;
            throw new RuntimeException('Koneksi ke Xendit terputus. Coba lanjutkan pembayaran kembali untuk memeriksa invoice yang sama.');
        }
        if (!$response->successful()) {
            if (in_array($response->json('error_code'), ['DUPLICATE_INVOICE_ERROR','DUPLICATE_EXTERNAL_ID']) && ($invoice = $this->find($key, $payload))) return $invoice;
            throw new RuntimeException('Xendit belum dapat memproses pembayaran. Periksa konfigurasi gateway di admin atau pilih metode lain.');
        }
        return $this->validate($response->json() ?? [], $payload);
    }

    private function find(string $key, array $payload): ?array
    {
        try {
            $response = Http::withBasicAuth($key, '')->acceptJson()->withOptions(['connect_timeout'=>3])->timeout(6)
                ->get('https://api.xendit.co/v2/invoices', ['external_id'=>$payload['external_id'],'limit'=>10]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Status invoice Xendit belum dapat diperiksa. Coba lanjutkan pembayaran kembali.');
        }
        if (!$response->successful()) throw new RuntimeException('Invoice Xendit belum dapat diperiksa. Periksa akses API gateway di admin.');
        foreach ($response->json() ?? [] as $invoice) {
            if (!is_array($invoice) || ($invoice['external_id'] ?? '') !== $payload['external_id']) continue;
            if (($invoice['status'] ?? '') === 'EXPIRED') throw new RuntimeException('Invoice Xendit sudah kedaluwarsa. Hubungi admin untuk melanjutkan pembayaran.');
            return $this->validate($invoice, $payload);
        }
        return null;
    }

    private function validate(array $invoice, array $payload): array
    {
        $url = $invoice['invoice_url'] ?? '';
        if (empty($invoice['id']) || ($invoice['external_id'] ?? '') !== $payload['external_id'] || (float)($invoice['amount'] ?? -1) !== (float)$payload['amount'] || ($invoice['currency'] ?? 'IDR') !== 'IDR' || parse_url($url, PHP_URL_SCHEME) !== 'https' || !parse_url($url, PHP_URL_HOST)) {
            throw new RuntimeException('Xendit belum memberikan invoice pembayaran yang valid.');
        }
        return $invoice;
    }
}
