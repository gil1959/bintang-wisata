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
use App\Models\User;
use App\Models\AffiliateLink;
class RentCarOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = RentCarPackage::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:50',

            
            'pickup'       => 'nullable|date',
            'return'       => 'nullable|date',
            'pickup_date'  => 'nullable|date',
            'return_date'  => 'nullable|date',

            'promo_id' => 'nullable|integer',
        ]);

        
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
$promoUsed = null;

if (!empty($validated['promo_id'])) {
    $promo = Promo::find($validated['promo_id']);

    if ($promo && $promo->is_valid_for($subtotal)) {
        $alreadyUsed = Order::where('customer_email', $validated['email'])
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


        $final = max(0, $subtotal - $discount);
$affUserId = session('affiliate_user_id');
$affLinkId = session('affiliate_link_id');
$affRef    = session('affiliate_ref');

$affType   = null;
$affValue  = null;
$affAmount = null;
$affStatus = null;

if ($affUserId && $affLinkId && $affRef) {
    $affUser = User::find($affUserId);

    if ($affUser && $affUser->is_affiliate) {
        $affType  = $affUser->affiliate_commission_type ?: 'percent';
        $affValue = (float) ($affUser->affiliate_commission_value ?: 0);

        if ($affType === 'percent') {
            $affAmount = (int) round(($final * $affValue) / 100);
        } else {
            $affAmount = (int) round($affValue);
        }

        $affStatus = 'pending';
    } else {
        $affUserId = null;
        $affLinkId = null;
        $affRef = null;
    }
}
$userId = auth()->id() ?: User::where('email', $validated['email'])->value('id');

        // ===================== SIMPAN ORDER ======================
        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'rent_car',
            'product_id'     => $package->id,
            'product_name'   => $package->title,
'promo_id'   => $promoUsed?->id,
'promo_code' => $promoUsed?->code,

            'customer_name'  => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
'affiliate_user_id' => $affUserId,
'affiliate_link_id' => $affLinkId,
'affiliate_ref' => $affRef,
'affiliate_commission_type' => $affType,
'affiliate_commission_value' => $affValue,
'affiliate_commission_amount' => $affAmount,
'affiliate_commission_status' => $affStatus,
            'user_id' => $userId,

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
        if ($affLinkId) {
    AffiliateLink::where('id', $affLinkId)->increment('conversions');
}

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
