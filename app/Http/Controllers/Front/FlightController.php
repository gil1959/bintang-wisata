<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Darmawisata\DarmawisataClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Services\FlightPriceOverrideService;

class FlightController extends Controller
{
    public function index(Request $request, DarmawisataClient $dw, FlightPriceOverrideService $flightPricing)
    {
        Log::info('FLIGHT_RUNTIME_MARKER_V4', [
            'full_url' => request()->fullUrl(),
            'origin' => request('origin'),
            'destination' => request('destination'),
            'departDate' => request('departDate'),
        ]);
        $isEn = app()->getLocale() === 'en';
        Log::info('FLIGHT_REQUEST_DEBUG', [
            'method' => $request->method(),
            'full_url' => $request->fullUrl(),
            'query' => $request->query(),
            'all' => $request->all(),
            'origin' => $request->get('origin'),
            'destination' => $request->get('destination'),
            'departDate' => $request->get('departDate'),
            'hasSearchFilled' => $request->filled('origin')
                && $request->filled('destination')
                && $request->filled('departDate'),
        ]);
        $search = [
            'tripType'  => $request->get('tripType', 'OneWay'),
            'origin'    => strtoupper(trim((string) $request->get('origin', ''))),
            'destination' => strtoupper(trim((string) $request->get('destination', ''))),
            'departDate'  => (string) $request->get('departDate', ''),
            'returnDate'  => (string) $request->get('returnDate', ''),
            'paxAdult'    => (int) $request->get('paxAdult', 1),
            'paxChild'    => (int) $request->get('paxChild', 0),
            'paxInfant'   => (int) $request->get('paxInfant', 0),
        ];

        $cities = [];
        $results = [];
        $error = null;

        try {
            $citiesRes = $dw->airlineCities();
            $cities = (array) ($citiesRes['cities'] ?? []);
        } catch (\Throwable $e) {
            Log::error('FLIGHT_SEARCH_EXCEPTION', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $error = 'FLIGHT ERROR: ' . $e->getMessage();
        }

        $hasSearch = $request->filled('origin') && $request->filled('destination') && $request->filled('departDate');
        Log::info('FLIGHT_HAS_SEARCH_CHECK', [
            'hasSearch' => $hasSearch,
            'origin' => $request->get('origin'),
            'destination' => $request->get('destination'),
            'departDate' => $request->get('departDate'),
        ]);
        if ($hasSearch) {
            Log::info('FLIGHT_SEARCH_BRANCH_ENTERED', [
                'search' => $search,
            ]);
            $request->validate([
                'tripType'    => 'nullable|in:OneWay,RoundTrip',
                'origin'      => 'required|string|max:10',
                'destination' => 'required|string|max:10|different:origin',
                'departDate'  => 'required|date|after_or_equal:today',
                'returnDate'  => 'nullable|date|after_or_equal:departDate',
                'paxAdult'    => 'required|integer|min:1|max:9',
                'paxChild'    => 'nullable|integer|min:0|max:9',
                'paxInfant'   => 'nullable|integer|min:0|max:9',
            ]);

            if (($search['tripType'] ?? 'OneWay') === 'RoundTrip' && empty($search['returnDate'])) {
                $error = $isEn ? 'Return date is required for Round Trip.' : 'Tanggal pulang wajib diisi untuk Round Trip.';
            } else {
                try {
                    Log::info('Flight search fired', [
                        'search' => $search,
                    ]);
                    $resp = $dw->scheduleAllAirline($search);

                    if (
                        strtoupper((string)($resp['status'] ?? '')) !== 'SUCCESS'
                        && count((array)($resp['journeyDepart'] ?? [])) === 0
                    ) {
                        $error = (string)($resp['respMessage'] ?? ($isEn ? 'Failed to fetch schedule.' : 'Gagal mengambil jadwal.'));
                    } else {
                        $journeysDepart = array_values((array) ($resp['journeyDepart'] ?? []));
                        $journeysReturn = array_values((array) ($resp['journeyReturn'] ?? []));
                        $tripType = (string) ($search['tripType'] ?? 'OneWay');

                        if ($tripType === 'RoundTrip') {
                            foreach ($journeysDepart as $journeyDepart) {
                                $departAirlineId = (string) data_get($journeyDepart, 'airlineID', '');

                                foreach ($journeysReturn as $journeyReturn) {
                                    $returnAirlineId = (string) data_get($journeyReturn, 'airlineID', '');

                                    // PriceAllAirline butuh airlineID tunggal.
                                    // Jadi pair hanya sesama airline.
                                    if ($departAirlineId === '' || $returnAirlineId === '' || $departAirlineId !== $returnAirlineId) {
                                        continue;
                                    }

                                    $key = (string) Str::uuid();

                                    Cache::put(
                                        "flight_quote:{$key}",
                                        [
                                            'search' => $search,
                                            'journey' => $journeyDepart,
                                            'journey_return' => $journeyReturn,
                                        ],
                                        now()->addMinutes(30)
                                    );

                                    $results[] = [
                                        'key' => $key,
                                        'journey' => $journeyDepart,
                                        'journey_return' => $journeyReturn,
                                    ];
                                }
                            }
                        } else {
                            foreach ($journeysDepart as $journey) {
                                $key = (string) Str::uuid();

                                Cache::put(
                                    "flight_quote:{$key}",
                                    [
                                        'search' => $search,
                                        'journey' => $journey,
                                        'journey_return' => [],
                                    ],
                                    now()->addMinutes(30)
                                );

                                $results[] = [
                                    'key' => $key,
                                    'journey' => $journey,
                                    'journey_return' => [],
                                ];
                            }
                        }

                        if (count($results) === 0) {
                            $error = $isEn ? 'No flights found for your search.' : 'Tidak ada penerbangan untuk pencarian ini.';
                        }
                    }
                } catch (\Throwable $e) {
                    $error = ($isEn ? 'Failed to fetch schedule.' : 'Gagal mengambil jadwal.')
                        . ' ' . $e->getMessage();
                }
            }
        }

        foreach ($results as $idx => $item) {
            $journeyItem = (array) ($item['journey'] ?? []);
            $journeyReturnItem = (array) ($item['journey_return'] ?? []);

            $results[$idx]['pricing'] = $flightPricing->resolve(
                $search,
                $journeyItem,
                $journeyReturnItem,
                (float) data_get($journeyItem, 'sumPrice', 0),
                (string) data_get($journeyItem, 'currency', 'IDR')
            );
        }

        return view('front.flights.index', [
            'cities' => $cities,
            'search' => $search,
            'results' => $results,
            'error' => $error,
        ]);
    }

    public function show(string $key, DarmawisataClient $dw, FlightPriceOverrideService $flightPricing)
    {
        $payload = Cache::get("flight_quote:{$key}");

        if (!$payload || !is_array($payload)) {
            return redirect()
                ->route('flights.index')
                ->with('error', 'Data penerbangan sudah kadaluarsa. Silakan cari ulang.');
        }

        $search = (array) ($payload['search'] ?? []);
        $journey = (array) ($payload['journey'] ?? []);
        $journeyReturn = (array) ($payload['journey_return'] ?? []);

        $originalJourney = $journey;
        $originalJourneyReturn = $journeyReturn;

        $price = [];
        $priceError = null;


        try {
            $tripType = (string) ($search['tripType'] ?? 'OneWay');

            $airlineID = (string) data_get($journey, 'airlineID', '');
            $journeyDepartReference = (string) data_get($journey, 'journeyReference', '');
            $journeyReturnReference = $tripType === 'RoundTrip'
                ? (string) data_get($journeyReturn, 'journeyReference', '')
                : '';

            if ($airlineID === '' || $journeyDepartReference === '') {
                throw new \RuntimeException('Data flight belum lengkap untuk proses reservation.');
            }

            if ($tripType === 'RoundTrip') {
                $returnAirlineId = (string) data_get($journeyReturn, 'airlineID', '');

                if ($journeyReturnReference === '') {
                    throw new \RuntimeException('Journey return reference kosong untuk RoundTrip.');
                }

                if ($returnAirlineId === '' || $returnAirlineId !== $airlineID) {
                    throw new \RuntimeException('RoundTrip wajib menggunakan pasangan departure/return dari airline yang sama.');
                }
            }

            /**
             * IMPORTANT:
             * ScheduleAllAirline hanya dipakai untuk listing.
             * Reservation flow yang valid harus ambil ulang jadwal final via Airline/Schedule.
             */
            $scheduleResp = $dw->scheduleByAirline($search, $airlineID);

            if (
                strtoupper((string) ($scheduleResp['status'] ?? '')) !== 'SUCCESS' &&
                count((array) ($scheduleResp['journeyDepart'] ?? [])) === 0
            ) {
                throw new \RuntimeException(
                    (string) ($scheduleResp['respMessage'] ?? 'Airline/Schedule gagal untuk airline terpilih.')
                );
            }

            $matchedJourney = $this->findMatchingJourneyFromAirlineSchedule(
                $journey,
                (array) ($scheduleResp['journeyDepart'] ?? [])
            );

            if ($matchedJourney === []) {
                throw new \RuntimeException('Tidak menemukan journey departure yang match dari Airline/Schedule.');
            }

            $matchedJourney = $this->mergeJourneyDisplayFallback($matchedJourney, $originalJourney);

            $matchedJourneyReturn = [];
            if ($tripType === 'RoundTrip') {
                $matchedJourneyReturn = $this->findMatchingJourneyFromAirlineSchedule(
                    $journeyReturn,
                    (array) ($scheduleResp['journeyReturn'] ?? [])
                );

                if ($matchedJourneyReturn === []) {
                    throw new \RuntimeException('Tidak menemukan journey return yang match dari Airline/Schedule.');
                }

                $matchedJourneyReturn = $this->mergeJourneyDisplayFallback($matchedJourneyReturn, $originalJourneyReturn);
            }


            $selectedSchDeparts = $this->buildSelectedScheduleModels(
                (array) data_get($matchedJourney, 'segment', []),
                (string) data_get($matchedJourney, 'journeyReference', '')
            );
            $selectedSchReturns = $this->buildSelectedScheduleModels(
                (array) data_get($matchedJourneyReturn, 'segment', []),
                (string) data_get($matchedJourneyReturn, 'journeyReference', '')
            );

            if (count($selectedSchDeparts) === 0) {
                throw new \RuntimeException('Selected schedule departure tidak valid untuk proses price.');
            }

            $priceParams = [
                'airlineID' => $airlineID,
                'origin' => strtoupper(trim((string) ($search['origin'] ?? data_get($matchedJourney, 'jiOrigin', '')))),
                'destination' => strtoupper(trim((string) ($search['destination'] ?? data_get($matchedJourney, 'jiDestination', '')))),
                'tripType' => $tripType,
                'departDate' => (string) ($search['departDate'] ?? ''),
                'returnDate' => $tripType === 'RoundTrip' ? (string) ($search['returnDate'] ?? '') : '',
                'paxAdult' => (int) ($search['paxAdult'] ?? 1),
                'paxChild' => (int) ($search['paxChild'] ?? 0),
                'paxInfant' => (int) ($search['paxInfant'] ?? 0),
                'searchKey' => (string) data_get($scheduleResp, 'searchKey', data_get($matchedJourney, 'searchKey', '')),
                'promoCode' => (string) ($search['promoCode'] ?? ''),
                'schDeparts' => $selectedSchDeparts,
                'schReturns' => $tripType === 'RoundTrip' ? $selectedSchReturns : [],
            ];

            $priceAllAirline = [];
            $price = [];

            try {
                $priceAllAirline = $dw->priceAllAirline([
                    'airlineID' => $airlineID,
                    'origin' => strtoupper(trim((string) ($search['origin'] ?? data_get($matchedJourney, 'jiOrigin', '')))),
                    'destination' => strtoupper(trim((string) ($search['destination'] ?? data_get($matchedJourney, 'jiDestination', '')))),
                    'tripType' => $tripType,
                    'departDate' => (string) ($search['departDate'] ?? ''),
                    'returnDate' => $tripType === 'RoundTrip' ? (string) ($search['returnDate'] ?? '') : '',
                    'paxAdult' => (int) ($search['paxAdult'] ?? 1),
                    'paxChild' => (int) ($search['paxChild'] ?? 0),
                    'paxInfant' => (int) ($search['paxInfant'] ?? 0),
                    'airlineAccessCode' => (string) data_get($scheduleResp, 'airlineAccessCode', ''),
                    'journeyDepartReference' => (string) data_get($originalJourney, 'journeyReference', ''),
                    'journeyReturnReference' => $tripType === 'RoundTrip'
                        ? (string) data_get($originalJourneyReturn, 'journeyReference', '')
                        : '',
                ]);
            } catch (\Throwable $e) {
                Log::warning('Flight detail PriceAllAirline failed', [
                    'key' => $key,
                    'error' => $e->getMessage(),
                    'airlineID' => $airlineID,
                ]);
            }

            /**
             * IMPORTANT:
             * PriceAllAirline hanya dipakai untuk display/listing.
             * Reservation flow tetap wajib pakai Airline/Price sesuai docs supplier.
             */

            Log::info('Airline/Price prepared payload', [
                'key' => $key,
                'airlineID' => $airlineID,
                'searchKey' => $priceParams['searchKey'] ?? '',
                'schDeparts' => $priceParams['schDeparts'] ?? [],
                'schReturns' => $priceParams['schReturns'] ?? [],
            ]);
            $price = $dw->priceAirline($priceParams);

            $priceStatus = strtoupper((string) data_get($price, 'status', ''));
            $priceAllStatus = strtoupper((string) data_get($priceAllAirline, 'status', ''));

            // FIX: Jika Airline/Price FAILED tapi PriceAllAirline SUCCESS,
            // gunakan PriceAllAirline sebagai reservationPrice agar booking flow bisa lanjut
            $reservationPrice = $priceStatus === 'SUCCESS'
                ? $price
                : ($priceAllStatus === 'SUCCESS' ? $priceAllAirline : $price);

            $displayPrice = $priceStatus === 'SUCCESS'
                ? $price
                : ($priceAllStatus === 'SUCCESS' ? $priceAllAirline : $price);

            $reservationPriceSuccess = $priceStatus === 'SUCCESS' || $priceAllStatus === 'SUCCESS';

            if (!$reservationPriceSuccess) {
                throw new \RuntimeException(
                    'Airline/Price gagal: ' . (string) data_get($price, 'respMessage', 'request failed')
                );
            }

            Log::info('Flight detail reservation price result', [
                'key' => $key,
                'airlineID' => $airlineID,
                'priceAllAirlineStatus' => $priceAllStatus,
                'priceStatus' => $priceStatus,
                'displayPriceStatus' => strtoupper((string) data_get($displayPrice, 'status', '')),
                'reservationPriceStatus' => strtoupper((string) data_get($reservationPrice, 'status', '')),
                'priceRespMessage' => (string) data_get($reservationPrice, 'respMessage', ''),
            ]);

            Cache::put("flight_quote_detail:{$key}", [
                'search' => $search,
                'journey' => $matchedJourney,
                'journey_return' => $matchedJourneyReturn,
                'price' => $reservationPrice,
                'display_price' => $displayPrice,
                'reservation_price' => $reservationPrice,
                'price_all_airline' => $priceAllAirline,
                'priced_at' => Carbon::now()->toIso8601String(),
            ], now()->addMinutes(30));

            Cache::put("flight_booking_ctx:{$key}", [
                'search' => $search,
                'airlineID' => $airlineID,
                'journey' => $matchedJourney,
                'journey_return' => $matchedJourneyReturn,
                'original_journey' => $originalJourney,
                'original_journey_return' => $originalJourneyReturn,
                'schedule_response' => $scheduleResp,
                'price' => $reservationPrice,
                'display_price' => $displayPrice,
                'reservation_price' => $reservationPrice,
                'price_all_airline' => $priceAllAirline,
                'price_status' => $priceStatus,
                'display_price_status' => strtoupper((string) data_get($displayPrice, 'status', '')),
                'reservation_price_status' => strtoupper((string) data_get($reservationPrice, 'status', '')),
                'price_all_airline_status' => $priceAllStatus,
                'price_success' => $reservationPriceSuccess,
                'selected_schedules' => [
                    'schDeparts' => $selectedSchDeparts,
                    'schReturns' => $tripType === 'RoundTrip' ? $selectedSchReturns : [],
                ],
                'schedule_codes' => $this->buildScheduleCodes(
                    (array) $matchedJourney,
                    (array) $matchedJourneyReturn,
                    $selectedSchDeparts,
                    $tripType === 'RoundTrip' ? $selectedSchReturns : [],
                    (array) $price,
                    (array) $priceAllAirline
                ),
            ], now()->addMinutes(30));

            $journey = $matchedJourney;
            $journeyReturn = $matchedJourneyReturn;

            if (!$reservationPriceSuccess) {
                $priceError = (string) ($reservationPrice['respMessage'] ?? 'Gagal mengambil harga reservasi supplier.');
            }
        } catch (\Throwable $e) {
            Log::warning('Flight detail repricing failed', [
                'key' => $key,
                'error' => $e->getMessage(),
                'journey' => $journey,
                'search' => $search,
            ]);

            $priceError = 'Gagal mengambil harga terbaru. ' . $e->getMessage();
        }

        $cities = [];

        try {
            $citiesRes = $dw->airlineCities();
            $cities = (array) ($citiesRes['cities'] ?? []);
        } catch (\Throwable $e) {
            $cities = [];
        }

        $displaySupplierPrice = is_numeric(data_get($displayPrice ?? [], 'sumFare'))
            ? (float) data_get($displayPrice ?? [], 'sumFare')
            : (float) data_get($journey, 'sumPrice', 0);

        $displayCurrency = (string) data_get($displayPrice ?? [], 'currency', data_get($journey, 'currency', 'IDR'));

        $pricingData = $flightPricing->resolve(
            $search,
            $journey,
            $journeyReturn,
            $displaySupplierPrice,
            $displayCurrency
        );

        return view('front.flights.show', [
            'key' => $key,
            'search' => $search,
            'journey' => $journey,
            'journeyReturn' => $journeyReturn,
            'price' => $reservationPrice ?? $price,
            'displayPrice' => $displayPrice ?? $price,
            'priceError' => $priceError,
            'cities' => $cities,
            'pricingData' => $pricingData,
        ]);
    }

    public function booking(string $key, DarmawisataClient $dw, FlightPriceOverrideService $flightPricing)
    {
        $detail = Cache::get("flight_quote_detail:{$key}");
        $ctx = Cache::get("flight_booking_ctx:{$key}");

        if (
            !is_array($detail) ||
            !is_array($ctx) ||
            empty($detail['journey']) ||
            empty($detail['search'])
        ) {
            return redirect()
                ->route('flights.show', $key)
                ->with('error', 'Data booking penerbangan sudah kadaluarsa. Silakan ulang dari detail tiket.');
        }

        $cities = [];
        $countries = [];

        try {
            $citiesRes = $dw->airlineCities();
            $cities = (array) ($citiesRes['cities'] ?? []);
        } catch (\Throwable $e) {
            $cities = [];
        }

        try {
            $countryRes = $dw->airlineNationalities();
            $countries = array_values((array) ($countryRes['countries'] ?? []));
        } catch (\Throwable $e) {
            $countries = [];
        }

        $search = (array) ($detail['search'] ?? []);
        $journey = (array) ($detail['journey'] ?? []);
        $journeyReturn = (array) ($detail['journey_return'] ?? []);
        $displayPrice = (array) ($detail['display_price'] ?? []);
        $reservationPrice = (array) ($detail['price'] ?? []);

        $displaySupplierPrice = is_numeric(data_get($displayPrice, 'sumFare'))
            ? (float) data_get($displayPrice, 'sumFare')
            : (float) data_get($journey, 'sumPrice', 0);

        $displayCurrency = (string) data_get($displayPrice, 'currency', data_get($journey, 'currency', 'IDR'));

        $pricingData = $flightPricing->resolve(
            $search,
            $journey,
            $journeyReturn,
            $displaySupplierPrice,
            $displayCurrency
        );

        return view('front.flights.booking', [
            'key' => $key,
            'search' => $search,
            'journey' => $journey,
            'journeyReturn' => $journeyReturn,
            'price' => $reservationPrice,
            'displayPrice' => $displayPrice,
            'priceError' => '',
            'cities' => $cities,
            'countries' => $countries,
            'pricingData' => $pricingData,
        ]);
    }

    protected function mergeJourneyDisplayFallback(array $freshJourney, array $originalJourney): array
    {
        if (!is_numeric(data_get($freshJourney, 'sumPrice')) && is_numeric(data_get($originalJourney, 'sumPrice'))) {
            $freshJourney['sumPrice'] = data_get($originalJourney, 'sumPrice');
        }

        if ((string) data_get($freshJourney, 'currency', '') === '' && (string) data_get($originalJourney, 'currency', '') !== '') {
            $freshJourney['currency'] = data_get($originalJourney, 'currency');
        }

        if ((string) data_get($freshJourney, 'journeyReference', '') === '' && (string) data_get($originalJourney, 'journeyReference', '') !== '') {
            $freshJourney['journeyReference'] = data_get($originalJourney, 'journeyReference');
        }

        if ((string) data_get($freshJourney, 'airlineID', '') === '' && (string) data_get($originalJourney, 'airlineID', '') !== '') {
            $freshJourney['airlineID'] = data_get($originalJourney, 'airlineID');
        }

        if ((string) data_get($freshJourney, 'jiOrigin', '') === '' && (string) data_get($originalJourney, 'jiOrigin', '') !== '') {
            $freshJourney['jiOrigin'] = data_get($originalJourney, 'jiOrigin');
        }

        if ((string) data_get($freshJourney, 'jiDestination', '') === '' && (string) data_get($originalJourney, 'jiDestination', '') !== '') {
            $freshJourney['jiDestination'] = data_get($originalJourney, 'jiDestination');
        }

        return $freshJourney;
    }



    protected function buildSelectedScheduleModels(array $segments, string $journeyReference = ''): array
    {
        $out = [];

        foreach ($segments as $segment) {
            $flightDetails = array_values((array) data_get($segment, 'flightDetail', []));
            $availableDetails = array_values((array) data_get($segment, 'availableDetail', []));

            if ($flightDetails === []) {
                continue;
            }

            $matchedAvail = $this->matchJourneyAvailableDetail($availableDetails, $journeyReference);
            $isTransitJourney = count($segments) > 1 || count($flightDetails) > 1;

            foreach ($flightDetails as $fdRaw) {
                $fd = (array) $fdRaw;

                if ($fd === []) {
                    continue;
                }

                $detailSchedule = $this->firstFilledString(
                    $fd['detailSchedule'] ?? null,
                    $isTransitJourney ? $journeyReference : null,
                    $matchedAvail['subClass'] ?? null,
                    $journeyReference,
                    $fd['routeInfo'] ?? null,
                    $fd['flightNumber'] ?? null
                );

                $flightClass = $this->firstFilledString(
                    $matchedAvail['classID'] ?? null,
                    $matchedAvail['classId'] ?? null,
                    $matchedAvail['classiId'] ?? null,
                    $matchedAvail['fareBasisCode'] ?? null,
                    $matchedAvail['flightClass'] ?? null,
                    $fd['flightClass'] ?? null
                );

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
                    'detailSchedule' => $detailSchedule,
                    'schDepart' => $detailSchedule,
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
                    'flightClass' => $flightClass,
                    'classID' => (string) ($matchedAvail['classID'] ?? ''),
                    'classId' => (string) ($matchedAvail['classId'] ?? ''),
                    'classiId' => (string) ($matchedAvail['classiId'] ?? ''),
                    'fareBasisCode' => (string) (
                        $matchedAvail['fareBasisCode']
                        ?? $flightClass
                        ?? ''
                    ),
                    'subClass' => (string) ($matchedAvail['subClass'] ?? ''),
                    'airlineSegmentCode' => $this->firstFilledString(
                        $matchedAvail['airlineSegmentCode'] ?? null,
                        $fd['airlineSegmentCode'] ?? null
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

    protected function buildScheduleCodes(
        array $journey,
        array $journeyReturn,
        array $selectedSchDeparts,
        array $selectedSchReturns,
        array $price = [],
        array $priceAllAirline = []
    ): array {
        $departJourneyReference = $this->firstFilledString(
            data_get($journey, 'journeyReference'),
            data_get($selectedSchDeparts, '0.detailSchedule'),
            data_get($selectedSchDeparts, '0.schDepart'),
            data_get($journey, 'segment.0.flightDetail.0.detailSchedule'),
            data_get($journey, 'segment.0.flightDetail.0.routeInfo'),
            data_get($journey, 'segment.0.flightDetail.0.flightNumber')
        );

        $returnJourneyReference = $this->firstFilledString(
            data_get($journeyReturn, 'journeyReference'),
            data_get($selectedSchReturns, '0.detailSchedule'),
            data_get($selectedSchReturns, '0.schDepart'),
            data_get($journeyReturn, 'segment.0.flightDetail.0.detailSchedule'),
            data_get($journeyReturn, 'segment.0.flightDetail.0.routeInfo'),
            data_get($journeyReturn, 'segment.0.flightDetail.0.flightNumber')
        );

        return [
            'schDepart' => $departJourneyReference,
            'schReturn' => $returnJourneyReference,
            'departureAirlineSegmentCode' => $this->firstFilledString(
                data_get($price, 'priceDepart.0.airlineSegmentCode'),
                data_get($priceAllAirline, 'priceDepart.0.airlineSegmentCode'),
                data_get($selectedSchDeparts, '0.airlineSegmentCode'),
                $this->resolveAirlineSegmentCodeFromJourney($journey, $departJourneyReference)
            ),
            'departureFareBasisCode' => $this->firstFilledString(
                data_get($price, 'priceDepart.0.classID'),
                data_get($price, 'priceDepart.0.classId'),
                data_get($price, 'priceDepart.0.classiId'),
                data_get($priceAllAirline, 'priceDepart.0.classID'),
                data_get($priceAllAirline, 'priceDepart.0.classId'),
                data_get($priceAllAirline, 'priceDepart.0.classiId'),
                data_get($selectedSchDeparts, '0.classID'),
                data_get($selectedSchDeparts, '0.classId'),
                data_get($selectedSchDeparts, '0.classiId'),
                data_get($selectedSchDeparts, '0.fareBasisCode'),
                data_get($selectedSchDeparts, '0.flightClass'),
                $this->resolveFareBasisCodeFromJourney($journey, $departJourneyReference)
            ),
            'returnAirlineSegmentCode' => $this->firstFilledString(
                data_get($price, 'priceReturn.0.airlineSegmentCode'),
                data_get($priceAllAirline, 'priceReturn.0.airlineSegmentCode'),
                data_get($selectedSchReturns, '0.airlineSegmentCode'),
                $this->resolveAirlineSegmentCodeFromJourney($journeyReturn, $returnJourneyReference)
            ),
            'returnFareBasisCode' => $this->firstFilledString(
                data_get($price, 'priceReturn.0.classID'),
                data_get($price, 'priceReturn.0.classId'),
                data_get($price, 'priceReturn.0.classiId'),
                data_get($priceAllAirline, 'priceReturn.0.classID'),
                data_get($priceAllAirline, 'priceReturn.0.classId'),
                data_get($priceAllAirline, 'priceReturn.0.classiId'),
                data_get($selectedSchReturns, '0.classID'),
                data_get($selectedSchReturns, '0.classId'),
                data_get($selectedSchReturns, '0.classiId'),
                data_get($selectedSchReturns, '0.fareBasisCode'),
                data_get($selectedSchReturns, '0.flightClass'),
                $this->resolveFareBasisCodeFromJourney($journeyReturn, $returnJourneyReference)
            ),
        ];
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

        return $this->firstFilledString(
            $matchedAvail['airlineSegmentCode'] ?? null,
            data_get($journey, 'segment.0.flightDetail.0.airlineSegmentCode'),
            data_get($journey, 'segment.0.airlineSegmentCode')
        );
    }

    protected function findMatchingJourneyFromAirlineSchedule(array $selectedJourney, array $candidates): array
    {
        if ($selectedJourney === [] || $candidates === []) {
            return [];
        }

        $wantedFingerprint = $this->journeyFingerprint($selectedJourney);

        foreach ($candidates as $candidate) {
            $candidate = (array) $candidate;

            if ($this->journeyFingerprint($candidate) === $wantedFingerprint) {
                return $candidate;
            }
        }

        $selectedFirst = $this->firstFlightFingerprint($selectedJourney);
        $selectedLast = $this->lastFlightFingerprint($selectedJourney);
        $selectedSegmentCount = count((array) data_get($selectedJourney, 'segment', []));

        foreach ($candidates as $candidate) {
            $candidate = (array) $candidate;

            if (
                $this->firstFlightFingerprint($candidate) === $selectedFirst &&
                $this->lastFlightFingerprint($candidate) === $selectedLast &&
                count((array) data_get($candidate, 'segment', [])) === $selectedSegmentCount
            ) {
                return $candidate;
            }
        }

        return [];
    }

    protected function journeyFingerprint(array $journey): string
    {
        $parts = [];

        foreach ((array) data_get($journey, 'segment', []) as $segment) {
            foreach ((array) data_get($segment, 'flightDetail', []) as $fd) {
                $parts[] = implode('|', [
                    strtoupper(trim((string) data_get($fd, 'airlineCode', ''))),
                    trim((string) data_get($fd, 'flightNumber', '')),
                    strtoupper(trim((string) data_get($fd, 'fdOrigin', data_get($fd, 'schOrigin', '')))),
                    strtoupper(trim((string) data_get($fd, 'fdDestination', data_get($fd, 'schDestination', '')))),
                    trim((string) data_get($fd, 'fdDepartTime', data_get($fd, 'schDepartTime', ''))),
                    trim((string) data_get($fd, 'fdArrivalTime', data_get($fd, 'schArrivalTime', ''))),
                ]);
            }
        }

        return md5(json_encode($parts));
    }

    protected function firstFlightFingerprint(array $journey): string
    {
        $fd = (array) data_get($journey, 'segment.0.flightDetail.0', []);

        return implode('|', [
            strtoupper(trim((string) data_get($fd, 'airlineCode', ''))),
            trim((string) data_get($fd, 'flightNumber', '')),
            strtoupper(trim((string) data_get($fd, 'fdOrigin', data_get($fd, 'schOrigin', '')))),
            strtoupper(trim((string) data_get($fd, 'fdDestination', data_get($fd, 'schDestination', '')))),
            trim((string) data_get($fd, 'fdDepartTime', data_get($fd, 'schDepartTime', ''))),
        ]);
    }

    protected function lastFlightFingerprint(array $journey): string
    {
        $segments = array_values((array) data_get($journey, 'segment', []));
        $lastSegment = count($segments) > 0 ? (array) end($segments) : [];
        $flightDetails = array_values((array) data_get($lastSegment, 'flightDetail', []));
        $fd = count($flightDetails) > 0 ? (array) end($flightDetails) : [];

        return implode('|', [
            strtoupper(trim((string) data_get($fd, 'airlineCode', ''))),
            trim((string) data_get($fd, 'flightNumber', '')),
            strtoupper(trim((string) data_get($fd, 'fdOrigin', data_get($fd, 'schOrigin', '')))),
            strtoupper(trim((string) data_get($fd, 'fdDestination', data_get($fd, 'schDestination', '')))),
            trim((string) data_get($fd, 'fdArrivalTime', data_get($fd, 'schArrivalTime', ''))),
        ]);
    }
}
