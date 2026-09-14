<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Order;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AffiliateUserCoupon;
use App\Models\TourPackage;
use App\Models\UmrahPackage;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\MicePackage;
use App\Services\Darmawisata\DarmawisataClient;
use App\Services\FlightPriceOverrideService;
use Illuminate\Support\Facades\Cache;

class AffiliateController extends Controller
{
    private function guardAffiliate()
    {
        $u = auth()->user();

        if (!$u) {
            abort(403);
        }

        // kalau belum approved, lempar ke halaman apply/status
        if (($u->affiliate_status ?? 'none') !== 'approved' || !$u->is_affiliate) {
            redirect()->route('user.affiliate.apply')->send();
            exit;
        }
    }
    public function apply()
    {
        $u = auth()->user();

        // kalau sudah approved, langsung ke dashboard
        if (($u->affiliate_status ?? 'none') === 'approved' && $u->is_affiliate) {
            return redirect()->route('user.affiliate.dashboard');
        }

        $status = $u->affiliate_status;
        $status = is_string($status) ? trim($status) : $status;

        // kalau null / string kosong -> anggap belum mengajukan
        if (empty($status)) {
            $status = 'none';
        }

        return view('user.affiliate.apply', [
            'status' => $status,
            'note' => $u->affiliate_review_note,
            'requested_at' => $u->affiliate_requested_at,
            'reviewed_at' => $u->affiliate_reviewed_at,
        ]);
    }

    public function submitApplication(\Illuminate\Http\Request $request)
    {
        $u = auth()->user();

        // jangan bikin spam submit berkali-kali
        $status = $u->affiliate_status;
        $status = is_string($status) ? trim($status) : $status;

        $isEn = app()->getLocale() === 'en';

        if ($status === 'pending') {
            return back()->with(
                'success',
                $isEn
                    ? 'Your application has already been submitted. Please wait for admin review.'
                    : 'Pengajuan kamu sudah masuk. Tinggal tunggu admin review.'
            );
        }


        $data = $request->validate([
            'reason' => ['required', 'string', 'min:20', 'max:1000'],
            'channel' => ['nullable', 'string', 'max:100'],
        ]);

        $u->affiliate_status = 'pending';
        $u->affiliate_requested_at = now();

        // simpan alasan di note dulu (admin bisa lihat)
        $u->affiliate_review_note = "APPLY:\nReason: {$data['reason']}\nChannel: " . ($data['channel'] ?? '-');
        $u->affiliate_reviewed_at = null;
        $u->affiliate_reviewed_by = null;

        // pastikan belum aktif sebelum approve
        $u->is_affiliate = false;

        $u->save();

        return redirect()->route('user.affiliate.apply')->with(
            'success',
            $isEn
                ? 'Affiliate application submitted successfully.'
                : 'Pengajuan affiliate berhasil dikirim.'
        );
    }


    public function dashboard()
    {
        $this->guardAffiliate();

        $userId = auth()->id();

        $stats = [
            'links' => AffiliateLink::where('user_id', $userId)->count(),
            'clicks' => AffiliateLink::where('user_id', $userId)->sum('clicks'),
            'conversions' => AffiliateLink::where('user_id', $userId)->sum('conversions'),
            'commission_pending' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'pending')->sum('affiliate_commission_amount'),
            'commission_approved' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'approved')->sum('affiliate_commission_amount'),
            'commission_paid' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'paid')->sum('affiliate_commission_amount'),
        ];

        return view('user.affiliate.dashboard', compact('stats'));
    }

    public function commission()
    {
        $this->guardAffiliate();

        $userId = auth()->id();

        $orders = Order::query()
            ->where('affiliate_user_id', $userId)
            ->whereNotNull('affiliate_commission_amount')
            ->orderByDesc('id')
            ->paginate(15);

        $summary = [
            'pending' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'pending')->sum('affiliate_commission_amount'),
            'approved' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'approved')->sum('affiliate_commission_amount'),
            'paid' => Order::where('affiliate_user_id', $userId)->where('affiliate_commission_status', 'paid')->sum('affiliate_commission_amount'),
        ];

        return view('user.affiliate.commission', compact('orders', 'summary'));
    }

    public function links()
    {
        $this->guardAffiliate();

        $links = AffiliateLink::where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('user.affiliate.links', compact('links'));
    }

    public function storeLink(Request $request)
    {
        $this->guardAffiliate();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'product_type' => ['required', 'in:tour,umrah,rent_car,ship,mice,flight'],
            'product_id' => ['nullable', 'string', 'max:100'],

            // acquisition params
            'platform' => ['nullable', 'string', 'max:50'],
            'platform_id' => ['nullable', 'string', 'max:100'],

            // utm optional
            'utm_source' => ['nullable', 'string', 'max:80'],
            'utm_medium' => ['nullable', 'string', 'max:80'],
            'utm_campaign' => ['nullable', 'string', 'max:120'],
            'utm_content' => ['nullable', 'string', 'max:120'],
            'utm_term' => ['nullable', 'string', 'max:120'],

            // coupon user optional
            'user_coupon_id' => ['nullable', 'integer', 'exists:affiliate_user_coupons,id'],
        ]);

        if (($data['product_type'] ?? null) === 'flight' && empty($data['product_id'])) {
            return back()
                ->withErrors(['product_pick' => 'Pilih tiket pesawat terlebih dahulu.'])
                ->withInput();
        }

        if (($data['product_type'] ?? null) === 'flight' && (($data['tripType'] ?? 'OneWay') === 'RoundTrip') && empty($data['returnDate'])) {
            return back()
                ->withErrors(['returnDate' => 'Return date wajib diisi untuk RoundTrip.'])
                ->withInput();
        }

        // resolve product
        $product = null;
        $slug = null;
        $productName = null;

        if ($data['product_type'] === 'tour') {
            $p = \App\Models\TourPackage::findOrFail($data['product_id']);
            $product = $p;
            $slug = $p->slug;
            $productName = $p->title;
            $baseUrl = route('tour.show', $slug);
        } elseif ($data['product_type'] === 'umrah') {
            $p = \App\Models\UmrahPackage::findOrFail($data['product_id']);
            $product = $p;
            $slug = $p->slug;
            $productName = $p->title;
            $baseUrl = route('umrah.show', $slug);
        } elseif ($data['product_type'] === 'rent_car') {

            $p = \App\Models\RentCarPackage::findOrFail($data['product_id']);
            $product = $p;
            $slug = $p->slug;
            $productName = $p->name;
            $baseUrl = route('rentcar.show', $slug);
        } elseif ($data['product_type'] === 'ship') {

            $p = \App\Models\ShipPackage::findOrFail($data['product_id']);
            $product = $p;
            $slug = $p->slug;
            $productName = $p->name;
            $baseUrl = route('ship.show', $slug);
        } elseif ($data['product_type'] === 'mice') {

            $p = \App\Models\MicePackage::findOrFail($data['product_id']);
            $product = $p;
            $slug = $p->slug;
            $productName = $p->title;
            $baseUrl = route('mice.show', $slug);
        } elseif ($data['product_type'] === 'flight') {

            $flightKey = trim((string) ($data['product_id'] ?? ''));

            if ($flightKey === '' || !Cache::has("flight_quote:{$flightKey}")) {
                return back()
                    ->withErrors(['product_pick' => 'Tiket pesawat yang dipilih sudah kadaluarsa. Cari ulang lalu pilih tiket lagi.'])
                    ->withInput();
            }

            $payload = (array) Cache::get("flight_quote:{$flightKey}", []);
            $search = (array) ($payload['search'] ?? []);
            $journey = (array) ($payload['journey'] ?? []);

            $origin = strtoupper((string) data_get($journey, 'jiOrigin', $search['origin'] ?? ''));
            $destination = strtoupper((string) data_get($journey, 'jiDestination', $search['destination'] ?? ''));
            $departAt = (string) data_get($journey, 'deptDate', data_get($journey, 'journeyDate', $search['departDate'] ?? ''));
            $airlineName = (string) data_get($journey, 'airlineName', data_get($journey, 'airlineID', 'Flight'));

            $product = null;
            $slug = $flightKey;
            $productName = trim($airlineName . ' | ' . $origin . ' - ' . $destination . ($departAt !== '' ? ' | ' . $departAt : ''));
            $baseUrl = route('flights.show', $flightKey);
        }

        // code unik
        do {
            $code = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8));
        } while (\App\Models\AffiliateLink::where('code', $code)->exists());

        // promo optional dari user coupon
        $promoId = null;
        $promoCode = null;
        if (!empty($data['user_coupon_id'])) {
            $uc = \App\Models\AffiliateUserCoupon::where('id', $data['user_coupon_id'])
                ->where('user_id', auth()->id())
                ->with('promo')
                ->firstOrFail();

            $promoId = $uc->promo_id;
            $promoCode = $uc->promo?->code;
        }

        // build query params standar: ref + promo + utm + acquisition
        // build query params: ref + promo + acquisition + UTM
        $params = [
            'ref' => $code,
        ];

        if ($promoCode) {
            $params['promo'] = $promoCode;
        }

        // acquisition (tetap optional)
        if (!empty($data['platform'])) {
            $params['platform'] = $data['platform'];
        }
        if (!empty($data['platform_id'])) {
            $params['platform_id'] = $data['platform_id'];
        }

        /**
         * UTM (optional + default)
         * - Default dibuat supaya user awam gak perlu isi apapun tapi tracking tetap ada.
         * - Kalau user isi manual lewat Advanced Tracking, nilai user akan dipakai.
         */
        $utmSource = !empty($data['utm_source'])
            ? $data['utm_source']
            : (!empty($data['platform']) ? $data['platform'] : 'affiliate');

        $utmMedium = !empty($data['utm_medium'])
            ? $data['utm_medium']
            : 'referral';

        $utmCampaign = !empty($data['utm_campaign'])
            ? $data['utm_campaign']
            : ('user_' . auth()->id());

        // selalu set 3 ini (biar konsisten tracking)
        $params['utm_source'] = $utmSource;
        $params['utm_medium'] = $utmMedium;
        $params['utm_campaign'] = $utmCampaign;

        // tambahan optional kalau user isi
        if (!empty($data['utm_content'])) {
            $params['utm_content'] = $data['utm_content'];
        }
        if (!empty($data['utm_term'])) {
            $params['utm_term'] = $data['utm_term'];
        }


        $salesUrl = $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . http_build_query($params);
        $checkoutUrl = $salesUrl . '&checkout=1';

        \App\Models\AffiliateLink::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'code' => $code,

            'product_type' => $data['product_type'],
            'product_id' => $data['product_type'] === 'flight' ? 0 : $data['product_id'],
            'product_slug' => $slug,
            'product_name' => $productName,

            'promo_id' => $promoId,
            'promo_code' => $promoCode,

            'platform' => $data['platform'] ?? null,
            'platform_id' => $data['platform_id'] ?? null,

            'utm_source' => $utmSource ?? null,
            'utm_medium' => $utmMedium ?? null,
            'utm_campaign' => $utmCampaign ?? null,
            'utm_content' => $data['utm_content'] ?? null,
            'utm_term' => $data['utm_term'] ?? null,


            'sales_url' => $salesUrl,
            'checkout_url' => $checkoutUrl,

            'clicks' => 0,
            'conversions' => 0,
        ]);

        $isEn = app()->getLocale() === 'en';
        return redirect()->route('user.affiliate.links')->with(
            'success',
            $isEn
                ? 'Affiliate link created successfully.'
                : 'Affiliate link berhasil dibuat.'
        );
    }


    public function coupons(Request $request)
    {
        $this->guardAffiliate();

        $q = trim((string) $request->get('q', ''));

        $promos = Promo::query()
            ->when($q !== '', fn($qq) => $qq->where('code', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $myCoupons = \App\Models\AffiliateUserCoupon::with('promo')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('user.affiliate.coupons', compact('promos', 'myCoupons', 'q'));
    }

    public function storeUserCoupon(Request $request)
    {
        $this->guardAffiliate();

        $data = $request->validate([
            'promo_id' => ['required', 'integer', 'exists:promos,id'],
            'alias_name' => ['required', 'string', 'max:80'],
        ]);

        \App\Models\AffiliateUserCoupon::create([
            'user_id' => auth()->id(),
            'promo_id' => $data['promo_id'],
            'alias_name' => $data['alias_name'],
        ]);

        $isEn = app()->getLocale() === 'en';
        return back()->with(
            'success',
            $isEn
                ? 'Coupon added to your account.'
                : 'Coupon berhasil ditambahkan ke akun kamu.'
        );
    }




    public function orders()
    {
        $this->guardAffiliate();

        $orders = Order::query()
            ->where('affiliate_user_id', auth()->id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('user.affiliate.orders', compact('orders'));
    }
    public function createLinkForm(Request $request, DarmawisataClient $dw)
    {
        $this->guardAffiliate();

        $q = trim((string) $request->get('q', ''));
        $type = trim((string) $request->get('type', ''));

        $flightSearch = [
            'tripType' => (string) $request->get('tripType', 'OneWay'),
            'origin' => strtoupper(trim((string) $request->get('origin', ''))),
            'destination' => strtoupper(trim((string) $request->get('destination', ''))),
            'departDate' => (string) $request->get('departDate', ''),
            'returnDate' => (string) $request->get('returnDate', ''),
            'paxAdult' => (int) $request->get('paxAdult', 1),
            'paxChild' => (int) $request->get('paxChild', 0),
            'paxInfant' => (int) $request->get('paxInfant', 0),
        ];

        // coupons milik user (dibuat dari promo admin)
        $userCoupons = AffiliateUserCoupon::with('promo')
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        // ambil produk sesuai filter
        $products = collect();

        $match = function ($query) use ($q) {
            if ($q === '') return $query;
            return $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")

                    ->orWhere('slug', 'like', "%{$q}%");
            });
        };

        if ($type === '' || $type === 'tour') {
            $items = $match(TourPackage::query())
                ->select(['id', 'title as name', 'slug'])
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($x) => ['type' => 'tour', 'id' => $x->id, 'name' => $x->name, 'slug' => $x->slug]);
            $products = $products->merge($items);
        }
        if ($type === '' || $type === 'umrah') {
            $items = $match(UmrahPackage::query())
                ->select(['id', 'title as name', 'slug'])
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($x) => ['type' => 'umrah', 'id' => $x->id, 'name' => $x->name, 'slug' => $x->slug]);
            $products = $products->merge($items);
        }
        if ($type === '' || $type === 'rent_car') {
            $items = $match(RentCarPackage::query())
                ->select(['id', 'title as name', 'slug'])
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($x) => ['type' => 'rent_car', 'id' => $x->id, 'name' => $x->name, 'slug' => $x->slug]);

            $products = $products->merge($items);
        }
        if ($type === '' || $type === 'ship') {
            $items = $match(ShipPackage::query())
                ->select(['id', 'title as name', 'slug'])
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($x) => ['type' => 'ship', 'id' => $x->id, 'name' => $x->name, 'slug' => $x->slug]);
            $products = $products->merge($items);
        }

        if ($type === '' || $type === 'mice') {
            $items = $match(MicePackage::query())
                ->select(['id', 'title as name', 'slug'])
                ->orderByDesc('id')->limit(50)->get()
                ->map(fn($x) => [
                    'type' => 'mice',
                    'id' => $x->id,
                    'name' => $x->name,
                    'slug' => $x->slug
                ]);

            $products = $products->merge($items);
        }

        if ($type === '' || $type === 'flight') {
            $items = collect();

            $hasFlightSearch =
                $type === 'flight' &&
                filled($flightSearch['origin']) &&
                filled($flightSearch['destination']) &&
                filled($flightSearch['departDate']);

            if ($hasFlightSearch) {
                $request->merge([
                    'origin' => $flightSearch['origin'],
                    'destination' => $flightSearch['destination'],
                ]);

                $request->validate([
                    'tripType' => 'nullable|in:OneWay,RoundTrip',
                    'origin' => 'required|string|max:10',
                    'destination' => 'required|string|max:10|different:origin',
                    'departDate' => 'required|date',
                    'returnDate' => 'nullable|date|after_or_equal:departDate',
                    'paxAdult' => 'required|integer|min:1|max:9',
                    'paxChild' => 'nullable|integer|min:0|max:9',
                    'paxInfant' => 'nullable|integer|min:0|max:9',
                ]);

                if (($flightSearch['tripType'] ?? 'OneWay') === 'RoundTrip' && empty($flightSearch['returnDate'])) {
                    return back()
                        ->withErrors(['returnDate' => 'Tanggal pulang wajib diisi untuk RoundTrip.'])
                        ->withInput();
                }

                try {
                    $resp = $dw->scheduleAllAirline($flightSearch);

                    if (
                        strtoupper((string) ($resp['status'] ?? '')) !== 'SUCCESS'
                        && count((array) ($resp['journeyDepart'] ?? [])) === 0
                    ) {
                        return back()
                            ->withErrors([
                                'flight_search' => (string) ($resp['respMessage'] ?? 'Gagal mengambil jadwal tiket pesawat.')
                            ])
                            ->withInput();
                    }

                    $journeysDepart = array_values((array) ($resp['journeyDepart'] ?? []));
                    $journeysReturn = array_values((array) ($resp['journeyReturn'] ?? []));
                    $tripType = (string) ($flightSearch['tripType'] ?? 'OneWay');

                    if ($tripType === 'RoundTrip') {
                        foreach ($journeysDepart as $journeyDepart) {
                            $departAirlineId = (string) data_get($journeyDepart, 'airlineID', '');

                            foreach ($journeysReturn as $journeyReturn) {
                                $returnAirlineId = (string) data_get($journeyReturn, 'airlineID', '');

                                if ($departAirlineId === '' || $returnAirlineId === '' || $departAirlineId !== $returnAirlineId) {
                                    continue;
                                }

                                $key = (string) Str::uuid();

                                Cache::put(
                                    "flight_quote:{$key}",
                                    [
                                        'search' => $flightSearch,
                                        'journey' => $journeyDepart,
                                        'journey_return' => $journeyReturn,
                                    ],
                                    now()->addMinutes(30)
                                );

                                $airlineName = (string) data_get($journeyDepart, 'airlineName', data_get($journeyDepart, 'airlineID', 'Flight'));
                                $origin = strtoupper((string) data_get($journeyDepart, 'jiOrigin', $flightSearch['origin']));
                                $destination = strtoupper((string) data_get($journeyDepart, 'jiDestination', $flightSearch['destination']));
                                $departAt = (string) data_get($journeyDepart, 'deptDate', data_get($journeyDepart, 'journeyDate', $flightSearch['departDate']));
                                $price = (float) data_get($journeyDepart, 'sumPrice', 0);

                                $items->push([
                                    'type' => 'flight',
                                    'id' => $key,
                                    'name' => trim($airlineName . ' | ' . $origin . ' → ' . $destination . ' | ' . $departAt),
                                    'slug' => 'Rp ' . number_format((int) round($price), 0, ',', '.'),
                                ]);
                            }
                        }
                    } else {
                        foreach ($journeysDepart as $journey) {
                            $key = (string) Str::uuid();

                            Cache::put(
                                "flight_quote:{$key}",
                                [
                                    'search' => $flightSearch,
                                    'journey' => $journey,
                                    'journey_return' => [],
                                ],
                                now()->addMinutes(30)
                            );

                            $airlineName = (string) data_get($journey, 'airlineName', data_get($journey, 'airlineID', 'Flight'));
                            $origin = strtoupper((string) data_get($journey, 'jiOrigin', $flightSearch['origin']));
                            $destination = strtoupper((string) data_get($journey, 'jiDestination', $flightSearch['destination']));
                            $departAt = (string) data_get($journey, 'deptDate', data_get($journey, 'journeyDate', $flightSearch['departDate']));
                            $price = (float) data_get($journey, 'sumPrice', 0);

                            $items->push([
                                'type' => 'flight',
                                'id' => $key,
                                'name' => trim($airlineName . ' | ' . $origin . ' → ' . $destination . ' | ' . $departAt),
                                'slug' => 'Rp ' . number_format((int) round($price), 0, ',', '.'),
                            ]);
                        }
                    }

                    if ($items->isEmpty()) {
                        return back()
                            ->withErrors(['flight_search' => 'Tidak ada tiket pesawat yang cocok untuk filter ini.'])
                            ->withInput();
                    }
                } catch (\Throwable $e) {
                    report($e);

                    return back()
                        ->withErrors(['flight_search' => 'Gagal mengambil tiket pesawat: ' . $e->getMessage()])
                        ->withInput();
                }
            }

            $products = $products->merge($items);
        }

        return view('user.affiliate.links-create', compact('products', 'userCoupons', 'q', 'type', 'flightSearch'));
    }
    public function withdrawals(Request $request)
    {
        $this->guardAffiliate();

        $status = trim((string)$request->get('status', ''));
        $q = trim((string)$request->get('q', ''));

        // saldo: komisi approved - paid - yang sudah diminta (pending/approved)
        $userId = auth()->id();

        // saldo: komisi yang masih "approved" (belum masuk request withdrawal)
        $userId = auth()->id();

        $balance = (float) \App\Models\Order::where('affiliate_user_id', $userId)
            ->where('affiliate_commission_status', 'approved')
            ->sum('affiliate_commission_amount');


        $reqs = \App\Models\AffiliateWithdrawalRequest::query()
            ->where('user_id', $userId)
            ->when(in_array($status, ['pending', 'approved', 'declined', 'paid'], true), fn($qq) => $qq->where('status', $status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('user.affiliate.withdrawals', compact('reqs', 'status', 'q', 'balance'));
    }

    public function submitWithdrawal(Request $request)
    {
        $this->guardAffiliate();

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payout_method' => ['required', 'in:bank,ewallet'],
            'payout_provider' => ['required', 'string', 'max:60'],
            'account_name' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:80'],
        ]);

        // recompute balance (harus sama seperti withdrawals())
        $userId = auth()->id();

        $balance = (float) \App\Models\Order::where('affiliate_user_id', $userId)
            ->where('affiliate_commission_status', 'approved')
            ->sum('affiliate_commission_amount');

        if ((float) $data['amount'] > (float) $balance) {
            $isEn = app()->getLocale() === 'en';

            return back()->withErrors([
                'amount' => $isEn
                    ? 'Insufficient balance for this withdrawal amount.'
                    : 'Saldo tidak cukup untuk nominal penarikan ini.'
            ])->withInput();
        }

        /**
         * Link withdrawal ke order commission (biar status sinkron).
         * Strategi: ambil order komisi yang statusnya "approved" (belum pernah ditarik),
         * lalu lakukan greedy match sampai jumlahnya PAS = amount yang diminta user.
         *
         * Kalau jumlahnya tidak bisa PAS, request ditolak (user harus sesuaikan nominal).
         * Ini sengaja supaya gak ada kasus "withdraw Rp100.000 tapi order yang dipilih Rp120.000".
         */
        $eligibleOrders = \App\Models\Order::query()
            ->where('affiliate_user_id', $userId)
            ->where('affiliate_commission_status', 'approved')
            ->whereNotNull('affiliate_commission_amount')
            ->orderBy('id') // FIFO biar predictable
            ->get(['id', 'affiliate_commission_amount']);

        $target = (float) $data['amount'];
        $picked = [];
        $remaining = $target;

        foreach ($eligibleOrders as $o) {
            $amt = (float) $o->affiliate_commission_amount;

            if ($amt <= 0) continue;
            if ($amt > $remaining) continue;

            $picked[] = $o;
            $remaining = (float) ($remaining - $amt);

            // aman untuk float (cuma 2 decimal), tapi tetap kasih toleransi kecil
            if (abs($remaining) < 0.00001) {
                $remaining = 0.0;
                break;
            }
        }

        if ($remaining > 0) {
            return back()->withErrors([
                'amount' => $isEn
                    ? 'Withdrawal amount must match EXACTLY the total available approved commissions. Please adjust the amount (e.g., sum of several commissions).'
                    : 'Nominal penarikan harus PAS dengan total komisi approved yang tersedia. Coba ambil nominal yang sesuai (mis. gabungan beberapa komisi yang ada).'
            ])->withInput();
        }

        $req = \App\Models\AffiliateWithdrawalRequest::create([
            'user_id' => $userId,
            'amount' => $data['amount'],
            'payout_method' => $data['payout_method'],
            'payout_provider' => $data['payout_provider'],
            'account_name' => $data['account_name'],
            'account_number' => $data['account_number'],
            'status' => 'pending',
        ]);

        // Buat item + ubah status commission order ikut status request (pending)
        foreach ($picked as $o) {
            \App\Models\AffiliateWithdrawalItem::create([
                'withdrawal_request_id' => $req->id,
                'order_id' => $o->id,
                'amount' => $o->affiliate_commission_amount,
            ]);

            \App\Models\Order::where('id', $o->id)->update([
                'affiliate_commission_status' => 'pending',
            ]);
        }


        return back()->with('success', 'Permintaan penarikan berhasil dikirim.');
    }
}
