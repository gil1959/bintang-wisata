<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightPriceOverride;
use App\Services\Darmawisata\DarmawisataClient;
use App\Services\FlightPriceOverrideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FlightPricingController extends Controller
{
    public function index(Request $request, DarmawisataClient $dw, FlightPriceOverrideService $pricingService)
    {
        $cities = $this->loadCities($dw);
        $cityLabelMap = $this->buildCityLabelMap($cities);

        $search = [
            'tripType' => (string) $request->get('tripType', 'OneWay'),
            'origin' => $this->normalizeAirportCode((string) $request->get('origin', '')),
            'destination' => $this->normalizeAirportCode((string) $request->get('destination', '')),
            'departDate' => (string) $request->get('departDate', ''),
            'returnDate' => (string) $request->get('returnDate', ''),
            'paxAdult' => (int) $request->get('paxAdult', 1),
            'paxChild' => (int) $request->get('paxChild', 0),
            'paxInfant' => (int) $request->get('paxInfant', 0),
        ];

        $results = [];
        $searchError = null;

        $hasSearch = filled($search['origin']) && filled($search['destination']) && filled($search['departDate']);

        if ($hasSearch) {
            $request->merge([
                'origin' => $search['origin'],
                'destination' => $search['destination'],
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

            try {
                $resp = $dw->scheduleAllAirline($search);
                $journeysDepart = array_values((array) ($resp['journeyDepart'] ?? []));
                $journeysReturn = array_values((array) ($resp['journeyReturn'] ?? []));

                if (($search['tripType'] ?? 'OneWay') === 'RoundTrip') {
                    foreach ($journeysDepart as $journeyDepart) {
                        $departAirlineId = (string) data_get($journeyDepart, 'airlineID', '');

                        foreach ($journeysReturn as $journeyReturn) {
                            $returnAirlineId = (string) data_get($journeyReturn, 'airlineID', '');

                            if ($departAirlineId === '' || $departAirlineId !== $returnAirlineId) {
                                continue;
                            }

                            $key = (string) Str::uuid();

                            Cache::put("admin_flight_quote:{$key}", [
                                'search' => $search,
                                'journey' => $journeyDepart,
                                'journey_return' => $journeyReturn,
                                'airlineAccessCode' => (string) data_get($resp, 'airlineAccessCode', ''),
                            ], now()->addMinutes(30));

                            $results[] = [
                                'key' => $key,
                                'journey' => $journeyDepart,
                                'journey_return' => $journeyReturn,
                                'pricing' => $pricingService->resolve(
                                    $search,
                                    $journeyDepart,
                                    $journeyReturn,
                                    (float) data_get($journeyDepart, 'sumPrice', 0),
                                    (string) data_get($journeyDepart, 'currency', 'IDR')
                                ),
                            ];
                        }
                    }
                } else {
                    foreach ($journeysDepart as $journey) {
                        $key = (string) Str::uuid();

                        Cache::put("admin_flight_quote:{$key}", [
                            'search' => $search,
                            'journey' => $journey,
                            'journey_return' => [],
                            'airlineAccessCode' => (string) data_get($resp, 'airlineAccessCode', ''),
                        ], now()->addMinutes(30));

                        $results[] = [
                            'key' => $key,
                            'journey' => $journey,
                            'journey_return' => [],
                            'pricing' => $pricingService->resolve(
                                $search,
                                $journey,
                                [],
                                (float) data_get($journey, 'sumPrice', 0),
                                (string) data_get($journey, 'currency', 'IDR')
                            ),
                        ];
                    }
                }

                if (empty($results)) {
                    $searchError = 'Tidak ada tiket pesawat yang cocok untuk filter ini.';
                }
            } catch (\Throwable $e) {
                $searchError = 'Gagal mengambil data tiket supplier: ' . $e->getMessage();
            }
        }

        $q = trim((string) $request->get('q', ''));
        $savedOverrides = FlightPriceOverride::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('airline_name', 'like', "%{$q}%")
                        ->orWhere('flight_number', 'like', "%{$q}%")
                        ->orWhere('origin', 'like', "%{$q}%")
                        ->orWhere('destination', 'like', "%{$q}%")
                        ->orWhere('origin_name', 'like', "%{$q}%")
                        ->orWhere('destination_name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.flights.pricing.index', [
            'cities' => $cities,
            'cityLabelMap' => $cityLabelMap,
            'search' => $search,
            'results' => $results,
            'searchError' => $searchError,
            'savedOverrides' => $savedOverrides,
            'q' => $q,
        ]);
    }

    public function show(string $key, DarmawisataClient $dw, FlightPriceOverrideService $pricingService)
    {
        $payload = Cache::get("admin_flight_quote:{$key}");

        if (!$payload || !is_array($payload)) {
            return redirect()
                ->route('admin.flights.pricing.index')
                ->with('error', 'Data tiket admin sudah kadaluarsa. Lakukan pencarian ulang.');
        }

        $search = (array) ($payload['search'] ?? []);
        $journey = (array) ($payload['journey'] ?? []);
        $journeyReturn = (array) ($payload['journey_return'] ?? []);
        $airlineAccessCode = (string) ($payload['airlineAccessCode'] ?? '');

        [$supplierPriceResponse, $supplierPrice, $currency] = $this->loadSupplierDisplayPrice(
            $dw,
            $search,
            $journey,
            $journeyReturn,
            $airlineAccessCode
        );

        $pricingData = $pricingService->resolve($search, $journey, $journeyReturn, $supplierPrice, $currency);
        $cities = $this->loadCities($dw);

        return view('admin.flights.pricing.show', [
            'quoteKey' => $key,
            'search' => $search,
            'journey' => $journey,
            'journeyReturn' => $journeyReturn,
            'supplierPriceResponse' => $supplierPriceResponse,
            'pricingData' => $pricingData,
            'cities' => $cities,
        ]);
    }

    public function upsert(Request $request, string $key, DarmawisataClient $dw, FlightPriceOverrideService $pricingService)
    {
        $payload = Cache::get("admin_flight_quote:{$key}");

        if (!$payload || !is_array($payload)) {
            return redirect()
                ->route('admin.flights.pricing.index')
                ->with('error', 'Data tiket admin sudah kadaluarsa. Lakukan pencarian ulang.');
        }

        $data = $request->validate([
            'manual_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $search = (array) ($payload['search'] ?? []);
        $journey = (array) ($payload['journey'] ?? []);
        $journeyReturn = (array) ($payload['journey_return'] ?? []);
        $airlineAccessCode = (string) ($payload['airlineAccessCode'] ?? '');

        [, $supplierPrice, $currency] = $this->loadSupplierDisplayPrice(
            $dw,
            $search,
            $journey,
            $journeyReturn,
            $airlineAccessCode
        );

        $snapshot = $pricingService->buildSnapshot($search, $journey, $journeyReturn, $supplierPrice, $currency);
        $pricingKey = (string) $snapshot['pricing_key'];

        $override = FlightPriceOverride::query()->firstOrNew([
            'pricing_key' => $pricingKey,
        ]);

        $override->fill([
            'airline_id' => $snapshot['airline_id'],
            'airline_name' => $snapshot['airline_name'],
            'flight_number' => $snapshot['flight_number'],
            'origin' => $snapshot['origin'],
            'origin_name' => $snapshot['origin_name'],
            'destination' => $snapshot['destination'],
            'destination_name' => $snapshot['destination_name'],
            'trip_type' => $snapshot['trip_type'],
            'depart_date' => $snapshot['depart_date'],
            'return_date' => $snapshot['return_date'],
            'pax_adult' => $snapshot['pax_adult'],
            'pax_child' => $snapshot['pax_child'],
            'pax_infant' => $snapshot['pax_infant'],
            'journey_reference' => $snapshot['journey_reference'],
            'journey_return_reference' => $snapshot['journey_return_reference'],
            'currency' => $snapshot['currency'],
            'supplier_price' => $snapshot['supplier_price'],
            'manual_price' => (float) $data['manual_price'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'payload_snapshot' => json_encode($snapshot['payload_snapshot']),
            'updated_by' => auth()->id(),
        ]);

        if (!$override->exists) {
            $override->created_by = auth()->id();
        }

        $override->save();

        return redirect()
            ->route('admin.flights.pricing.show', $key)
            ->with('success', 'Harga manual tiket pesawat berhasil disimpan.');
    }

    private function loadSupplierDisplayPrice(
        DarmawisataClient $dw,
        array $search,
        array $journey,
        array $journeyReturn = [],
        string $airlineAccessCode = ''
    ): array {
        $supplierPriceResponse = [];
        $supplierPrice = (float) data_get($journey, 'sumPrice', 0);
        $currency = (string) data_get($journey, 'currency', 'IDR');

        try {
            $supplierPriceResponse = $dw->priceAllAirline([
                'airlineID' => (string) data_get($journey, 'airlineID', ''),
                'origin' => strtoupper((string) ($search['origin'] ?? data_get($journey, 'jiOrigin', ''))),
                'destination' => strtoupper((string) ($search['destination'] ?? data_get($journey, 'jiDestination', ''))),
                'tripType' => (string) ($search['tripType'] ?? 'OneWay'),
                'departDate' => (string) ($search['departDate'] ?? ''),
                'returnDate' => (string) ($search['returnDate'] ?? ''),
                'paxAdult' => (int) ($search['paxAdult'] ?? 1),
                'paxChild' => (int) ($search['paxChild'] ?? 0),
                'paxInfant' => (int) ($search['paxInfant'] ?? 0),
                'airlineAccessCode' => $airlineAccessCode,
                'journeyDepartReference' => (string) data_get($journey, 'journeyReference', ''),
                'journeyReturnReference' => (string) data_get($journeyReturn, 'journeyReference', ''),
            ]);

            if (is_numeric(data_get($supplierPriceResponse, 'sumFare'))) {
                $supplierPrice = (float) data_get($supplierPriceResponse, 'sumFare');
            }

            $currency = (string) data_get($supplierPriceResponse, 'currency', $currency);
        } catch (\Throwable $e) {
            // fallback tetap pakai sumPrice dari schedule, biar panel admin tetap jalan
        }

        return [$supplierPriceResponse, $supplierPrice, $currency];
    }

    private function loadCities(DarmawisataClient $dw): array
    {
        try {
            $citiesRes = $dw->airlineCities();
            return (array) ($citiesRes['cities'] ?? []);
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function buildCityLabelMap(array $cities): array
    {
        $map = [];

        foreach ($cities as $c) {
            $code = strtoupper((string) data_get($c, 'cityID', ''));
            $name = trim((string) data_get($c, 'cityName', ''));

            if ($code === '') {
                continue;
            }

            $map[$code] = $name !== '' ? ($name . ' (' . $code . ')') : $code;
        }

        return $map;
    }

    private function normalizeAirportCode(string $value): string
    {
        $raw = trim($value);

        if ($raw === '') {
            return '';
        }

        if (preg_match('/\(([A-Za-z0-9]{2,10})\)\s*$/', $raw, $m)) {
            return strtoupper(trim($m[1]));
        }

        return strtoupper(trim($raw));
    }
}
