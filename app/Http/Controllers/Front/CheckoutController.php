<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentMethod;

class CheckoutController extends Controller
{
    public function show($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Cek tipe order → ambil paket yang benar
        if ($order->type === 'tour') {
            $package = \App\Models\TourPackage::find($order->product_id);
        } else {
            $package = \App\Models\RentCarPackage::find($order->product_id);
        }

        $methods = PaymentMethod::where('is_active', 1)->get();

        return view('front.checkout.index', compact('order', 'package', 'methods'));
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

        // Update order
        $order->update([
            'billing_first_name' => $data['billing_first_name'],
            'billing_last_name'  => $data['billing_last_name'],
            'billing_country'    => $data['billing_country'],
            'billing_address'    => $data['billing_address'],
            'billing_city'       => $data['billing_city'],
            'billing_state'      => $data['billing_state'],
            'billing_postal'     => $data['billing_postal'],
            'billing_phone'      => $data['billing_phone'],

            'payment_method'     => $data['payment_method'],
            'payment_status'     => 'waiting_payment',
        ]);

        return redirect()->route('payment.page', $order->id);
    }
}
