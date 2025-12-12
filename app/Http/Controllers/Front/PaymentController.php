<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show(Order $order)
    {
        if (! $order->payment_method) {
            return redirect()->route('checkout.show', $order);
        }

        // Pecah kode payment_method → manual:ID atau gateway:nama
        [$type, $value] = explode(':', $order->payment_method, 2);
        $manualMethod = null;
        $gateway      = null;

        if ($type === 'manual') {
            $manualMethod = PaymentMethod::where('id', (int) $value)
                ->where('type', 'manual')
                ->where('is_active', 1)
                ->firstOrFail();
        } elseif ($type === 'gateway') {
            $gateway = PaymentGateway::where('name', $value)
                ->where('is_active', 1)
                ->firstOrFail();
        } else {
            abort(400, 'Metode pembayaran tidak dikenal.');
        }

        return view('front.payment.index', compact('order', 'type', 'manualMethod', 'gateway'));
    }

    public function submitManual(Request $request, Order $order)
    {
        if (strpos($order->payment_method, 'manual:') !== 0) {
            abort(403);
        }

        $request->validate([
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Hindari spam: kalau sudah ada payment waiting_verification / paid, jangan buat baru
        if ($order->payments()->whereIn('status', ['waiting_verification', 'paid'])->exists()) {
            return redirect()
                ->route('payment.waiting', $order)
                ->with('error', 'Pembayaran sudah dikirim. Tunggu verifikasi admin.');
        }

        $path = $request->file('proof')->store('payments', 'public');

        $order->payments()->create([
            'method'            => 'manual',
            'amount'            => $order->final_price,     // ANTI manipulasi harga
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

    public function startGateway(Request $request, Order $order)
    {
        if (strpos($order->payment_method, 'gateway:') !== 0) {
            abort(403);
        }

        [, $gatewayName] = explode(':', $order->payment_method, 2);

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', 1)
            ->firstOrFail();

        // Catat payment dulu supaya masuk ke dashboard admin
        $payment = $order->payments()->create([
            'method'            => 'gateway',
            'amount'            => $order->final_price,
            'proof_image'       => null,
            'gateway_reference' => null,
            'status'            => 'waiting_verification', // dianggap pending
        ]);

        // TODO: Integrasi API gateway:
        // 1. Panggil API (Xendit/Duitku/Tripay) buat invoice.
        // 2. Simpan ID transaksi ke $payment->gateway_reference.
        // 3. Redirect ke URL pembayaran dari gateway.
        // 4. Di callback webhooks, ubah $payment->status = 'paid' dan order->payment_status / order_status.

        return back()->with('error', 'Payment gateway belum dikonfigurasi. Silakan hubungi admin.');
    }
}
