<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentGateway;

class CheckoutController extends Controller
{
    public function show($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Ambil paket buat ringkasan
        if ($order->type === 'tour') {
            $package = \App\Models\TourPackage::find($order->product_id);
        } else {
            $package = \App\Models\RentCarPackage::find($order->product_id);
        }

        // Semua metode pembayaran yang diaktifkan admin
        $manualMethods = PaymentMethod::where('is_active', 1)
            ->where('type', 'manual')
            ->orderBy('id')
            ->get();

        $gateways = PaymentGateway::where('is_active', 1)
            ->orderBy('id')
            ->get();

        return view('front.checkout.index', [
            'order'         => $order,
            'package'       => $package,
            'manualMethods' => $manualMethods,
            'gateways'      => $gateways,
        ]);
    }

    public function process(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $data = $request->validate([
            'billing_first_name' => 'required|string|max:120',
            'billing_last_name'  => 'required|string|max:120',
            'billing_country'    => 'required|string',
            'billing_address'    => 'required|string',
            'billing_city'       => 'required|string',
            'billing_state'      => 'required|string',
            'billing_postal'     => 'required|string|max:20',
            'billing_phone'      => 'required|string|max:20',
            'payment_method'     => 'required|string',
        ]);

        // --- Validasi & normalisasi payment_method ---
        $raw   = $data['payment_method'];
        $parts = explode(':', $raw, 2);

        if (count($parts) !== 2) {
            return back()->withErrors([
                'payment_method' => 'Metode pembayaran tidak valid.',
            ])->withInput();
        }

        [$type, $value] = $parts;

        if ($type === 'manual') {
            // Harus ada di tabel payment_methods dengan type=manual
            $method = PaymentMethod::where('id', (int) $value)
                ->where('type', 'manual')
                ->where('is_active', 1)
                ->first();

            if (! $method) {
                return back()->withErrors([
                    'payment_method' => 'Rekening transfer tidak ditemukan / nonaktif.',
                ])->withInput();
            }
        } elseif ($type === 'gateway') {
            // Harus ada gateway aktif
            $gateway = PaymentGateway::where('name', $value)
                ->where('is_active', 1)
                ->first();

            if (! $gateway) {
                return back()->withErrors([
                    'payment_method' => 'Payment gateway tidak tersedia.',
                ])->withInput();
            }
        } else {
            return back()->withErrors([
                'payment_method' => 'Metode pembayaran tidak dikenal.',
            ])->withInput();
        }

        // Update order (payment_method disimpan apa adanya, mis: manual:1 / gateway:xendit)
        $order->update([
            'billing_first_name' => $data['billing_first_name'],
            'billing_last_name'  => $data['billing_last_name'],
            'billing_country'    => $data['billing_country'],
            'billing_address'    => $data['billing_address'],
            'billing_city'       => $data['billing_city'],
            'billing_state'      => $data['billing_state'],
            'billing_postal'     => $data['billing_postal'],
            'billing_phone'      => $data['billing_phone'],

            'payment_method'     => $raw,
            'payment_status'     => 'waiting_payment',
        ]);

        return redirect()->route('payment.page', $order->id);
    }
}
