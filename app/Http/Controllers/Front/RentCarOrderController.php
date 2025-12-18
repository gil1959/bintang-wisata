<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RentCarPackage;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Setting;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RentCarOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        /**
         * Frontend lo saat ini ngirim:
         * - pickup (string date)
         * - return (string date)
         *
         * Tapi untuk DB kita simpan sebagai:
         * - pickup_date
         * - return_date
         *
         * Jadi kita validasi yang ada, lalu normalisasi.
         */
        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:50',

            // ✅ terima dua kemungkinan nama field
            'pickup'       => 'nullable|date',
            'return'       => 'nullable|date',
            'pickup_date'  => 'nullable|date',
            'return_date'  => 'nullable|date',

            'promo_id' => 'nullable|integer',
        ]);

        // ✅ normalisasi tanggal (prioritaskan pickup_date jika ada)
        $pickupDate = $validated['pickup_date'] ?? $validated['pickup'] ?? null;
        $returnDate = $validated['return_date'] ?? $validated['return'] ?? null;

        if (!$pickupDate || !$returnDate) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'pickup_date' => ['Pickup date is required.'],
                    'return_date' => ['Return date is required.'],
                ]
            ], 422);
        }

        // ✅ validasi return >= pickup
        if (strtotime($returnDate) < strtotime($pickupDate)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'return_date' => ['Return date must be after or equal to pickup date.'],
                ]
            ], 422);
        }

        // ===================== HITUNG DURASI ======================
        $start = strtotime($pickupDate);
        $end   = strtotime($returnDate);

        $days = max(1, floor(($end - $start) / 86400) + 1);
        $subtotal = $days * $package->price_per_day;

        // ===================== PROMO ======================
        $discount = 0;
        if (!empty($validated['promo_id'])) {
            $promo = Promo::find($validated['promo_id']);
            if ($promo && $promo->is_valid_for($subtotal)) {
                $discount = $promo->calculate_discount($subtotal);
            }
        }

        $final = max(0, $subtotal - $discount);

        // ===================== SIMPAN ORDER ======================
        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'rent_car',
            'product_id'     => $package->id,
            'product_name'   => $package->title,

            'customer_name'  => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],

            // ✅ ini yang bikin admin bisa tampil
            'pickup_date'    => $pickupDate,
            'return_date'    => $returnDate,

            'departure_date' => null,
            'participants'   => null,

            'total_days'     => $days,

            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'final_price'    => $final,

            'payment_status' => 'waiting_payment',
            'order_status'   => 'pending',
        ]);
        try {
            if (!empty($order->customer_email)) {
                Mail::to($order->customer_email)->send(new OrderInvoiceMail($order, false));
            }

            $adminEmail = Setting::invoiceAdminEmail();
            if (!empty($adminEmail) && $adminEmail !== $order->customer_email) {
                Mail::to($adminEmail)->send(new OrderInvoiceMail($order, true));
            }
        } catch (\Throwable $e) {
            Log::warning('Invoice email gagal dikirim', [
                'invoice' => $order->invoice_number,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'redirect' => route('checkout.show', $order->id),
        ]);
    }
}
