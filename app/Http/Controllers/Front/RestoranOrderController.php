<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestoranPackage;
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

class RestoranOrderController extends Controller
{
    public function draft(Request $request, $slug)
    {
        $package = RestoranPackage::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'required|string|max:50',
            'departure_date' => 'required|date|after:today',
            'pickup_time' => 'required|date_format:H:i',
            'participants' => 'required|integer|min:1|max:9999',
            'promo_id' => 'nullable|integer',
            'menus' => 'required|array|min:1',
            'menus.*.id' => 'required|integer',
            'menus.*.qty' => 'required|integer|min:1|max:999',
        ], [
            'departure_date.after' => 'Waktu reservasi minimal harus H-1 (mulai besok).',
        ]);

        // Verifikasi menu langsung ke database
        $menuIds = collect($data['menus'])->pluck('id')->unique();
        $dbMenus = \App\Models\RestoranMenu::where('restoran_package_id', $package->id)
            ->whereIn('id', $menuIds)
            ->where('is_ready', 1)
            ->get()
            ->keyBy('id');

        if ($dbMenus->isEmpty()) {
            return response()->json([
                'error' => 'Menu yang dipilih tidak tersedia.'
            ], 422);
        }

        $cleanOrderItems = [];
        $subtotal = 0;

        foreach ($data['menus'] as $m) {
            $menuId = (int)$m['id'];
            if (!$dbMenus->has($menuId)) continue;

            $dbMenu = $dbMenus->get($menuId);
            $qty = max(1, (int)$m['qty']);
            $unitPrice = (float)$dbMenu->price;
            $lineTotal = $unitPrice * $qty;

            $cleanOrderItems[] = [
                'id' => $dbMenu->id,
                'name' => $dbMenu->name,
                'category' => $dbMenu->category ?? '',
                'price' => $unitPrice,
                'qty' => $qty,
                'subtotal' => $lineTotal,
                'thumbnail_path' => $dbMenu->thumbnail_path ?? '',
            ];

            $subtotal += $lineTotal;
        }

        if (empty($cleanOrderItems)) {
            return response()->json([
                'error' => 'Silakan pilih minimal 1 menu makanan yang tersedia.'
            ], 422);
        }

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

        $userId = auth()->id() ?: User::where('email', $data['email'])->value('id');

        $order = Order::create([
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type'           => 'restoran',
            'product_id'     => $package->id,
            'product_name'   => $package->title,
            'order_items'    => $cleanOrderItems,
            'user_id'        => $userId,

            'promo_id'       => $promoUsed?->id,
            'promo_code'     => $promoUsed?->code,

            'affiliate_user_id' => $affUserId,
            'affiliate_link_id' => $affLinkId,
            'affiliate_ref' => $affRef,
            'affiliate_commission_type' => $affType,
            'affiliate_commission_value' => $affValue,
            'affiliate_commission_amount' => $affAmount,
            'affiliate_commission_status' => $affStatus,

            'customer_name'  => $data['name'],
            'customer_email' => $data['email'],
            'customer_phone' => $data['phone'],

            'departure_date' => $data['departure_date'],
            'participants'   => (int) $data['participants'],

            'pickup_date'    => $data['departure_date'] . ' ' . $data['pickup_time'] . ':00',
            'return_date'    => null,
            'total_days'     => null,

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
            'redirect' => route('checkout.show', $order->id)
        ]);
    }
}
