<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Promo;
use App\Models\RentCarPackage;

class RentCarOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        // Validasi input pop-up
        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email',
            'phone'  => 'required|string|max:20',

            'pickup' => 'required|date',
            'return' => 'required|date|after_or_equal:pickup',

            'promo_id' => 'nullable|integer',
            'final_price' => 'required|integer|min:1'
        ]);

        /*
        |--------------------------------------------------------------------------
        | HITUNG ULANG TOTAL HARI (ANTI MANIPULASI)
        |--------------------------------------------------------------------------
        */
        $days = (strtotime($data['return']) - strtotime($data['pickup'])) / 86400 + 1;
        if ($days < 1) $days = 1;

        $backend_price = $package->price_per_day * $days;

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PROMO DI BACKEND
        |--------------------------------------------------------------------------
        */
        $discount = 0;

        if (!empty($data['promo_id'])) {
            $promo = Promo::find($data['promo_id']);

            if ($promo && $promo->is_valid_for($backend_price)) {
                $discount = $promo->calculate_discount($backend_price);
            }
        }

        $final_price = $backend_price - $discount;

        /*
        |--------------------------------------------------------------------------
        | BUAT ORDER
        |--------------------------------------------------------------------------
        */
        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(100, 999),

            'type' => 'rent_car',
            'product_id' => $package->id,
            'product_name' => $package->title,

            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],

            'total_days' => $days,

            'subtotal' => $backend_price,
            'discount' => $discount,
            'final_price' => $final_price,

            'payment_status' => 'waiting_payment',
            'order_status' => 'pending',
        ]);

        return response()->json([
            'redirect' => route('checkout', $order->id)
        ]);
    }
}
