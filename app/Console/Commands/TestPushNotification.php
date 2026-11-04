<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Mobile\OneSignalPush;
use App\Services\Mobile\FirebasePush;

class TestPushNotification extends Command
{
    protected $signature = 'push:test {user_id? : ID user yang ingin dikirimi notifikasi} {--broadcast : Kirim broadcast ke semua subscriber}';
    protected $description = 'Kirim notifikasi pengujian OneSignal dan Firebase';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $isBroadcast = $this->option('broadcast');

        $payload = [
            'title' => 'Tes Notifikasi Bintang Wisata',
            'body' => 'Notifikasi ini dikirim via sistem push Bintang Wisata pada ' . date('d M Y H:i:s'),
            'url' => 'https://bintangwisataholiday.com/user/notifications',
        ];

        $this->info("Menjalankan pengujian push notification...");

        if ($isBroadcast) {
            $this->line("Mode: Broadcast (Semua Subscriber OneSignal)");
            $result = app(OneSignalPush::class)->sendBroadcast($payload);
            $this->info($result ? "✅ OneSignal broadcast berhasil dikirim!" : "❌ OneSignal broadcast gagal.");
            return 0;
        }

        if (!$userId) {
            $this->error("Masukkan user_id, contoh: php artisan push:test 1 (atau gunakan opsi --broadcast)");
            return 1;
        }

        $userId = (int) $userId;
        $this->line("Target User ID: {$userId}");

        // Test OneSignal
        $this->line("1. Mengirim via OneSignal REST API...");
        $osResult = app(OneSignalPush::class)->sendToUser($userId, $payload);
        $this->info($osResult ? "   ✅ OneSignal: Berhasil dikirim ke external_id '{$userId}'" : "   ❌ OneSignal: Gagal dikirim (cek storage/logs/laravel.log)");

        // Test Firebase
        $this->line("2. Mengirim via Firebase FCM v1...");
        try {
            app(FirebasePush::class)->send($userId, $payload);
            $this->info("   ✅ Firebase FCM: Berhasil diproses");
        } catch (\Throwable $e) {
            $this->warn("   ⚠️ Firebase FCM: " . $e->getMessage());
        }

        $this->info("Pengujian selesai.");
        return 0;
    }
}

