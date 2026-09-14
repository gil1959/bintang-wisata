<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelPackage;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Setting;
use App\Mail\OrderInvoiceMail;
use App\Mail\PartnerOrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\PartnerPayoutService;
use App\Models\User;
use App\Models\AffiliateLink;
use Carbon\Carbon;

class HotelOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = HotelPackage::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $validated = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:50',
            'checkin_date'  => 'required|date|after_or_equal:tomorrow',
            'checkout_date' => 'required|date|after:checkin_date',
            'promo_id' => 'nullable|integer',
            'room_id'  => 'nullable|integer',
            'with_breakfast' => 'nullable',
            'room_count' => 'nullable|integer|min:1',
        ], [
            'checkin_date.after_or_equal' => 'Tanggal reservasi minimal sehari sebelumnya (minimal besok).',
            'checkout_date.after' => 'Tanggal check-out harus setelah tanggal check-in.',
        ]);

        $checkinDate = $validated['checkin_date'];
        $checkoutDate = $validated['checkout_date'];

        try {
            $checkinCarbon = Carbon::parse($checkinDate);
            $checkoutCarbon = Carbon::parse($checkoutDate);

            $checkinDateDb = $checkinCarbon->format('Y-m-d');
            $checkoutDateDb = $checkoutCarbon->format('Y-m-d');
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'checkin_date' => ['Format tanggal tidak valid.'],
                    'checkout_date' => ['Format tanggal tidak valid.'],
                ]
            ], 422);
        }

        if ($checkoutCarbon->lte($checkinCarbon)) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'checkout_date' => ['Checkout date must be after checkin date.'],
                ]
            ], 422);
        }

        $nights = max(1, $checkinCarbon->diffInDays($checkoutCarbon));
        $roomCount = max(1, (int)($request->input('room_count', 1)));
        $withBreakfast = (bool)$request->input('with_breakfast', false);

        $room = null;
        if (!empty($validated['room_id'])) {
            $room = $package->rooms()->find($validated['room_id']);
        }

        if ($room) {
            $roomRate = ($withBreakfast && $room->price_with_breakfast) ? (float)$room->price_with_breakfast : (float)$room->price;
            $productName = $package->title . ' (' . $room->name . ($withBreakfast ? ' + Sarapan' : '') . ')';
            $orderItems = [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'room_size' => $room->room_size,
                'bed_type' => $room->bed_type,
                'with_breakfast' => $withBreakfast,
                'price_per_night' => $roomRate,
                'nights' => $nights,
                'room_count' => $roomCount,
                'subtotal' => $nights * $roomRate * $roomCount,
            ];
        } else {
            $roomRate = (float)$package->price_per_night;
            $productName = $package->title;
            $orderItems = [
                'room_id' => null,
                'room_name' => 'Standar / Pilihan Properti',
                'with_breakfast' => false,
                'price_per_night' => $roomRate,
                'nights' => $nights,
                'room_count' => $roomCount,
                'subtotal' => $nights * $roomRate * $roomCount,
            ];
        }

        $subtotal = $nights * $roomRate * $roomCount;

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

        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'hotel',
            'product_id'     => $package->id,
            'product_name'   => $productName,
            'order_items'    => $orderItems,
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

            'pickup_date'    => $checkinDateDb,
            'return_date'    => $checkoutDateDb,

            'departure_date' => null,
            'participants'   => null,

            'total_days'     => $nights,
            'total_hours'    => null,

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

            $payoutService = app(PartnerPayoutService::class);
            $partnerId = $payoutService->resolvePartnerIdFromOrder($order);
            if ($partnerId) {
                $partner = User::find($partnerId);
                if ($partner && $partner->email !== $order->customer_email) {
                    Mail::to($partner->email)->send(new PartnerOrderInvoiceMail($order, $partner));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Invoice email gagal dikirim', [
                'invoice' => $order->invoice_number,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'redirect' => route('checkout.show', $order->id),
        ]);
    }
}
