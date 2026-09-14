<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\Darmawisata\DarmawisataClient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\AffiliateLink;
use App\Services\FlightPriceOverrideService;

class FlightBookingController extends Controller
{

    protected function hasValidPriceContext(array $ctx): bool
    {
        $reservationPriceStatus = strtoupper((string) data_get(
            $ctx,
            'reservation_price.status',
            data_get($ctx, 'reservation_price_status', data_get($ctx, 'price_status', ''))
        ));

        if ($reservationPriceStatus === 'SUCCESS') {
            return true;
        }

        // FIX: Fallback ke priceAllAirline jika Airline/Price gagal
        // PriceAllAirline SUCCESS sudah cukup untuk melanjutkan booking flow
        $priceAllAirlineStatus = strtoupper((string) data_get(
            $ctx,
            'price_all_airline.status',
            data_get($ctx, 'price_all_airline_status', '')
        ));

        return $priceAllAirlineStatus === 'SUCCESS';
    }
    public function addons(Request $request, string $key, DarmawisataClient $dw)
    {
        $ctx = $this->getContext($key);

        if (!$this->hasValidPriceContext($ctx)) {
            return response()->json([
                'message' => 'Harga supplier belum valid. Tidak bisa lanjut ke add-ons.',
                'price' => (array) ($ctx['price'] ?? []),
                'reservation_price' => (array) ($ctx['reservation_price'] ?? []),
                'price_all_airline' => (array) ($ctx['price_all_airline'] ?? []),
            ], 422);
        }

        $data = $request->validate([
            'contact.first_name' => 'required|string|max:80',
            'contact.last_name' => 'required|string|max:80',
            'contact.title' => 'required|string|max:10',
            'contact.phone' => 'required|string|max:30',
            'contact.email' => 'required|email|max:120',
            'insurance' => 'nullable|boolean',
            'paxDetails' => 'required|array|min:1',
            'paxDetails.*.IDNumber' => 'nullable|string|max:80',
            'paxDetails.*.title' => 'required|string|max:10',
            'paxDetails.*.firstName' => 'required|string|max:80',
            'paxDetails.*.lastName' => 'required|string|max:80',
            'paxDetails.*.birthDate' => 'required|date',
            'paxDetails.*.gender' => 'required|string|max:20',
            'paxDetails.*.nationality' => 'required|string|max:10',
            'paxDetails.*.birthCountry' => 'required|string|max:10',
            'paxDetails.*.DocType' => 'nullable|string|max:20',
            'paxDetails.*.parent' => 'nullable|string|max:80',
            'paxDetails.*.passportNumber' => 'nullable|string|max:50',
            'paxDetails.*.passportIssuedCountry' => 'nullable|string|max:10',
            'paxDetails.*.passportIssuedDate' => 'nullable|date',
            'paxDetails.*.passportExpiredDate' => 'nullable|date',
            'paxDetails.*.Email' => 'nullable|email|max:120',
            'paxDetails.*.type' => 'required',
            'paxDetails.*.batikMilesNo' => 'nullable|string|max:50',
            'paxDetails.*.garudaFrequentFlyer' => 'nullable|string|max:50',
            'paxDetails.*.SSR' => 'nullable|string|max:100',
        ]);

        $data['contact'] = $this->normalizeContactInput((array) ($data['contact'] ?? []));

        $paxDetails = $this->normalizePassengerAddOns(array_values($data['paxDetails']));
        $paxDetails = $this->normalizePaxDetailsForAddonSeat($ctx, $paxDetails);
        $paxDetails = $this->resolveAndReorderInfantParents($ctx, $paxDetails);

        $payload = array_merge(
            $this->basePayloadFromContext($ctx),
            $this->contactPayload($data),
            $this->resolveAddonSeatMeta($ctx),
            [
                'paxDetails' => $paxDetails,
                'insurance' => (bool) ($data['insurance'] ?? false),
            ]
        );

        $resp = $dw->baggageAndMeal($payload);

        Log::info('Airline/BaggageAndMeal response', [
            'quote_key' => $key,
            'status' => (string) ($resp['status'] ?? ''),
            'respMessage' => (string) ($resp['respMessage'] ?? ''),
            'data' => $resp,
        ]);

        if (
            strtoupper((string) ($resp['status'] ?? '')) === 'SUCCESS' &&
            !$this->hasAddonChoices($resp)
        ) {
            Log::warning('Airline/BaggageAndMeal success but empty options', [
                'quote_key' => $key,
                'schedule_codes' => (array) data_get($ctx, 'schedule_codes', []),
                'selected_schedules' => (array) data_get($ctx, 'selected_schedules', []),
                'payload' => $payload,
                'response' => $resp,
            ]);
        }

        $ctx['contact'] = $data['contact'];
        $ctx['insurance'] = (bool) ($data['insurance'] ?? false);
        $ctx['paxDetails'] = $paxDetails;
        $ctx['addons_response'] = $resp;

        $this->putContext($key, $ctx);

        if (strtoupper((string) ($resp['status'] ?? '')) !== 'SUCCESS') {
            return response()->json($resp, 422);
        }

        return response()->json($resp);
    }

    public function seat(Request $request, string $key, DarmawisataClient $dw)
    {
        $ctx = Cache::get("flight_booking_ctx:{$key}");

        if (!is_array($ctx) || empty($ctx['search']) || empty($ctx['journey']) || empty($ctx['contact'])) {
            return response()->json([
                'message' => 'Booking context expired.'
            ], 422);
        }

        $search = (array) ($ctx['search'] ?? []);
        $journey = (array) ($ctx['journey'] ?? []);
        $journeyReturn = (array) ($ctx['journey_return'] ?? []);
        $contact = (array) ($ctx['contact'] ?? []);
        $addonsResponse = (array) ($ctx['addons_response'] ?? []);

        try {
            $payload = array_merge(
                $this->basePayloadFromContext($ctx),
                $this->resolveAddonSeatMeta($ctx),
                [
                    'contactFirstName' => (string) ($contact['first_name'] ?? ''),
                    'contactLastName' => (string) ($contact['last_name'] ?? ''),
                    'contactTitle' => (string) ($contact['title'] ?? 'MR'),
                    'contactCountryCodePhone' => (string) ($contact['country_code_phone'] ?? '62'),
                    'contactAreaCodePhone' => (string) ($contact['area_code_phone'] ?? ''),
                    'contactRemainingPhoneNo' => (string) ($contact['remaining_phone_no'] ?? ''),
                    'contactEmail' => (string) ($contact['email'] ?? ''),
                    'paxDetails' => (array) ($ctx['paxDetails'] ?? []),
                    'insurance' => (bool) ($ctx['insurance'] ?? false),
                ]
            );

            $resp = $dw->seat($payload);

            $ctx['seat_response'] = is_array($resp) ? $resp : [];

            if (
                strtoupper((string) ($resp['status'] ?? '')) === 'SUCCESS' &&
                !$this->hasSeatChoices((array) $resp)
            ) {
                Log::warning('Airline/Seat success but empty options', [
                    'quote_key' => $key,
                    'schedule_codes' => (array) data_get($ctx, 'schedule_codes', []),
                    'selected_schedules' => (array) data_get($ctx, 'selected_schedules', []),
                    'response' => $resp,
                ]);
            }
            Cache::put("flight_booking_ctx:{$key}", $ctx, now()->addMinutes(30));

            return response()->json($resp);
        } catch (\Throwable $e) {
            Log::warning('Airline/Seat failed but booking flow continues without seat selection', [
                'quote_key' => $key,
                'airlineID' => (string) data_get($journey, 'airlineID', ''),
                'message' => $e->getMessage(),
            ]);

            $fallback = [
                'seatAddOns' => [],
                'status' => 'SUCCESS',
                'respMessage' => 'Seat unavailable, continue without seat selection.',
                'seat_optional_failed' => true,
            ];

            $ctx['seat_response'] = $fallback;
            Cache::put("flight_booking_ctx:{$key}", $ctx, now()->addMinutes(30));

            return response()->json($fallback);
        }
    }

    public function book(Request $request, string $key, DarmawisataClient $dw, FlightPriceOverrideService $flightPricing)
    {
        $ctx = $this->getContext($key);

        if (empty($ctx['paxDetails'])) {
            return response()->json([
                'message' => 'Pax details belum ada. Call addons dulu.',
            ], 422);
        }

        if (empty($ctx['addons_response'])) {
            return response()->json([
                'message' => 'Flow booking belum valid. Airline/BaggageAndMeal wajib dieksekusi dulu sesuai dokumentasi.',
            ], 422);
        }

        if (strtoupper((string) data_get($ctx, 'addons_response.status', '')) !== 'SUCCESS') {
            return response()->json([
                'message' => (string) data_get($ctx, 'addons_response.respMessage', 'AddOn supplier belum SUCCESS.'),
                'addons' => (array) ($ctx['addons_response'] ?? []),
            ], 422);
        }

        if (!$this->hasValidPriceContext($ctx)) {
            return response()->json([
                'message' => 'Flow booking belum valid. Harga supplier belum valid sebelum booking.',
                'price' => (array) ($ctx['price'] ?? []),
                'reservation_price' => (array) ($ctx['reservation_price'] ?? []),
                'price_all_airline' => (array) ($ctx['price_all_airline'] ?? []),
            ], 422);
        }

        $data = $request->validate([

            'paxDetails' => 'required|array|min:1',
            'paxDetails.*.IDNumber' => 'nullable|string|max:80',
            'paxDetails.*.title' => 'required|string|max:10',
            'paxDetails.*.firstName' => 'required|string|max:80',
            'paxDetails.*.lastName' => 'required|string|max:80',
            'paxDetails.*.birthDate' => 'required|date',
            'paxDetails.*.gender' => 'required|string|max:20',
            'paxDetails.*.nationality' => 'required|string|max:10',
            'paxDetails.*.birthCountry' => 'required|string|max:10',
            'paxDetails.*.DocType' => 'nullable|string|max:20',
            'paxDetails.*.parent' => 'nullable|string|max:80',
            'paxDetails.*.passportNumber' => 'nullable|string|max:50',
            'paxDetails.*.passportIssuedCountry' => 'nullable|string|max:10',
            'paxDetails.*.passportIssuedDate' => 'nullable|date',
            'paxDetails.*.passportExpiredDate' => 'nullable|date',
            'paxDetails.*.Email' => 'nullable|email|max:120',
            'paxDetails.*.type' => 'required',
            'paxDetails.*.batikMilesNo' => 'nullable|string|max:50',
            'paxDetails.*.garudaFrequentFlyer' => 'nullable|string|max:50',
            'paxDetails.*.SSR' => 'nullable|string|max:100',
            'paxDetails.*.addOns' => 'nullable|array',
            'paxDetails.*.addOns.*.aoOrigin' => 'nullable|string|max:10',
            'paxDetails.*.addOns.*.aoDestination' => 'nullable|string|max:10',
            'paxDetails.*.addOns.*.baggageString' => 'nullable|string|max:100',
            'paxDetails.*.addOns.*.meals' => 'nullable|array',
            'paxDetails.*.addOns.*.meals.*' => 'nullable|string|max:100',
            'paxDetails.*.addOns.*.seat' => 'nullable|string|max:20',
            'paxDetails.*.addOns.*.compartment' => 'nullable|string|max:100',
        ]);

        $data['contact'] = $this->normalizeContactInput((array) ($data['contact'] ?? []));
        $paxDetails = $this->normalizePassengerAddOns(array_values($data['paxDetails']));
        $paxDetails = $this->normalizePaxDetailsForBooking($ctx, $paxDetails);
        $paxDetails = $this->resolveAndReorderInfantParents($ctx, $paxDetails);

        $this->assertBookingBusinessRules($ctx, $paxDetails);
        $this->assertBookingPaxRules($ctx, $paxDetails);

        $bookingSchDeparts = array_values((array) data_get($ctx, 'selected_schedules.schDeparts', []));
        $bookingSchReturns = array_values((array) data_get($ctx, 'selected_schedules.schReturns', []));
        $bookingPayload = array_merge(
            $this->basePayloadFromContext($ctx),
            $this->contactPayload([
                'contact' => $ctx['contact'],
            ]),
            [
                'paxDetails' => $paxDetails,
                'insurance' => (bool) ($ctx['insurance'] ?? false),
                'searchKey' => (string) data_get($ctx, 'reservation_price.searchKey', ''),
                'schDeparts' => $bookingSchDeparts,
                'schReturns' => $bookingSchReturns,
            ]
        );

        Log::info('Airline/Booking prepared payload', [
            'quote_key' => $key,
            'searchKey' => $bookingPayload['searchKey'] ?? '',
            'airlineID' => $bookingPayload['airlineID'] ?? null,
            'pax_summary' => array_map(function ($pax, $idx) {
                return [
                    'idx' => $idx,
                    'type' => $pax['type'] ?? null,
                    'firstName' => $pax['firstName'] ?? null,
                    'lastName' => $pax['lastName'] ?? null,
                    'parent' => $pax['parent'] ?? null,
                ];
            }, $bookingPayload['paxDetails'] ?? [], array_keys($bookingPayload['paxDetails'] ?? [])),
        ]);

        Log::info('Flight booking context before supplier booking', [
            'quote_key' => $key,
            'price_status' => (string) data_get($ctx, 'price.status', ''),
            'price_searchKey' => (string) data_get($ctx, 'price.searchKey', ''),
            'selected_schedules' => (array) data_get($ctx, 'selected_schedules', []),
            'schedule_codes' => (array) data_get($ctx, 'schedule_codes', []),
        ]);
        try {
            $priceRefreshParams = array_merge(
                $this->basePayloadFromContext($ctx),
                [
                    'searchKey'  => (string) data_get($ctx, 'reservation_price.searchKey', ''),
                    'promoCode'  => (string) data_get($ctx, 'search.promoCode', ''),
                    'schDeparts' => $bookingSchDeparts,
                    'schReturns' => $bookingSchReturns,
                ]
            );
            $freshPrice = $dw->priceAirline($priceRefreshParams);
            Log::info('Pre-booking Airline/Price session re-establish', [
                'quote_key'   => $key,
                'status'      => $freshPrice['status'] ?? null,
                'respMessage' => $freshPrice['respMessage'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Pre-booking Airline/Price re-establish gagal (non-fatal, lanjut booking)', [
                'quote_key' => $key,
                'error'     => $e->getMessage(),
            ]);
        }
        try {
            $resp = $dw->bookingAirline($bookingPayload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('Airline/Booking connection timeout, attempting BookingList recovery', [
                'quote_key' => $key,
                'error'     => $e->getMessage(),
            ]);
            $recovered = $this->recoverBookingFromBookingList($key, $ctx, $dw);
            if ($recovered) {
                return response()->json([
                    'booking'   => ['status' => 'TIMEOUT', 'respMessage' => 'timeout'],
                    'recovered' => $recovered,
                ], 200);
            }
            return response()->json([
                'message' => 'Booking timeout. Silakan cek riwayat pesanan beberapa saat lagi.',
                'error'   => $e->getMessage(),
            ], 500);
        }
        Log::info('Airline/Booking response', [
            'quote_key' => $key,
            'status' => (string) ($resp['status'] ?? ''),
            'respMessage' => (string) ($resp['respMessage'] ?? ''),
            'data' => $resp,
        ]);

        if (strtoupper((string) ($resp['status'] ?? '')) !== 'SUCCESS') {
            $respMessage = Str::lower((string) ($resp['respMessage'] ?? ''));
            $hasBookingIdentity =
                trim((string) ($resp['bookingCode'] ?? '')) !== '' &&
                trim((string) ($resp['bookingDate'] ?? '')) !== '';

            $mustCheckDetail =
                $hasBookingIdentity &&
                (
                    str_contains($respMessage, 'ticket status is processed') ||
                    str_contains($respMessage, 'please check booking detail') ||
                    str_contains($respMessage, 'timeout')
                );

            if ($mustCheckDetail) {
                $detailResp = $dw->bookingDetailAirline([
                    'bookingCode' => (string) $resp['bookingCode'],
                    'referenceNo' => (string) ($resp['referenceNo'] ?? ''),
                    'bookingDate' => (string) $resp['bookingDate'],
                ]);

                Log::warning('Airline/Booking failed but BookingDetail checked', [
                    'quote_key' => $key,
                    'booking' => $resp,
                    'detail' => $detailResp,
                ]);

                return response()->json([
                    'booking' => $resp,
                    'detail' => $detailResp,
                ], strtoupper((string) ($detailResp['status'] ?? '')) === 'SUCCESS' ? 200 : 422);
            }

            $recovered = $this->recoverBookingFromBookingList($key, $ctx, $dw);

            if ($recovered) {
                return response()->json([
                    'booking' => $resp,
                    'recovered' => $recovered,
                ], 200);
            }

            return response()->json($resp, 422);
        }

        $search = (array) ($ctx['search'] ?? []);
        $journey = (array) ($ctx['journey'] ?? []);
        $journeyReturn = (array) ($ctx['journey_return'] ?? []);
        $price = (array) ($ctx['price'] ?? []);
        $userId = auth()->id() ?: User::where('email', data_get($ctx, 'contact.email'))->value('id');

        $supplierFare = (float) ($resp['salesPrice'] ?? $resp['ticketPrice'] ?? $price['sumFare'] ?? $journey['sumPrice'] ?? 0);
        $pricingData = $flightPricing->resolve(
            $search,
            $journey,
            $journeyReturn,
            $supplierFare,
            (string) ($resp['currency'] ?? $price['currency'] ?? $journey['currency'] ?? 'IDR')
        );

        $payableFare = (int) round((float) ($pricingData['final_price'] ?? $supplierFare));

        $affUserId = session('affiliate_user_id');
        $affLinkId = session('affiliate_link_id');
        $affRef = session('affiliate_ref');

        $affType = null;
        $affValue = null;
        $affAmount = null;
        $affStatus = null;

        if ($affUserId && $affLinkId && $affRef) {
            $affUser = User::find($affUserId);

            if ($affUser && $affUser->is_affiliate) {
                $affType = $affUser->affiliate_commission_type ?: 'percent';
                $affValue = (float) ($affUser->affiliate_commission_value ?: 0);

                if ($affType === 'percent') {
                    $affAmount = (int) round(($payableFare * $affValue) / 100);
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

        $orderData = [
            'invoice_number' => 'INV-' . date('YmdHis') . rand(1000, 9999),
            'type' => 'flight',
            'product_id' => 0,
            'product_name' => trim('Tiket Pesawat ' . ($search['origin'] ?? '') . '-' . ($search['destination'] ?? '')),
            'user_id' => $userId,
            'customer_name' => trim(data_get($ctx, 'contact.first_name') . ' ' . data_get($ctx, 'contact.last_name')),
            'customer_email' => (string) data_get($ctx, 'contact.email'),
            'customer_phone' => (string) (
                data_get($ctx, 'contact.country_code_phone') .
                data_get($ctx, 'contact.area_code_phone') .
                data_get($ctx, 'contact.remaining_phone_no')
            ),
            'departure_date' => $search['departDate'] ?? null,
            'participants' => max(1, (int) ($search['paxAdult'] ?? 1) + (int) ($search['paxChild'] ?? 0) + (int) ($search['paxInfant'] ?? 0)),
            'affiliate_user_id' => $affUserId,
            'affiliate_link_id' => $affLinkId,
            'affiliate_ref' => $affRef,
            'affiliate_commission_type' => $affType,
            'affiliate_commission_value' => $affValue,
            'affiliate_commission_amount' => $affAmount,
            'affiliate_commission_status' => $affStatus,
            'subtotal' => $payableFare,
            'discount' => 0,
            'final_price' => $payableFare,
            'payment_status' => 'waiting_payment',
            'order_status' => 'pending',
        ];
        if (Schema::hasColumn('orders', 'meta')) {
            $orderData['meta'] = [
                'provider' => 'darmawisata',
                'quote_key' => $key,
                'search' => $search,
                'journey' => $journey,
                'journey_return' => (array) ($ctx['journey_return'] ?? []),
                'price' => $price,
                'contact' => $ctx['contact'],
                'paxDetails' => $paxDetails,
                'addons_response' => $ctx['addons_response'] ?? null,
                'seat_response' => $ctx['seat_response'] ?? null,
                'pricing' => [
                    'pricing_key' => $pricingData['pricing_key'] ?? null,
                    'supplier_price' => $pricingData['supplier_price'] ?? $supplierFare,
                    'manual_price' => $pricingData['manual_price'] ?? null,
                    'final_price' => $pricingData['final_price'] ?? $payableFare,
                    'is_manual' => (bool) ($pricingData['is_manual'] ?? false),
                    'override_id' => $pricingData['override_id'] ?? null,
                ],
                'supplier_booking' => $resp,
                'supplier_status' => [
                    'booking_status' => (string) ($resp['status'] ?? ''),
                    'ticket_status' => 'HOLD',
                ],
            ];
        }

        $order = Order::create($orderData);

        if ($affLinkId) {
            AffiliateLink::where('id', $affLinkId)->increment('conversions');
        }

        $ctx['paxDetails'] = $paxDetails;
        $ctx['booking_response'] = $resp;
        $ctx['order_id'] = $order->id;
        $this->putContext($key, $ctx);

        return response()->json([
            'order_id' => $order->id,
            'redirect' => route('checkout.show', $order->id),
            'booking' => $resp,
        ]);
    }

    public function issued(Order $order, DarmawisataClient $dw)
    {
        $meta = (array) ($order->meta ?? []);
        $booking = (array) ($meta['supplier_booking'] ?? []);
        $search = (array) ($meta['search'] ?? []);
        $journey = (array) ($meta['journey'] ?? []);

        if (empty($booking['bookingCode']) || empty($booking['bookingDate'])) {
            return response()->json([
                'message' => 'Order belum punya bookingCode / bookingDate supplier.',
            ], 422);
        }

        $issuedResp = $dw->issuedAirline([
            'airlineID' => (string) data_get($booking, 'airlineID', data_get($journey, 'airlineID', '')),
            'origin' => strtoupper((string) ($search['origin'] ?? data_get($journey, 'jiOrigin', ''))),
            'destination' => strtoupper((string) ($search['destination'] ?? data_get($journey, 'jiDestination', ''))),
            'tripType' => (string) ($search['tripType'] ?? 'OneWay'),
            'departDate' => (string) ($search['departDate'] ?? ''),
            'returnDate' => (string) ($search['returnDate'] ?? ''),
            'bookingCode' => (string) $booking['bookingCode'],
            'bookingDate' => (string) $booking['bookingDate'],
            'airlineAccessCode' => (string) data_get($meta, 'price.airlineAccessCode', data_get($journey, 'airlineAccessCode', '')),
        ]);

        $detailResp = $this->resolveBookingDetailAfterIssued($dw, $booking, $issuedResp);

        $meta['supplier_issued'] = $issuedResp;
        $meta['supplier_booking_detail'] = $detailResp;
        $meta['supplier_status'] = [
            'issued_status' => (string) ($issuedResp['status'] ?? ''),
            'issued_resp_message' => (string) ($issuedResp['respMessage'] ?? ''),
            'booking_status' => (string) ($issuedResp['bookingStatus'] ?? ''),
            'ticket_status' => (string) ($detailResp['ticketStatus'] ?? ''),
            'ticket_detail' => (string) ($detailResp['ticketDetail'] ?? ''),
        ];

        $order->meta = $meta;

        $ticketStatus = $this->normalizeTicketStatus((string) ($detailResp['ticketStatus'] ?? ''));
        if ($ticketStatus === 'TICKETED') {
            $order->order_status = 'approved';
        } elseif ($ticketStatus === 'CANCELLED') {
            $order->order_status = 'cancelled';
        } else {
            $order->order_status = 'pending';
        }

        $order->save();

        return response()->json([
            'issued' => $issuedResp,
            'detail' => $detailResp,
        ]);
    }

    public function detail(Order $order, DarmawisataClient $dw)
    {
        $meta = (array) ($order->meta ?? []);
        $booking = (array) ($meta['supplier_booking'] ?? []);

        if (empty($booking['bookingCode']) || empty($booking['bookingDate'])) {
            return response()->json([
                'message' => 'Order belum punya bookingCode / bookingDate supplier.',
            ], 422);
        }

        $detailResp = $dw->bookingDetailAirline([
            'bookingCode' => (string) $booking['bookingCode'],
            'referenceNo' => (string) ($booking['referenceNo'] ?? ''),
            'bookingDate' => (string) $booking['bookingDate'],
        ]);

        $meta['supplier_booking_detail'] = $detailResp;
        $meta['supplier_status'] = [
            'ticket_status' => (string) ($detailResp['ticketStatus'] ?? ''),
            'ticket_detail' => (string) ($detailResp['ticketDetail'] ?? ''),
        ];

        $order->meta = $meta;

        $ticketStatus = $this->normalizeTicketStatus((string) ($detailResp['ticketStatus'] ?? ''));
        if ($ticketStatus === 'TICKETED') {
            $order->order_status = 'approved';
        } elseif ($ticketStatus === 'CANCELLED') {
            $order->order_status = 'cancelled';
        } else {
            $order->order_status = 'pending';
        }

        $order->save();

        return response()->json($detailResp);
    }

    protected function resolveBookingDetailAfterIssued(
        DarmawisataClient $dw,
        array $booking,
        array $issuedResp
    ): array {
        $detailResp = [];
        $maxAttempts = 4;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $detailResp = $dw->bookingDetailAirline([
                'bookingCode' => (string) ($booking['bookingCode'] ?? ''),
                'referenceNo' => (string) ($booking['referenceNo'] ?? ''),
                'bookingDate' => (string) ($booking['bookingDate'] ?? ''),
            ]);

            $ticketStatus = $this->normalizeTicketStatus((string) ($detailResp['ticketStatus'] ?? ''));
            if (in_array($ticketStatus, ['TICKETED', 'CANCELLED', 'HOLD'], true)) {
                return $detailResp;
            }

            $issuedMessage = Str::lower((string) ($issuedResp['respMessage'] ?? ''));
            $detailMessage = Str::lower((string) ($detailResp['respMessage'] ?? ''));
            $ticketDetail = Str::lower((string) ($detailResp['ticketDetail'] ?? ''));

            $mustRecheck =
                str_contains($issuedMessage, 'ticket status is processed') ||
                str_contains($issuedMessage, 'please check booking detail') ||
                str_contains($issuedMessage, 'timeout') ||
                str_contains($detailMessage, 'processed') ||
                str_contains($ticketDetail, 'processed');

            if (!$mustRecheck) {
                return $detailResp;
            }

            usleep(500000);
        }

        return $detailResp;
    }

    protected function normalizeTicketStatus(?string $status): string
    {
        $value = strtoupper(trim((string) $status));

        return match ($value) {
            'CANCELED' => 'CANCELLED',
            default => $value,
        };
    }

    protected function recoverBookingFromBookingList(string $key, array $ctx, DarmawisataClient $dw): ?array
    {
        $start = now()->subMinutes(15)->toIso8601String();
        $end = now()->addMinutes(5)->toIso8601String();

        $listResp = $dw->bookingListAirline([
            'filterByStatus' => 0,
            'startDate' => $start,
            'endDate' => $end,
        ]);

        Log::warning('Airline/BookingList recovery response', [
            'quote_key' => $key,
            'data' => $listResp,
        ]);

        if (strtoupper((string) data_get($listResp, 'status', '')) !== 'SUCCESS') {
            return null;
        }

        $airlineID = strtoupper((string) data_get($ctx, 'airlineID', ''));
        $expectedRoute = strtoupper((string) data_get($ctx, 'search.origin', '') . '-' . (string) data_get($ctx, 'search.destination', ''));
        $expectedPax = max(
            1,
            (int) data_get($ctx, 'search.paxAdult', 1)
                + (int) data_get($ctx, 'search.paxChild', 0)
                + (int) data_get($ctx, 'search.paxInfant', 0)
        );

        $candidate = null;

        foreach ((array) data_get($listResp, 'bookingInfos', []) as $item) {
            $itemAirlineID = strtoupper((string) data_get($item, 'airlineID', ''));
            $itemRoute = strtoupper((string) data_get($item, 'route', ''));
            $itemPax = (int) data_get($item, 'pax', 0);

            if ($itemAirlineID !== $airlineID) {
                continue;
            }

            if ($itemRoute !== '' && $expectedRoute !== '' && !str_contains($itemRoute, $expectedRoute)) {
                continue;
            }

            if ($itemPax > 0 && $itemPax !== $expectedPax) {
                continue;
            }

            $candidate = $item;
            break;
        }

        if (!$candidate) {
            return null;
        }

        $detailResp = $dw->bookingDetailAirline([
            'bookingCode' => (string) data_get($candidate, 'bookingCode', ''),
            'referenceNo' => (string) data_get($candidate, 'referenceNo', ''),
            'bookingDate' => (string) data_get($candidate, 'bookingDate', ''),
        ]);

        Log::warning('Airline/Booking recovery detail checked', [
            'quote_key' => $key,
            'candidate' => $candidate,
            'detail' => $detailResp,
        ]);

        if (strtoupper((string) data_get($detailResp, 'status', '')) !== 'SUCCESS') {
            return null;
        }

        return [
            'booking_list' => $listResp,
            'booking_info' => $candidate,
            'detail' => $detailResp,
        ];
    }

    protected function getContext(string $key): array
    {
        $ctx = Cache::get("flight_booking_ctx:{$key}");

        if (is_array($ctx)) {
            $ctx['search'] = (array) ($ctx['search'] ?? []);
            $ctx['journey'] = (array) ($ctx['journey'] ?? []);
            $ctx['journey_return'] = (array) ($ctx['journey_return'] ?? []);
            $ctx['price'] = (array) ($ctx['price'] ?? []);

            if (
                !isset($ctx['schedule_codes']) ||
                !is_array($ctx['schedule_codes']) ||
                empty($ctx['schedule_codes']['schDepart'])
            ) {
                $ctx['schedule_codes'] = $this->extractScheduleCodes(
                    (array) ($ctx['journey'] ?? []),
                    (array) ($ctx['journey_return'] ?? [])
                );
            }

            $ctx['selected_schedules'] = [
                'schDeparts' => array_values((array) data_get($ctx, 'selected_schedules.schDeparts', [])),
                'schReturns' => array_values((array) data_get($ctx, 'selected_schedules.schReturns', [])),
            ];

            $this->putContext($key, $ctx);

            return $ctx;
        }

        $detail = Cache::get("flight_quote_detail:{$key}");
        if (!is_array($detail)) {
            $detail = Cache::get("flight_quote:{$key}");
        }

        if (!is_array($detail)) {
            abort(422, 'Data penerbangan sudah kadaluarsa. Cari ulang.');
        }

        $price = (array) ($detail['price'] ?? []);

        $ctx = [
            'search' => (array) ($detail['search'] ?? []),
            'journey' => (array) ($detail['journey'] ?? []),
            'journey_return' => (array) ($detail['journey_return'] ?? []),
            'price' => $price,
            'selected_schedules' => [
                'schDeparts' => $this->selectedScheduleModels(
                    (array) data_get($detail, 'journey.segment', []),
                    (string) data_get($detail, 'journey.journeyReference', '')
                ),
                'schReturns' => $this->selectedScheduleModels(
                    (array) data_get($detail, 'journey_return.segment', []),
                    (string) data_get($detail, 'journey_return.journeyReference', '')
                ),
            ],
            'schedule_codes' => $this->extractScheduleCodes(
                (array) ($detail['journey'] ?? []),
                (array) ($detail['journey_return'] ?? [])
            ),
        ];

        $this->putContext($key, $ctx);

        return $ctx;
    }

    protected function putContext(string $key, array $ctx): void
    {
        Cache::put("flight_booking_ctx:{$key}", $ctx, now()->addMinutes(30));
    }

    protected function basePayloadFromContext(array $ctx): array
    {
        $search = (array) ($ctx['search'] ?? []);
        $journey = (array) ($ctx['journey'] ?? []);
        $originalJourney = (array) ($ctx['original_journey'] ?? []);

        $airlineID = (string) (
            data_get($ctx, 'airlineID')
            ?: data_get($journey, 'airlineID')
            ?: data_get($originalJourney, 'airlineID')
            ?: data_get($journey, 'segment.0.flightDetail.0.airlineCode')
            ?: data_get($originalJourney, 'segment.0.flightDetail.0.airlineCode')
        );

        $origin = (string) (
            $search['origin']
            ?? data_get($journey, 'jiOrigin')
            ?? data_get($originalJourney, 'jiOrigin')
            ?? data_get($journey, 'segment.0.flightDetail.0.fdOrigin')
            ?? data_get($originalJourney, 'segment.0.flightDetail.0.fdOrigin')
            ?? ''
        );

        $destination = (string) (
            $search['destination']
            ?? data_get($journey, 'jiDestination')
            ?? data_get($originalJourney, 'jiDestination')
            ?? data_get($journey, 'segment.0.flightDetail.0.fdDestination')
            ?? data_get($originalJourney, 'segment.0.flightDetail.0.fdDestination')
            ?? ''
        );

        return [
            'airlineID' => strtoupper(trim($airlineID)),
            'origin' => strtoupper(trim($origin)),
            'destination' => strtoupper(trim($destination)),
            'tripType' => (string) ($search['tripType'] ?? 'OneWay'),
            'departDate' => (string) ($search['departDate'] ?? ''),

            'returnDate' => ($search['tripType'] ?? 'OneWay') !== 'RoundTrip'
                ? '0001-01-01T00:00:00'
                : (string) ($search['returnDate'] ?? ''),
            'paxAdult' => (int) ($search['paxAdult'] ?? 1),
            'paxChild' => (int) ($search['paxChild'] ?? 0),
            'paxInfant' => (int) ($search['paxInfant'] ?? 0),
        ];
    }


    protected function contactPayload(array $data): array
    {
        return [
            'contactFirstName' => (string) data_get($data, 'contact.first_name', ''),
            'contactLastName' => (string) data_get($data, 'contact.last_name', ''),
            'contactTitle' => (string) data_get($data, 'contact.title', ''),
            'contactCountryCodePhone' => (string) data_get($data, 'contact.country_code_phone', ''),
            'contactAreaCodePhone' => (string) data_get($data, 'contact.area_code_phone', ''),
            'contactRemainingPhoneNo' => (string) data_get($data, 'contact.remaining_phone_no', ''),
            'contactEmail' => (string) data_get($data, 'contact.email', ''),
        ];
    }

    protected function firstFilledString(...$values): string
    {
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }

            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    protected function resolveAddonSeatMeta(array $ctx): array
    {
        $journey = (array) ($ctx['journey'] ?? []);
        $journeyReturn = (array) ($ctx['journey_return'] ?? []);
        $selectedSchDeparts = array_values((array) data_get($ctx, 'selected_schedules.schDeparts', []));
        $selectedSchReturns = array_values((array) data_get($ctx, 'selected_schedules.schReturns', []));
        $scheduleCodes = (array) data_get($ctx, 'schedule_codes', []);
        $price = (array) data_get($ctx, 'price', []);
        $reservationPrice = (array) data_get($ctx, 'reservation_price', []);
        $priceAllAirline = (array) data_get($ctx, 'price_all_airline', []);

        $departJourneyReference = $this->firstFilledString(
            data_get($journey, 'journeyReference'),
            data_get($scheduleCodes, 'schDepart'),
            data_get($selectedSchDeparts, '0.detailSchedule'),
            data_get($selectedSchDeparts, '0.schDepart')
        );

        $returnJourneyReference = $this->firstFilledString(
            data_get($journeyReturn, 'journeyReference'),
            data_get($scheduleCodes, 'schReturn'),
            data_get($selectedSchReturns, '0.detailSchedule'),
            data_get($selectedSchReturns, '0.schDepart')
        );

        return [
            'schDepart' => $departJourneyReference,
            'schReturn' => $returnJourneyReference,
            'departureAirlineSegmentCode' => $this->firstFilledString(
                data_get($price, 'priceDepart.0.airlineSegmentCode'),
                data_get($reservationPrice, 'priceDepart.0.airlineSegmentCode'),
                data_get($priceAllAirline, 'priceDepart.0.airlineSegmentCode'),
                data_get($selectedSchDeparts, '0.airlineSegmentCode'),
                data_get($scheduleCodes, 'departureAirlineSegmentCode'),
                $this->resolveAirlineSegmentCodeFromJourney($journey, $departJourneyReference)
            ),
            'departureFareBasisCode' => $this->firstFilledString(
                data_get($price, 'priceDepart.0.classID'),
                data_get($price, 'priceDepart.0.classId'),
                data_get($price, 'priceDepart.0.classiId'),
                data_get($reservationPrice, 'priceDepart.0.classID'),
                data_get($reservationPrice, 'priceDepart.0.classId'),
                data_get($reservationPrice, 'priceDepart.0.classiId'),
                data_get($priceAllAirline, 'priceDepart.0.classID'),
                data_get($priceAllAirline, 'priceDepart.0.classId'),
                data_get($priceAllAirline, 'priceDepart.0.classiId'),
                data_get($selectedSchDeparts, '0.classID'),
                data_get($selectedSchDeparts, '0.classId'),
                data_get($selectedSchDeparts, '0.classiId'),
                data_get($selectedSchDeparts, '0.fareBasisCode'),
                data_get($selectedSchDeparts, '0.flightClass'),
                data_get($scheduleCodes, 'departureFareBasisCode'),
                $this->resolveFareBasisCodeFromJourney($journey, $departJourneyReference)
            ),
            'returnAirlineSegmentCode' => $this->firstFilledString(
                data_get($price, 'priceReturn.0.airlineSegmentCode'),
                data_get($reservationPrice, 'priceReturn.0.airlineSegmentCode'),
                data_get($priceAllAirline, 'priceReturn.0.airlineSegmentCode'),
                data_get($selectedSchReturns, '0.airlineSegmentCode'),
                data_get($scheduleCodes, 'returnAirlineSegmentCode'),
                $this->resolveAirlineSegmentCodeFromJourney($journeyReturn, $returnJourneyReference)
            ),
            'returnFareBasisCode' => $this->firstFilledString(
                data_get($price, 'priceReturn.0.classID'),
                data_get($price, 'priceReturn.0.classId'),
                data_get($price, 'priceReturn.0.classiId'),
                data_get($reservationPrice, 'priceReturn.0.classID'),
                data_get($reservationPrice, 'priceReturn.0.classId'),
                data_get($reservationPrice, 'priceReturn.0.classiId'),
                data_get($priceAllAirline, 'priceReturn.0.classID'),
                data_get($priceAllAirline, 'priceReturn.0.classId'),
                data_get($priceAllAirline, 'priceReturn.0.classiId'),
                data_get($selectedSchReturns, '0.classID'),
                data_get($selectedSchReturns, '0.classId'),
                data_get($selectedSchReturns, '0.classiId'),
                data_get($selectedSchReturns, '0.fareBasisCode'),
                data_get($selectedSchReturns, '0.flightClass'),
                data_get($scheduleCodes, 'returnFareBasisCode'),
                $this->resolveFareBasisCodeFromJourney($journeyReturn, $returnJourneyReference)
            ),
        ];
    }

    protected function normalizeContactInput(array $contact): array
    {
        $phone = preg_replace('/\D+/', '', (string) ($contact['phone'] ?? ''));

        if ($phone === '') {
            $phone = preg_replace(
                '/\D+/',
                '',
                (string) (($contact['country_code_phone'] ?? '') . ($contact['area_code_phone'] ?? '') . ($contact['remaining_phone_no'] ?? ''))
            );
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . ltrim(substr($phone, 1), '0');
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . ltrim($phone, '0');
        }

        $local = substr($phone, 2);
        $area = substr($local, 0, 3);
        $remaining = substr($local, 3);

        return [
            'first_name' => (string) ($contact['first_name'] ?? ''),
            'last_name' => (string) ($contact['last_name'] ?? ''),
            'title' => (string) ($contact['title'] ?? 'MR'),
            'phone' => $phone,
            'country_code_phone' => '62',
            'area_code_phone' => $area,
            'remaining_phone_no' => $remaining,
            'email' => (string) ($contact['email'] ?? ''),
        ];
    }

    protected function normalizePassengerAddOns(array $paxDetails): array
    {
        foreach ($paxDetails as $idx => $pax) {
            $normalizedAddOns = [];

            foreach ((array) data_get($pax, 'addOns', []) as $addOn) {
                if (!is_array($addOn)) {
                    continue;
                }

                $aoOrigin = strtoupper(trim((string) ($addOn['aoOrigin'] ?? $addOn['origin'] ?? '')));
                $aoDestination = strtoupper(trim((string) ($addOn['aoDestination'] ?? $addOn['destination'] ?? '')));
                $baggageString = trim((string) ($addOn['baggageString'] ?? $addOn['baggage'] ?? ''));
                $seat = trim((string) ($addOn['seat'] ?? ''));
                $compartment = trim((string) ($addOn['compartment'] ?? ''));

                $meals = [];
                if (isset($addOn['meals']) && is_array($addOn['meals'])) {
                    foreach ($addOn['meals'] as $meal) {
                        $meal = trim((string) $meal);
                        if ($meal !== '') {
                            $meals[] = $meal;
                        }
                    }
                } else {
                    $meal = trim((string) ($addOn['meal'] ?? ''));
                    if ($meal !== '') {
                        $meals[] = $meal;
                    }
                }

                $hasRealAddonValue =
                    $baggageString !== '' ||
                    count($meals) > 0 ||
                    $seat !== '' ||
                    $compartment !== '';

                if (!$hasRealAddonValue) {
                    continue;
                }

                $row = [
                    'aoOrigin' => $aoOrigin,
                    'aoDestination' => $aoDestination,
                    'baggageString' => $baggageString,
                    'meals' => array_values($meals),
                    'seat' => $seat,
                    'compartment' => $compartment,
                ];

                $normalizedAddOns[] = array_filter(
                    $row,
                    fn($value) => !(is_string($value) && trim($value) === '') && !(is_array($value) && count($value) === 0)
                );
            }

            $paxDetails[$idx]['addOns'] = array_values($normalizedAddOns);
        }

        return array_values($paxDetails);
    }

    protected function normalizePaxDetailsForAddonSeat(array $ctx, array $paxDetails): array
    {
        foreach ($paxDetails as $i => $pax) {
            $idNumber = trim((string) ($pax['IDNumber'] ?? ''));
            $title = trim((string) ($pax['title'] ?? 'MR'));
            $firstName = trim((string) ($pax['firstName'] ?? ''));
            $lastName = trim((string) ($pax['lastName'] ?? ''));
            $birthDate = trim((string) ($pax['birthDate'] ?? ''));
            $genderRaw = strtolower(trim((string) ($pax['gender'] ?? 'Male')));
            $docType = strtoupper(trim((string) ($pax['DocType'] ?? '')));
            $email = trim((string) ($pax['Email'] ?? ''));
            $typeString = $this->normalizePassengerTypeLabel($pax['type'] ?? 'Adult');

            $passportNumber = trim((string) ($pax['passportNumber'] ?? ''));
            $passportIssuedCountry = strtoupper(trim((string) ($pax['passportIssuedCountry'] ?? '')));
            $passportIssuedDate = trim((string) ($pax['passportIssuedDate'] ?? ''));
            $passportExpiredDate = trim((string) ($pax['passportExpiredDate'] ?? ''));

            $row = [
                'IDNumber' => $idNumber !== '' ? $idNumber : null,
                'title' => $title !== '' ? $title : 'MR',
                'firstName' => $firstName,
                'lastName' => $lastName,
                'birthDate' => $birthDate,
                'gender' => $genderRaw === 'female' ? 'Female' : 'Male',
                'nationality' => strtoupper(trim((string) ($pax['nationality'] ?? 'ID'))),
                'birthCountry' => strtoupper(trim((string) ($pax['birthCountry'] ?? 'ID'))),
                'DocType' => $docType !== '' ? $docType : null,
                'type' => $typeString,
                'passportNumber' => $passportNumber !== '' ? $passportNumber : null,
                'passportIssuedCountry' => $passportIssuedCountry !== '' ? $passportIssuedCountry : null,
                'passportIssuedDate' => $passportIssuedDate !== '' ? $passportIssuedDate : null,
                'passportExpiredDate' => $passportExpiredDate !== '' ? $passportExpiredDate : null,
                'addOns' => array_values((array) ($pax['addOns'] ?? [])),
            ];

            if ($email !== '') {
                $row['Email'] = $email;
            }

            if ($typeString === 'Infant') {
                $parent = trim((string) ($pax['parent'] ?? ''));
                $row['parent'] = $parent !== '' ? $parent : null;
            }

            $batikMilesNo = trim((string) ($pax['batikMilesNo'] ?? ''));
            if ($batikMilesNo !== '') {
                $row['batikMilesNo'] = $batikMilesNo;
            }

            $garudaFrequentFlyer = trim((string) ($pax['garudaFrequentFlyer'] ?? ''));
            if ($garudaFrequentFlyer !== '') {
                $row['garudaFrequentFlyer'] = $garudaFrequentFlyer;
            }

            $ssr = trim((string) ($pax['SSR'] ?? ''));
            if ($ssr !== '') {
                $row['SSR'] = $ssr;
            }

            $paxDetails[$i] = $row;
        }

        return array_values($paxDetails);
    }

    protected function injectDefaultSegmentAddOns(array $ctx, array $paxDetails): array
    {
        return array_values($paxDetails);
    }

    protected function assertBookingBusinessRules(array $ctx, array $paxDetails): void
    {
        if ($this->journeyRequiresPassport($ctx)) {
            foreach ($paxDetails as $index => $pax) {
                $missing = [];

                foreach (
                    [
                        'passportNumber',
                        'passportIssuedCountry',
                        'passportIssuedDate',
                        'passportExpiredDate',
                    ] as $field
                ) {
                    if (trim((string) ($pax[$field] ?? '')) === '') {
                        $missing[] = $field;
                    }
                }

                if ($missing !== []) {
                    abort(
                        422,
                        'Passenger #' . ($index + 1) . ' wajib isi passport untuk rute internasional. Missing: ' . implode(', ', $missing)
                    );
                }
            }
        }
    }

    protected function normalizePassengerTypeLabel(mixed $type): string
    {
        if (is_int($type) || ctype_digit((string) $type)) {
            return match ((int) $type) {
                1 => 'Child',
                2 => 'Infant',
                default => 'Adult',
            };
        }

        $raw = strtolower(trim((string) $type));

        return match ($raw) {
            '1', 'child' => 'Child',
            '2', 'infant' => 'Infant',
            default => 'Adult',
        };
    }

    protected function resolveAndReorderInfantParents(array $ctx, array $paxDetails): array
    {
        $airlineId = strtoupper(trim((string) ($ctx['airlineID'] ?? data_get($ctx, 'search.airlineID', ''))));

        $adults = [];
        $children = [];
        $infants = [];

        foreach ($paxDetails as $idx => $pax) {
            $type = $this->normalizePassengerTypeLabel($pax['type'] ?? 'Adult');

            $pax['_resolvedType'] = $type;
            $pax['_originalIndex'] = $idx;

            if ($type === 'Adult') {
                $adults[] = $pax;
            } elseif ($type === 'Child') {
                $children[] = $pax;
            } else {
                $infants[] = $pax;
            }
        }

        $adultMap = [];
        $validAdultSequences = array_values(array_unique(array_values($adultMap)));
        foreach ($adults as $adultIndex => $adult) {
            $firstName = trim((string) ($adult['firstName'] ?? ''));
            $lastName = trim((string) ($adult['lastName'] ?? ''));

            $sequence = in_array($airlineId, ['QG', 'QZ'], true)
                ? $adultIndex
                : ($adultIndex + 1);

            if ($firstName !== '') {
                $adultMap[strtolower($firstName)] = $sequence;
            }

            $fullName = strtolower(trim($firstName . ' ' . $lastName));
            if ($fullName !== '') {
                $adultMap[$fullName] = $sequence;
            }
        }

        foreach ($infants as $idx => &$infant) {
            $label = 'Passenger #' . (($infant['_originalIndex'] ?? $idx) + 1);
            $rawParent = trim((string) ($infant['parent'] ?? ''));

            if ($rawParent === '') {
                abort(422, $label . ' bertipe Infant wajib punya parent.');
            }

            if (ctype_digit($rawParent)) {
                $sequence = (int) $rawParent;

                if (!in_array($sequence, $validAdultSequences, true)) {
                    abort(422, $label . ' parent infant numeric tidak cocok dengan urutan penumpang dewasa.');
                }

                $infant['parent'] = (string) $sequence;
                continue;
            }

            $lookupKey = strtolower($rawParent);

            if (!array_key_exists($lookupKey, $adultMap)) {
                abort(422, $label . ' parent infant tidak cocok dengan penumpang dewasa.');
            }

            // DOCS: parent = sequence adult passenger, bukan nama
            $infant['parent'] = (string) $adultMap[$lookupKey];
        }
        unset($infant);

        // urutan final tetap: Adult -> Child -> Infant
        $ordered = array_merge($adults, $children, $infants);

        foreach ($ordered as &$row) {
            unset($row['_resolvedType'], $row['_originalIndex']);
        }
        unset($row);

        return array_values($ordered);
    }

    protected function normalizePaxDetailsForBooking(array $ctx, array $paxDetails): array
    {
        $isInternational = $this->isInternationalRoute($ctx);

        foreach ($paxDetails as $i => $pax) {
            $docType = strtoupper(trim((string) ($pax['DocType'] ?? '')));
            if ($docType === 'PASSPORT') {
                $docType = 'PASPORT';
            }
            $typeString = $this->normalizePassengerTypeLabel($pax['type'] ?? 'Adult');

            $row = [
                'IDNumber'     => trim((string) ($pax['IDNumber'] ?? '')),
                'title'        => trim((string) ($pax['title'] ?? 'MR')),
                'firstName'    => trim((string) ($pax['firstName'] ?? '')),
                'lastName'     => trim((string) ($pax['lastName'] ?? '')),
                'birthDate'    => trim((string) ($pax['birthDate'] ?? '')),
                'gender'       => trim((string) ($pax['gender'] ?? 'Male')),
                'nationality'  => strtoupper(trim((string) ($pax['nationality'] ?? 'ID'))),
                'birthCountry' => strtoupper(trim((string) ($pax['birthCountry'] ?? 'ID'))),
                'DocType'      => $docType !== '' ? $docType : null,
                'Email'        => trim((string) ($pax['Email'] ?? '')),
                'type'         => $typeString, // samakan dengan payload add-ons/seat yang lolos
                'addOns'       => array_values((array) ($pax['addOns'] ?? [])),
            ];

            if ($typeString === 'Infant') {
                $row['parent'] = (isset($pax['parent']) && $pax['parent'] !== '' && $pax['parent'] !== null)
                    ? trim((string) $pax['parent'])
                    : null;
            }

            if ($typeString === 'Infant') {
                $row['parent'] = (isset($pax['parent']) && $pax['parent'] !== '' && $pax['parent'] !== null)
                    ? trim((string) $pax['parent'])
                    : null;

                if (trim((string) ($row['Email'] ?? '')) === '') {
                    unset($row['Email']);
                }
            }

            $batikMilesNo = trim((string) ($pax['batikMilesNo'] ?? ''));
            if ($batikMilesNo !== '') {
                $row['batikMilesNo'] = $batikMilesNo;
            }

            $garudaFrequentFlyer = trim((string) ($pax['garudaFrequentFlyer'] ?? ''));
            if ($garudaFrequentFlyer !== '') {
                $row['garudaFrequentFlyer'] = $garudaFrequentFlyer;
            }

            $ssr = trim((string) ($pax['SSR'] ?? ''));
            if ($ssr !== '') {
                $row['SSR'] = $ssr;
            }

            if (!array_key_exists('Email', $row) || trim((string) ($row['Email'] ?? '')) === '') {
                unset($row['Email']);
            }

            if ($isInternational) {
                $row['passportNumber']        = trim((string) ($pax['passportNumber'] ?? ''));
                $row['passportIssuedCountry'] = strtoupper(trim((string) ($pax['passportIssuedCountry'] ?? '')));
                $row['passportIssuedDate']    = trim((string) ($pax['passportIssuedDate'] ?? ''));
                $row['passportExpiredDate']   = trim((string) ($pax['passportExpiredDate'] ?? ''));
            } else {
                // samakan dengan add-ons/seat: null, bukan empty string / zero date
                $row['passportNumber']        = null;
                $row['passportIssuedCountry'] = null;
                $row['passportIssuedDate']    = null;
                $row['passportExpiredDate']   = null;
            }

            $paxDetails[$i] = $row;
        }

        return array_values($paxDetails);
    }

    protected function assertBookingPaxRules(array $ctx, array $paxDetails): void
    {
        $isInternational = $this->isInternationalRoute($ctx);

        foreach ($paxDetails as $index => $pax) {
            $label = 'Passenger #' . ($index + 1);
            $docType = strtoupper(trim((string) ($pax['DocType'] ?? '')));
            $idNumber = trim((string) ($pax['IDNumber'] ?? ''));
            $type = $this->normalizePassengerTypeLabel($pax['type'] ?? 'Adult');

            if ($docType === '') {
                abort(422, $label . ' wajib isi DocType sesuai dokumentasi supplier.');
            }

            if (!in_array($docType, ['KTP', 'PASPORT', 'PASSPORT'], true)) {
                abort(422, $label . ' DocType tidak valid. Gunakan KTP atau Pasport/Passport sesuai dokumentasi supplier.');
            }

            if ($docType === 'KTP' && !$isInternational) {
                if (!preg_match('/^[0-9]{16}$/', $idNumber)) {
                    abort(422, $label . ' untuk DocType KTP wajib memakai NIK 16 digit.');
                }
            }

            if ($type === 'Infant') {
                $parent = trim((string) ($pax['parent'] ?? ''));
                if ($parent === '') {
                    abort(422, $label . ' bertipe Infant wajib punya parent.');
                }
            }

            if ($isInternational) {
                foreach (['passportNumber', 'passportIssuedCountry', 'passportIssuedDate', 'passportExpiredDate'] as $field) {
                    if (trim((string) ($pax[$field] ?? '')) === '') {
                        abort(422, $label . ' wajib isi ' . $field . ' untuk rute internasional.');
                    }
                }
            }
        }
    }

    protected function isInternationalRoute(array $ctx): bool
    {
        $origin = strtoupper(trim((string) data_get($ctx, 'search.origin', '')));
        $destination = strtoupper(trim((string) data_get($ctx, 'search.destination', '')));

        $domesticCodes = ['CGK', 'SUB', 'DPS', 'UPG', 'BDO', 'JOG', 'YIA', 'SRG', 'SOC', 'LOP', 'BPN', 'PKU', 'PLM', 'PDG', 'BTJ', 'KNO', 'HLP'];

        return !(in_array($origin, $domesticCodes, true) && in_array($destination, $domesticCodes, true));
    }

    protected function journeyRequiresPassport(array $ctx): bool
    {
        foreach (
            [
                (array) ($ctx['journey'] ?? []),
                (array) ($ctx['journey_return'] ?? []),
            ] as $journey
        ) {
            foreach ((array) data_get($journey, 'segment', []) as $segment) {
                foreach ((array) data_get($segment, 'flightDetail', []) as $flightDetail) {
                    if ((bool) ($flightDetail['passportRequired'] ?? false) === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function addonRequiresMandatoryBaggage(array $ctx): bool
    {
        return $this->containsFlagValue((array) ($ctx['addons_response'] ?? []), 'isEnableNoBaggage', false);
    }

    protected function containsFlagValue(array $data, string $key, $expected): bool
    {
        foreach ($data as $k => $value) {
            if ($k === $key && $value === $expected) {
                return true;
            }

            if (is_array($value) && $this->containsFlagValue($value, $key, $expected)) {
                return true;
            }
        }

        return false;
    }

    protected function hasAddonChoices(array $resp): bool
    {
        $rawAddons = $resp['data']['addOns'] ?? $resp['addOns'] ?? [];
        foreach ((array) $rawAddons as $row) {
            if (!empty((array) ($row['baggageInfos'] ?? [])) || !empty((array) ($row['mealInfos'] ?? []))) {
                return true;
            }
        }

        return false;
    }

    protected function hasSeatChoices(array $resp): bool
    {
        foreach ((array) data_get($resp, 'seatAddOns', []) as $row) {
            if (!empty((array) ($row['infos'] ?? []))) {
                return true;
            }
        }

        return false;
    }

    protected function extractScheduleCodes(array $journey, array $journeyReturn = []): array
    {
        $firstDepartFd = (array) data_get($journey, 'segment.0.flightDetail.0', []);
        $firstReturnFd = (array) data_get($journeyReturn, 'segment.0.flightDetail.0', []);

        $departJourneyReference = (string) data_get($journey, 'journeyReference', '');
        $returnJourneyReference = (string) data_get($journeyReturn, 'journeyReference', '');

        $codes = [
            'schDepart' => (string) (
                $departJourneyReference
                ?: ($firstDepartFd['schDepart'] ?? null)
                ?: ($firstDepartFd['scheduleCode'] ?? null)
                ?: ($firstDepartFd['flightNumber'] ?? '')
            ),
            'schReturn' => (string) (
                $returnJourneyReference
                ?: ($firstReturnFd['schDepart'] ?? null)
                ?: ($firstReturnFd['scheduleCode'] ?? null)
                ?: ($firstReturnFd['flightNumber'] ?? '')
            ),
            'departureAirlineSegmentCode' => $this->resolveAirlineSegmentCodeFromJourney($journey, $departJourneyReference),
            'departureFareBasisCode' => $this->resolveFareBasisCodeFromJourney($journey, $departJourneyReference),
            'returnAirlineSegmentCode' => $this->resolveAirlineSegmentCodeFromJourney($journeyReturn, $returnJourneyReference),
            'returnFareBasisCode' => $this->resolveFareBasisCodeFromJourney($journeyReturn, $returnJourneyReference),
        ];

        if ($codes['schDepart'] === '') {
            abort(422, 'Journey reference tidak tersedia. Silakan refresh halaman atau cari ulang penerbangan.');
        }

        return $codes;
    }

    protected function selectedScheduleModels(array $segments, string $journeyReference = ''): array
    {
        $out = [];

        foreach ($segments as $segment) {
            $flightDetails = array_values((array) data_get($segment, 'flightDetail', []));
            $availableDetails = array_values((array) data_get($segment, 'availableDetail', []));

            if ($flightDetails === []) {
                continue;
            }

            $matchedAvail = $this->matchJourneyAvailableDetail($availableDetails, $journeyReference);

            foreach ($flightDetails as $fdRaw) {
                $fd = (array) $fdRaw;

                if ($fd === []) {
                    continue;
                }

                $out[] = [
                    'airlineCode' => (string) ($fd['airlineCode'] ?? ''),
                    'flightNumber' => (string) ($fd['flightNumber'] ?? ''),
                    'schOrigin' => (string) (
                        $fd['schOrigin']
                        ?? $fd['fdOrigin']
                        ?? ''
                    ),
                    'schDestination' => (string) (
                        $fd['schDestination']
                        ?? $fd['fdDestination']
                        ?? ''
                    ),
                    'detailSchedule' => (string) (
                        ($fd['detailSchedule'] ?? null)
                        ?: $journeyReference
                        ?: ($fd['routeInfo'] ?? '')
                    ),
                    'schDepartTime' => (string) (
                        $fd['schDepartTime']
                        ?? $fd['fdDepartTime']
                        ?? ''
                    ),
                    'schArrivalTime' => (string) (
                        $fd['schArrivalTime']
                        ?? $fd['fdArrivalTime']
                        ?? ''
                    ),
                    'flightClass' => (string) (
                        $matchedAvail['flightClass']
                        ?? $matchedAvail['classID']
                        ?? $matchedAvail['classId']
                        ?? $matchedAvail['classiId']
                        ?? $fd['flightClass']
                        ?? ''
                    ),
                    'garudaNumber' => (string) (
                        $fd['garudaNumber']
                        ?? $matchedAvail['garudaNumber']
                        ?? ''
                    ),
                    'garudaAvailability' => (string) (
                        $fd['garudaAvailability']
                        ?? $matchedAvail['garudaAvailability']
                        ?? ''
                    ),
                ];
            }
        }

        return array_values($out);
    }

    protected function matchJourneyAvailableDetail(array $availableDetails, string $journeyReference = ''): array
    {
        foreach ($availableDetails as $availRaw) {
            $avail = (array) $availRaw;

            if (
                $journeyReference !== '' &&
                (string) ($avail['subClass'] ?? '') === $journeyReference
            ) {
                return $avail;
            }
        }

        return (array) ($availableDetails[0] ?? []);
    }

    protected function resolveFareBasisCodeFromJourney(array $journey, string $journeyReference = ''): string
    {
        $availableDetails = array_values((array) data_get($journey, 'segment.0.availableDetail', []));
        $matchedAvail = $this->matchJourneyAvailableDetail($availableDetails, $journeyReference);

        return (string) (
            $matchedAvail['classID']
            ?? $matchedAvail['classId']
            ?? $matchedAvail['classiId']
            ?? $matchedAvail['fareBasisCode']
            ?? $matchedAvail['flightClass']
            ?? ''
        );
    }

    protected function resolveAirlineSegmentCodeFromJourney(array $journey, string $journeyReference = ''): string
    {
        $availableDetails = array_values((array) data_get($journey, 'segment.0.availableDetail', []));
        $matchedAvail = $this->matchJourneyAvailableDetail($availableDetails, $journeyReference);

        return (string) (
            $matchedAvail['airlineSegmentCode']
            ?? data_get($journey, 'segment.0.flightDetail.0.airlineSegmentCode')
            ?? data_get($journey, 'segment.0.flightDetail.0.segmentCode')
            ?? ''
        );
    }
}
