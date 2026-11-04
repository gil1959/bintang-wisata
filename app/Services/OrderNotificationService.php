<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Mail\OrderVerificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function sendVerificationEmail(Order $order, string $result)
    {
        if ($order->user_id) {
            $payload = [
                'title' => 'Status pesanan',
                'body' => 'Status pesanan ' . $order->invoice_number . ': ' . $result,
            ];
            \App\Services\Mobile\FirebasePush::dispatch((int)$order->user_id, $payload);
            \App\Services\Mobile\OneSignalPush::dispatch((int)$order->user_id, $payload);
        }
        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderVerificationMail($order, $result, false));
            }

            $adminEmail = Setting::invoiceAdminEmail();
            if (!empty($adminEmail)) {
                Mail::to($adminEmail)->send(new OrderVerificationMail($order, $result, true));
            }

            $payoutService = app(\App\Services\PartnerPayoutService::class);
            $partnerId = $payoutService->resolvePartnerIdFromOrder($order);
            if ($partnerId) {
                $partner = User::find($partnerId);
                if ($partner && $partner->email !== $order->customer_email) {
                    $partnerPayload = [
                        'title' => 'Status pesanan partner',
                        'body' => 'Status pesanan ' . $order->invoice_number . ': ' . $result,
                    ];
                    \App\Services\Mobile\FirebasePush::dispatch((int)$partner->id, $partnerPayload);
                    \App\Services\Mobile\OneSignalPush::dispatch((int)$partner->id, $partnerPayload);
                    Mail::to($partner->email)->send(new OrderVerificationMail($order, $result, true));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Email verifikasi (' . $result . ') gagal dikirim via Webhook', [
                'invoice' => $order->invoice_number,
                'err' => $e->getMessage(),
            ]);
        }
    }
}
