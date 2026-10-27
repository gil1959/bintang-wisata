<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\GoogleIndexingLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleIndexingController extends Controller
{
    /**
     * Tampilkan halaman utama Google Indexing API Bulk Manager
     */
    public function index()
    {
        $today = Carbon::today();

        // Statistik kuota hari ini (maksimal 200 request per hari dari Google)
        $quotaLimit = 200;
        $quotaUsed = 0;
        $totalSubmitted = 0;
        $totalSuccess = 0;
        $totalFailed = 0;

        try {
            $quotaUsed = GoogleIndexingLog::whereDate('created_at', $today)->count();
            $totalSubmitted = GoogleIndexingLog::count();
            $totalSuccess = GoogleIndexingLog::where('status_code', 200)->count();
            $totalFailed = GoogleIndexingLog::where('status_code', '!=', 200)->count();
        } catch (\Throwable $e) {
            // Safe fallback
        }

        $successRate = $totalSubmitted > 0 ? round(($totalSuccess / $totalSubmitted) * 100) : 0;

        $stats = [
            'total' => $totalSubmitted,
            'success' => $totalSuccess,
            'failed' => $totalFailed,
            'success_rate' => $successRate,
            'quota_used' => $quotaUsed,
            'quota_limit' => $quotaLimit,
            'quota_remaining' => max(0, $quotaLimit - $quotaUsed),
        ];

        // 7 Hari Terakhir Chart Data
        $chartLabels = [];
        $chartSuccess = [];
        $chartFailed = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $dateStr = $date->toDateString();

            try {
                $chartSuccess[] = GoogleIndexingLog::whereDate('created_at', $dateStr)
                    ->where('status_code', 200)
                    ->count();

                $chartFailed[] = GoogleIndexingLog::whereDate('created_at', $dateStr)
                    ->where('status_code', '!=', 200)
                    ->count();
            } catch (\Throwable $e) {
                $chartSuccess[] = 0;
                $chartFailed[] = 0;
            }
        }

        // 50 Log Terakhir
        $logs = collect();
        try {
            $logs = GoogleIndexingLog::latest()->take(50)->get();
        } catch (\Throwable $e) {
            // Safe fallback
        }

        // Info Service Account JSON jika ada
        $jsonPath = storage_path('app/google/service-account.json');
        $hasJson = file_exists($jsonPath);
        $serviceAccountEmail = null;
        $projectId = null;

        if ($hasJson) {
            try {
                $jsonContent = json_decode(file_get_contents($jsonPath), true);
                $serviceAccountEmail = $jsonContent['client_email'] ?? null;
                $projectId = $jsonContent['project_id'] ?? null;
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }

        $authMode = Setting::getValue('g_index_auth_mode', 'json');

        return view('admin.google_indexing.index', compact(
            'stats',
            'chartLabels',
            'chartSuccess',
            'chartFailed',
            'logs',
            'hasJson',
            'serviceAccountEmail',
            'projectId',
            'authMode'
        ));
    }

    /**
     * Upload Service Account JSON Key
     */
    public function uploadJson(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|max:2048'
        ]);

        $file = $request->file('json_file');
        $content = file_get_contents($file->getRealPath());
        $data = json_decode($content, true);

        if (!$data || empty($data['client_email']) || empty($data['private_key'])) {
            return redirect()->back()->with('error', 'File JSON tidak valid. Pastikan file berisi client email dan private key Google Cloud Service Account.');
        }

        $dir = storage_path('app/google');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($dir . '/service-account.json', $content);

        Setting::setValue('g_index_auth_mode', 'json');
        Setting::setValue('g_index_sa_email', $data['client_email'] ?? '');
        Setting::setValue('g_index_sa_project', $data['project_id'] ?? '');

        return redirect()->back()->with('success', 'File JSON Service Account berhasil diunggah dan diaktifkan.');
    }

    /**
     * Submit URL ke Google Indexing API
     */
    public function submitUrls(Request $request)
    {
        $request->validate([
            'urls' => 'required|string',
            'action_type' => 'required|in:URL_UPDATED,URL_DELETED'
        ]);

        // Cek kuota harian 200 request
        $todayUsed = GoogleIndexingLog::whereDate('created_at', Carbon::today())->count();
        if ($todayUsed >= 200) {
            return response()->json([
                'success' => false,
                'status_code' => 429,
                'message' => 'Batas kuota harian Google Indexing API yaitu 200 request telah tercapai untuk hari ini.'
            ], 429);
        }

        // Dapatkan Access Token
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Autentikasi gagal. File JSON Service Account belum diunggah atau tidak valid.'
            ], 401);
        }

        $rawUrls = explode("\n", str_replace("\r", "", $request->urls));
        $urls = array_values(array_filter(array_map('trim', $rawUrls), function ($u) {
            return strlen($u) > 5 && filter_var($u, FILTER_VALIDATE_URL);
        }));

        if (empty($urls)) {
            return response()->json([
                'success' => false,
                'status_code' => 422,
                'message' => 'Tidak ditemukan URL yang valid. Pastikan format URL menyertakan http atau https.'
            ], 422);
        }

        // Batasi maksimal 10 URL jika dikirim sekaligus via request ini
        $urlToProcess = $urls[0];
        $actionType = $request->action_type;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(15)->post('https://indexing.googleapis.com/v3/urlNotifications:publish', [
                'url' => $urlToProcess,
                'type' => $actionType
            ]);

            $statusCode = $response->status();
            $body = $response->json();

            $isSuccess = $response->successful();
            $message = 'Sukses';

            if (!$isSuccess) {
                if (isset($body['error']['message'])) {
                    $message = $body['error']['message'];
                } else {
                    $message = 'Gagal dengan kode status ' . $statusCode;
                }

                if ($statusCode === 403) {
                    $message = 'Izin ditolak (403). Pastikan email Service Account sudah ditambahkan sebagai Pemilik pada Google Search Console properti ini.';
                }
            }

            GoogleIndexingLog::create([
                'url' => $urlToProcess,
                'action_type' => $actionType,
                'status_code' => $statusCode,
                'response_message' => $message
            ]);

            $newQuotaUsed = GoogleIndexingLog::whereDate('created_at', Carbon::today())->count();

            return response()->json([
                'success' => $isSuccess,
                'status_code' => $statusCode,
                'url' => $urlToProcess,
                'message' => $message,
                'quota_remaining' => max(0, 200 - $newQuotaUsed)
            ]);

        } catch (\Throwable $e) {
            $msg = 'Terjadi kesalahan koneksi ke server Google: ' . $e->getMessage();
            GoogleIndexingLog::create([
                'url' => $urlToProcess,
                'action_type' => $actionType,
                'status_code' => 500,
                'response_message' => $msg
            ]);

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'url' => $urlToProcess,
                'message' => $msg
            ], 500);
        }
    }

    /**
     * Dapatkan Google Access Token (Mendukung Service Account JSON dan OAuth)
     */
    private function getAccessToken(): ?string
    {
        $mode = Setting::getValue('g_index_auth_mode', 'json');

        if ($mode === 'json') {
            $jsonPath = storage_path('app/google/service-account.json');
            if (!file_exists($jsonPath)) {
                return null;
            }

            try {
                if (class_exists(\Google\Client::class)) {
                    $client = new \Google\Client();
                    $client->setAuthConfig($jsonPath);
                    $client->addScope('https://www.googleapis.com/auth/indexing');
                    $token = $client->fetchAccessTokenWithAssertion();
                    if (!empty($token['access_token'])) {
                        return $token['access_token'];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google Client fetch token assertion failed: ' . $e->getMessage());
            }

            // Fallback: Direct JWT pembuatan token
            try {
                $keyData = json_decode(file_get_contents($jsonPath), true);
                if (empty($keyData['client_email']) || empty($keyData['private_key'])) {
                    return null;
                }

                $now = time();
                $header = ['alg' => 'RS256', 'typ' => 'JWT'];
                $payload = [
                    'iss' => $keyData['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/indexing',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'exp' => $now + 3600,
                    'iat' => $now
                ];

                $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
                $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
                $signatureData = $base64Header . '.' . $base64Payload;

                $privateKey = openssl_pkey_get_private($keyData['private_key']);
                if ($privateKey && openssl_sign($signatureData, $binarySignature, $privateKey, OPENSSL_ALGO_SHA256)) {
                    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($binarySignature));
                    $jwt = $signatureData . '.' . $base64Signature;

                    $res = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion' => $jwt
                    ]);

                    if ($res->successful() && !empty($res->json('access_token'))) {
                        return $res->json('access_token');
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Direct JWT generation failed: ' . $e->getMessage());
            }
        } else {
            // OAuth mode
            $tokenJson = Setting::getValue('g_index_access_token');
            if ($tokenJson) {
                $token = json_decode($tokenJson, true);
                if (!empty($token['access_token'])) {
                    return $token['access_token'];
                }
            }
        }

        return null;
    }

    /**
     * Redirect ke halaman Google OAuth
     */
    public function oauthRedirect()
    {
        $clientId = Setting::getValue('google_client_id');
        $clientSecret = Setting::getValue('google_client_secret');

        if (!$clientId || !$clientSecret) {
            return redirect()->back()->with('error', 'Kredensial Google Client ID dan Secret belum dikonfigurasi pada menu pengaturan.');
        }

        $redirectUri = route('admin.google-indexing.callback');
        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/indexing',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    /**
     * Callback dari Google OAuth
     */
    public function oauthCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('admin.google-indexing.index')->with('error', 'Gagal menghubungkan OAuth: ' . $request->error);
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('admin.google-indexing.index')->with('error', 'Kode autentikasi OAuth tidak ditemukan.');
        }

        $clientId = Setting::getValue('google_client_id');
        $clientSecret = Setting::getValue('google_client_secret');
        $redirectUri = route('admin.google-indexing.callback');

        try {
            $res = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code'
            ]);

            if ($res->successful()) {
                $tokenData = $res->json();
                Setting::setValue('g_index_access_token', json_encode($tokenData));
                Setting::setValue('g_index_auth_mode', 'oauth');

                return redirect()->route('admin.google-indexing.index')->with('success', 'Berhasil menghubungkan Google Search Console via OAuth.');
            }

            return redirect()->route('admin.google-indexing.index')->with('error', 'Gagal menukar kode otorisasi Google: ' . $res->body());
        } catch (\Throwable $e) {
            return redirect()->route('admin.google-indexing.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
