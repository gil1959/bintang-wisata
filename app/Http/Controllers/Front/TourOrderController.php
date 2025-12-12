<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use App\Models\Order;
use App\Models\Promo;

class TourOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = TourPackage::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name'           => 'required|string|max:120',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:50',
            'departure_date' => 'required|date|after_or_equal:today',
            'participants'   => 'required|integer|min:1',
            'promo_id'       => 'nullable|integer',
        ]);

        // ===================== HITUNG TIER ======================
        $tier = $package->tiers->first(function ($t) use ($data) {
            return $data['participants'] >= $t->min_people &&
                ($t->max_people === null || $data['participants'] <= $t->max_people);
        });

        if (!$tier) {
            return response()->json([
                'error' => 'Tidak ada harga untuk jumlah peserta ini.'
            ], 422);
        }

        $subtotal = $tier->price * $data['participants'];

        // ===================== PROMO ======================
        $discount = 0;
        if ($data['promo_id']) {
            $promo = Promo::find($data['promo_id']);
            if ($promo && $promo->is_valid_for($subtotal)) {
                $discount = $promo->calculate_discount($subtotal);
            }
        }

        $final = $subtotal - $discount;

        // ===================== SIMPAN ORDER ======================
        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'tour',
            'product_id'     => $package->id,
            'product_name'   => $package->title,

            'customer_name'  => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],

            'departure_date' => $data['departure_date'],
            'participants'   => $data['participants'],

            'total_days'     => null, // khusus rent car

            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'final_price'    => $final,

            'payment_status' => 'waiting_payment',
            'order_status'   => 'pending',
        ]);

        return response()->json([
            'redirect' => route('checkout.show', $order->id)
        ]);
    }
}
