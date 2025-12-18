<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class IpaymuWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('iPaymu webhook payload', $data);

        /**
         * Status iPaymu:
         * Status = 1  => sukses
         */
        if (
            isset($data['Status']) &&
            (int) $data['Status'] === 1 &&
            isset($data['SessionID'])
        ) {
            $payment = Payment::where('reference', $data['SessionID'])->first();

            if (!$payment) {
                Log::error('iPaymu webhook: payment not found', [
                    'reference' => $data['SessionID']
                ]);
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
