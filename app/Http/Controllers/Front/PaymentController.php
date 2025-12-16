<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Services\Payments\TripayService;
use App\Services\Payments\DokuService;
use App\Services\Payments\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Router lama biar gak ngerusak link existing
    public function show(Order $order)
    {
        if (!$order->payment_method) {
            return redirect()->route('checkout.show', $order);
        }

        if (str_starts_with($order->payment_method, 'manual:')) {
            return redirect()->route('payment.manual.page', $order);
        }

        if (str_starts_with($order->payment_method, 'gateway:')) {
            return redirect()->route('payment.gateway.page', $order);
        }

        abort(400, 'Metode pembayaran tidak dikenal.');
    }

    // ===== PAGE MANUAL =====
    public function manualPage(Order $order)
    {
        if (strpos($order->payment_method, 'manual:') !== 0) {
            abort(403);
        }

        [, $id] = explode(':', $order->payment_method, 2);

        $manualMethod = PaymentMethod::where('id', (int)$id)
            ->where('type', 'manual')
            ->where('is_active', 1)
            ->firstOrFail();

        return view('front.payment.manual', compact('order', 'manualMethod'));
    }

    // ===== PAGE GATEWAY =====
    public function gatewayPage(Order $order)
    {
        if (strpos($order->payment_method, 'gateway:') !== 0) {
            abort(403);
        }

        $parts = explode(':', $order->payment_method, 3);
        if (count($parts) !== 3) abort(400, 'Format gateway invalid');

        [, $gatewayName, $channelCode] = $parts;

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', 1)
            ->firstOrFail();

        $gatewayMethodLabel = $channelCode;
        $channels = is_array($gateway->channels) ? $gateway->channels : [];
        $found = false;

        foreach ($channels as $ch) {
            if (($ch['channel_code'] ?? null) === $channelCode) {
                $gatewayMethodLabel = $ch['name'] ?? $channelCode;
                $found = true;
                break;
            }
        }

        if (!$found) abort(400, 'Channel gateway tidak valid.');

        return view('front.payment.gateway', compact('order', 'gateway', 'channelCode', 'gatewayMethodLabel'));
    }

    // SUBMIT MANUAL
    public function submitManual(Request $request, Order $order)
    {
        if (strpos($order->payment_method, 'manual:') !== 0) {
            abort(403);
        }

        $request->validate([
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($order->payments()->whereIn('status', ['waiting_verification', 'paid'])->exists()) {
            return redirect()
                ->route('payment.waiting', $order)
                ->with('error', 'Pembayaran sudah dikirim. Tunggu verifikasi admin.');
        }

        $path = $request->file('proof')->store('payments', 'public');

        $order->payments()->create([
            'method'            => 'manual',
            'amount'            => $order->final_price,
            'proof_image'       => $path,
            'gateway_reference' => null,
            'status'            => 'waiting_verification',
        ]);

        $order->update([
            'payment_status' => 'waiting_verification',
        ]);

        return redirect()
            ->route('payment.waiting', $order)
            ->with('success', 'Bukti transfer berhasil diupload. Pesanan menunggu verifikasi admin.');
    }

    public function waiting(Order $order)
    {
        return view('front.payment.waiting', compact('order'));
    }

    // ✅ START GATEWAY (beneran call API + redirect)
    public function startGateway(
        Request $request,
        Order $order,
        TripayService $tripay,
        DokuService $doku,
        MidtransService $midtrans
    ) {
        if (strpos($order->payment_method, 'gateway:') !== 0) {
            abort(403);
        }

        $parts = explode(':', $order->payment_method, 3);
        if (count($parts) !== 3) abort(400, 'Format gateway invalid');

        [, $gatewayName, $channelCode] = $parts;

        if (!in_array($gatewayName, ['doku', 'tripay', 'midtrans'], true)) {
            abort(400, 'Gateway tidak didukung');
        }

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', 1)
            ->firstOrFail();

        // Anti-tamper channel
        $channels = is_array($gateway->channels) ? $gateway->channels : [];
        $valid = false;
        foreach ($channels as $ch) {
            if (($ch['channel_code'] ?? null) === $channelCode) {
                $valid = true;
                break;
            }
        }
        if (!$valid) abort(400, 'Channel gateway tidak valid');

        // prevent double click
        $payment = $order->payments()
            ->where('method', 'gateway')
            ->where('status', 'waiting_payment')
            ->latest()
            ->first();

        if (!$payment) {
            $payment = $order->payments()->create([
                'method'            => 'gateway',
                'amount'            => $order->final_price,
                'proof_image'       => null,
                'gateway_name'      => $gatewayName,
                'channel_code'      => $channelCode,
                'gateway_reference' => null,
                'payment_url'       => null,
                'gateway_payload'   => null,
                'status'            => 'waiting_payment',
            ]);
        }

        // ========= TRIPAY =========
        if ($gatewayName === 'tripay') {
            $cred = $gateway->credentials ?? [];

            $payload = [
                'method'       => $channelCode,
                'merchant_ref' => $order->invoice_number,
                'amount'       => (int)$order->final_price,

                'customer_name'  => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,

                'order_items' => [
                    [
                        'sku'      => 'ORDER',
                        'name'     => $order->product_name,
                        'price'    => (int)$order->final_price,
                        'quantity' => 1,
                        'subtotal' => (int)$order->final_price,
                    ],
                ],

                'return_url'   => route('payment.page', $order->id),
                'callback_url' => url('/api/webhooks/tripay'),
                'expired_time' => time() + (24 * 60 * 60),
            ];

            try {
                $resp = $tripay->createTransaction($cred, $payload);
            } catch (\Throwable $e) {
                return back()->with('error', 'Tripay error: ' . $e->getMessage());
            }

            $data = $resp['data'] ?? null;
            if (!$data) {
                $payment->gateway_payload = $resp;
                $payment->save();
                return back()->with('error', 'Tripay response tidak punya data.');
            }

            $reference = $data['reference'] ?? null;
            $checkoutUrl = $data['checkout_url'] ?? ($data['pay_url'] ?? null);

            $payment->gateway_reference = $reference;
            $payment->payment_url = $checkoutUrl;
            $payment->gateway_payload = $resp;
            $payment->save();

            if (!$checkoutUrl) {
                return back()->with('error', 'Tripay tidak mengembalikan checkout_url/pay_url.');
            }

            return redirect()->away($checkoutUrl);
        }

        // ========= DOKU =========
        if ($gatewayName === 'doku') {
            $cred = $gateway->credentials ?? [];

            $payload = [
                'order' => [
                    'invoice_number' => $order->invoice_number,
                    'amount'         => (int)$order->final_price,
                ],
                'customer' => [
                    'name'  => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'payment' => [
                    'payment_method' => $channelCode,
                ],
                'callback_url' => url('/api/webhooks/doku'),
                'return_url'   => route('payment.page', $order->id),
            ];

            try {
                $resp = $doku->createPayment($cred, $payload);
            } catch (\Throwable $e) {
                return back()->with('error', 'DOKU error: ' . $e->getMessage());
            }

            $paymentUrl = data_get($resp, 'payment.paymentUrl')
                ?? data_get($resp, 'paymentUrl')
                ?? data_get($resp, 'redirect_url');

            $payment->gateway_reference = $order->invoice_number;
            $payment->payment_url = $paymentUrl;
            $payment->gateway_payload = $resp;
            $payment->save();

            if (!$paymentUrl) {
                return back()->with('error', 'DOKU tidak mengembalikan paymentUrl/redirect_url.');
            }

            return redirect()->away($paymentUrl);
        }

        // ========= MIDTRANS =========
        if ($gatewayName === 'midtrans') {
            $cred = $gateway->credentials ?? [];

            $payload = [
                'transaction_details' => [
                    'order_id'     => $order->invoice_number,
                    'gross_amount' => (int)$order->final_price,
                ],
                'customer_details' => [
                    'first_name' => $order->billing_first_name ?: $order->customer_name,
                    'last_name'  => $order->billing_last_name ?: '',
                    'email'      => $order->customer_email,
                    'phone'      => $order->customer_phone,
                ],
                'item_details' => [
                    [
                        'id'       => 'ORDER',
                        'price'    => (int)$order->final_price,
                        'quantity' => 1,
                        'name'     => $order->product_name,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('payment.page', $order->id),
                ],
            ];

            try {
                $snap = $midtrans->createSnapTransaction($cred, $payload);
            } catch (\Throwable $e) {
                return back()->with('error', 'Midtrans error: ' . $e->getMessage());
            }

            $redirectUrl = $snap['redirect_url'] ?? null;

            $payment->gateway_reference = $order->invoice_number;
            $payment->payment_url = $redirectUrl;
            $payment->gateway_payload = $snap;
            $payment->save();

            if (!$redirectUrl) {
                return back()->with('error', 'Midtrans tidak mengembalikan redirect_url.');
            }

            return redirect()->away($redirectUrl);
        }

        return back()->with('error', 'Gateway tidak dikenal.');
    }
}
