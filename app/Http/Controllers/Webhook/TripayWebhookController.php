<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $gateway = PaymentGateway::where('name', 'tripay')->first();

        // Jangan blok webhook cuma karena gateway inactive.
        // Ini untuk lolos verifikasi callback + cegah retry spam.
        if (!$gateway || !$gateway->is_active) {
            Log::warning('Tripay webhook received but gateway inactive', [
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);

            return response()->json(['message' => 'OK'], 200);
        }

        $credentials = $gateway->credentials ?? [];
        $privateKey = $credentials['private_key'] ?? null;
        if (!$privateKey) {
            return response()->json(['message' => 'missing private_key'], 400);
        }

        $event = (string) $request->header('X-Callback-Event');
        if ($event !== '' && $event !== 'payment_status') {
            return response()->json(['message' => 'ignored event'], 200);
        }

        $raw = $request->getContent();
        $sig = (string) $request->header('X-Callback-Signature');

        $expected = hash_hmac('sha256', $raw, $privateKey);
        if ($sig === '' || !hash_equals($expected, $sig)) {
            // 403 lebih cocok untuk webhook
            return response()->json(['message' => 'invalid signature'], 403);
        }

        $data = $request->input('data');
        if (!$data || !is_array($data)) {
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $reference = $data['reference'] ?? null;
        $status = strtoupper((string) ($data['status'] ?? ''));

        if (!$reference) {
            return response()->json(['message' => 'missing reference'], 400);
        }

        $payment = Payment::where('gateway_name', 'tripay')
            ->where('gateway_reference', $reference)
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        $newPaymentStatus = match ($status) {
            'PAID', 'SUCCESS' => 'paid',
            'FAILED', 'EXPIRED' => 'failed',
            default => 'waiting_payment',
        };

        $payment->status = $newPaymentStatus;
        $payment->gateway_payload = $request->all();
        $payment->save();

        if ($payment->order) {
            if ($newPaymentStatus === 'paid') {
                $payment->order->payment_status = 'paid';
                $payment->order->order_status = 'approved';
            } elseif ($newPaymentStatus === 'failed') {
                $payment->order->payment_status = 'failed';
                $payment->order->order_status = 'rejected';
            } else {
                $payment->order->payment_status = 'waiting_payment';
            }
            $payment->order->save();
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
