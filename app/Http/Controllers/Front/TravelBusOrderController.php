<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TravelBusPackage;
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

class TravelBusOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = TravelBusPackage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'name'            => 'required|string|max:120',
            'email'           => 'required|email',
            'phone'           => 'required|string|max:50',
            'departure_date'  => 'required|date|after_or_equal:today',
            'pax'             => 'required|integer|min:1',
            'promo_id'        => 'nullable|integer',
        ], [
            'departure_date.after_or_equal' => 'Tanggal keberangkatan minimal hari ini.',
            'pax.min' => 'Jumlah penumpang minimal 1 orang.',
        ]);

        try {
            $departureCarbon = Carbon::parse($validated['departure_date']);
            $departureDateDb = $departureCarbon->format('Y-m-d');
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'departure_date' => ['Format tanggal tidak valid.'],
                ]
            ], 422);
        }

        $pax = (int) $validated['pax'];
        $pricePerPax = (float) $package->price;
        $subtotal = $pax * $pricePerPax;

        $orderItems = [
            'armada_name'        => $package->armada_name,
            'armada_type'        => $package->armada_type,
            'travel_type'        => $package->travel_type,
            'departure_location' => $package->departure_location,
            'arrival_location'   => $package->arrival_location,
            'departure_time'     => $package->departure_time,
            'arrival_time'       => $package->arrival_time,
            'pax'                => $pax,
            'price_per_pax'      => $pricePerPax,
            'subtotal'           => $subtotal,
        ];

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
            'type'           => 'travel_bus',
            'product_id'     => $package->id,
            'product_name'   => $package->armada_name . ' (' . $package->travel_type . ')',
            'order_items'    => $orderItems,
            'promo_id'       => $promoUsed?->id,
            'promo_code'     => $promoUsed?->code,

            'customer_name'  => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],

            'affiliate_user_id'          => $affUserId,
            'affiliate_link_id'          => $affLinkId,
            'affiliate_ref'              => $affRef,
            'affiliate_commission_type'  => $affType,
            'affiliate_commission_value' => $affValue,
            'affiliate_commission_amount'=> $affAmount,
            'affiliate_commission_status'=> $affStatus,
            'user_id'                    => $userId,

            'pickup_date'    => $departureDateDb,
            'departure_date' => $departureDateDb,
            'participants'   => $pax,

            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'final_price'    => $final,

            'payment_status' => 'waiting_payment',
            'order_status'   => 'pending',
        ]);

        if ($affLinkId) {
            AffiliateLink::where('id', $affLinkId)->increment('conversions');
        }

        app(\App\Services\Mobile\InvoiceDelivery::class)->send($request, function () use ($order) {
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

        });
        return response()->json([
            'redirect' => route('checkout.show', $order->id),
        ]);
    }
}
