<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ambil gateway xendit
        $gateway = PaymentGateway::where('name', 'xendit')->first();

        if (!$gateway) {
            Log::error('Xendit webhook: gateway not found');
            return response()->json(['error' => 'Gateway not found'], 404);
        }

        // validasi callback token
        $callbackToken = $request->header('x-callback-token');
        if (!$callbackToken || $callbackToken !== ($gateway->credentials['callback_token'] ?? null)) {
            Log::warning('Xendit webhook: invalid callback token');
            return response()->json(['error' => 'Invalid token'], 403);
        }

        // payload utama
        $data = $request->all();

        Log::info('Xendit webhook payload', $data);

        // status sukses
        if (
            isset($data['status']) &&
            in_array($data['status'], ['PAID', 'SETTLED'])
        ) {
            $payment = Payment::where('reference', $data['id'])->first();

            if (!$payment) {
                Log::error('Xendit webhook: payment not found', ['reference' => $data['id']]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // update payment
            $payment->status = 'paid';
            $payment->save();

            // update order
            if ($payment->order) {
                $payment->order->payment_status = 'paid';
                $payment->order->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
