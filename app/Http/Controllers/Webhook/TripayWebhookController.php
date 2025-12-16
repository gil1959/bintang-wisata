<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class TripayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $gateway = PaymentGateway::where('name', 'tripay')->first();
        if (!$gateway || !$gateway->is_active) {
            return response()->json(['message' => 'gateway inactive'], 403);
        }

        $credentials = $gateway->credentials ?? [];
        $privateKey = $credentials['private_key'] ?? null;
        if (!$privateKey) {
            return response()->json(['message' => 'missing private_key'], 400);
        }

        // (optional) event guard
        // Tripay biasanya: payment_status
        $event = (string) $request->header('X-Callback-Event');
        if ($event !== '' && $event !== 'payment_status') {
            // gak error, cuma ignore supaya gak ganggu
            return response()->json(['message' => 'ignored event'], 200);
        }

        // Verify signature
        $raw = $request->getContent();
        $sig = (string) $request->header('X-Callback-Signature');

        $expected = hash_hmac('sha256', $raw, $privateKey);
        if ($sig === '' || !hash_equals($expected, $sig)) {
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $data = $request->input('data');
        if (!$data || !is_array($data)) {
            return response()->json(['message' => 'invalid payload'], 400);
        }

        $reference = $data['reference'] ?? null;
        $status = strtoupper((string)($data['status'] ?? ''));

        if (!$reference) {
            return response()->json(['message' => 'missing reference'], 400);
        }

        // Cari payment yg bener (Tripay reference dari createTransaction)
        $payment = Payment::where('gateway_name', 'tripay')
            ->where('gateway_reference', $reference)
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        // Map ke enum DB lu
        $newPaymentStatus = match ($status) {
            'PAID', 'SUCCESS' => 'paid',
            'FAILED', 'EXPIRED' => 'failed',
            default => 'waiting_payment',
        };

        $payment->status = $newPaymentStatus;
        $payment->gateway_payload = $request->all();
        $payment->save();

        // Gateway auto approve/reject
        if ($payment->order) {
            if ($newPaymentStatus === 'paid') {
                $payment->order->payment_status = 'paid';
                $payment->order->order_status = 'approved';
            } elseif ($newPaymentStatus === 'failed') {
                $payment->order->payment_status = 'failed';
                $payment->order->order_status = 'rejected';
            } else {
                $payment->order->payment_status = 'waiting_payment';
                // order_status biarin
            }
            $payment->order->save();
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
