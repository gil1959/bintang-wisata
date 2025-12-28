<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UmrahPackage;
use App\Models\Order;
use App\Models\Promo;

class UmrahOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = UmrahPackage::with('tiers')->where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'booking_date' => 'required|date|after_or_equal:today',

            'tier_id' => 'required|integer',
            'participants' => 'required|integer|min:1|max:999',

            'promo_id' => 'nullable|integer',
        ]);

        $tier = $package->tiers->firstWhere('id', (int)$data['tier_id']);
        if (!$tier) {
            return response()->json(['error' => 'Harga tidak valid.'], 422);
        }

        $subtotal = (int)$tier->price * (int)$data['participants'];

        // Promo
        $discount = 0;
        $promoUsed = null;

        if (!empty($data['promo_id'])) {
            $promo = Promo::find($data['promo_id']);

            if ($promo && $promo->is_valid_for($subtotal)) {

                $alreadyUsed = Order::where('customer_email', $data['email'])
                    ->where('promo_id', $promo->id)
                    ->exists();

                if ($alreadyUsed) {
                    return response()->json([
                        'error' => 'Kode promo ini sudah pernah digunakan untuk email ini.'
                    ], 422);
                }

                $discount = $promo->calculate_discount($subtotal);
                $promoUsed = $promo;
            }
        }

        $final = $subtotal - $discount;

        $order = Order::create([
    'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
    'type'           => 'umrah',
    'product_id'     => $package->id,
    'product_name'   => $package->title,

    'promo_id'       => $promoUsed?->id,
    'promo_code'     => $promoUsed?->code,

    'customer_name'  => $data['name'],
    'customer_email' => $data['email'],
    'customer_phone' => $data['phone'],

    // Umrah disimpan pakai field "departure_date" biar konsisten sama schema orders
    'departure_date' => $data['booking_date'],
    'participants'   => (int) $data['participants'],

    // field rentcar nullable
    'pickup_date'    => null,
    'return_date'    => null,
    'total_days'     => null,

    'subtotal'       => $subtotal,
    'discount'       => $discount,

    // PENTING: di tabel kolomnya "final_price", bukan "total"
    'final_price'    => $final,

    // PENTING: kolom ini wajib (enum non-null) di migration orders
    'payment_status' => 'waiting_payment',
    'order_status'   => 'pending',
]);


        // Simpan detail tier yang dipilih biar admin bisa lihat (kalau orders punya kolom notes/meta).
        // Kalau belum ada: lu bisa tambah kolom json "meta" di orders.
        // Untuk minimal change: skip dulu.

       return response()->json([
    'ok' => true,
    'redirect' => route('checkout.show', $order->id),
]);


    }
}
