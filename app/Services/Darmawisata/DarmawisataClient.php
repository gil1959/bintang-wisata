<?php

namespace App\Services\Darmawisata;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DarmawisataClient
{
    public function __construct()
    {
        if (!config('darmawisata.user_id') || !config('darmawisata.password')) {
            Log::warning('Darmawisata credential is missing (DARMAWISATA_USER_ID / DARMAWISATA_PASSWORD).');
        }
    }

    public function accessToken(): string
    {
        $ttl = (int) config('darmawisata.token_ttl_minutes', 50);

        return Cache::remember($this->accessTokenCacheKey(), now()->addMinutes($ttl), function () {
            $res = $this->login();

            $token = (string) ($res['accessToken'] ?? '');
            if ($token === '') {
                $msg = (string) ($res['respMessage'] ?? 'accessToken kosong');
                throw new \RuntimeException('Darmawisata login gagal: ' . ($msg !== '' ? $msg : 'accessToken kosong') . '.');
            }

            return $token;
        });
    }

    public function login(): array
    {
        $schemes = $this->candidateSecuritySchemes();
        $last = null;

        foreach ($schemes as $scheme) {
            $token = $this->makeToken();

            $payload = [
                'token'        => $token,
                'securityCode' => $this->makeSecurityCode($token, $scheme),
                'language'     => (int) config('darmawisata.language', 1),
                'userID'       => $this->userId(),
                'accessToken'  => '',
            ];

            $data = $this->post('Session/Login', $payload, false, false);
            Log::info('Darmawisata login response', [
                'scheme' => $scheme,
                'response' => $data,
            ]);
            $last = $data;

            if ($this->isSuccess($data) && (string) ($data['accessToken'] ?? '') !== '') {
                $configured = strtolower((string) config('darmawisata.security_scheme', 'auto'));
                if ($configured === 'auto') {
                    Log::info('Darmawisata login success', [
                        'endpoint' => 'Session/Login',
                        'scheme' => $scheme,
                        'userID' => $this->userId(),
                    ]);
                }

                return $data;
            }
        }

        $this->forgetAccessTokenCache();

        if (is_array($last) && !$this->isSuccess($last)) {
            Log::warning('Darmawisata login failed after trying security schemes', [
                'endpoint' => 'Session/Login',
                'userID' => $this->userId(),
                'schemes' => $schemes,
                'status' => $last['status'] ?? null,
                'respMessage' => $last['respMessage'] ?? null,
            ]);
        }

        return $last ?? [
            'accessToken' => '',
            'status' => 'FAILED',
            'respMessage' => 'Login failed (no response).',
            'userID' => $this->userId(),
        ];
    }

    protected function accessTokenCacheKey(): string
    {
        return 'darmawisata_access_token:' . md5($this->baseUrl() . '|' . $this->userId());
    }

    protected function forgetAccessTokenCache(): void
    {
        Cache::forget($this->accessTokenCacheKey());

        Cache::forget('darmawisata_access_token');
    }

    public function airlineCities(): array
    {
        $ttl = (int) config('darmawisata.cities_ttl_minutes', 10080);

        return Cache::remember('darmawisata_airline_cities', now()->addMinutes($ttl), function () {
            $data = $this->post('Airline/City', []);

            return $data;
        });
    }

    public function airlineNationalities(): array
    {
        $ttl = (int) config('darmawisata.cities_ttl_minutes', 10080);

        return Cache::remember('darmawisata_airline_nationalities', now()->addMinutes($ttl), function () {
            return $this->post('Airline/Nationality', []);
        });
    }

    public function airlineList(): array
    {
        return $this->post('Airline/List', []);
    }

    public function scheduleAllAirline(array $params): array
    {
        $tripType = (string) Arr::get($params, 'tripType', 'OneWay');
        $departDate = (string) Arr::get($params, 'departDate');
        $returnDate = (string) Arr::get($params, 'returnDate');

        $basePayload = [
            'tripType'          => $tripType,
            'origin'            => strtoupper(trim((string) Arr::get($params, 'origin'))),
            'destination'       => strtoupper(trim((string) Arr::get($params, 'destination'))),
            'departDate'        => Carbon::parse($departDate)->format('Y-m-d'),
            'returnDate'        => $returnDate ? Carbon::parse($returnDate)->format('Y-m-d') : null,
            'paxAdult'          => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild'          => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant'         => (int) Arr::get($params, 'paxInfant', 0),
            'promoCode'         => (string) Arr::get($params, 'promoCode', ''),
            'airlineAccessCode' => '',
        ];

        if ($basePayload['returnDate'] === null) {
            unset($basePayload['returnDate']);
        }

        // 1. Coba mode default campuran cache + live
        $resp = $this->post('Airline/ScheduleAllAirline', array_merge($basePayload, [
            'cacheType' => 2,
            'isShowEachAirline' => false,
        ]));

        if ($this->isSuccess($resp) && $this->hasJourneys($resp)) {
            return $resp;
        }

        Log::warning('ScheduleAllAirline fallback to FullLive', [
            'first_response' => $resp,
        ]);

        // 2. Coba mode FullLive
        $respLive = $this->post('Airline/ScheduleAllAirline', array_merge($basePayload, [
            'cacheType' => 1,
            'isShowEachAirline' => false,
        ]));

        if ($this->isSuccess($respLive) && $this->hasJourneys($respLive)) {
            return $respLive;
        }

        Log::warning('ScheduleAllAirline fallback to each-airline loop', [
            'live_response' => $respLive,
        ]);

        // 3. Coba per-airline sesuai dokumentasi
        $aggregate = [
            'journeyDepart' => [],
            'journeyReturn' => [],
            'airlineAccessCode' => '',
            'totalAirline' => 0,
            'airlineIndex' => 0,
            'status' => 'FAILED',
            'respMessage' => 'flight schedule service failed',
            'userID' => $this->userId(),
            'accessToken' => '',
        ];

        $seen = [];
        $maxLoop = 12;
        $currentAirlineAccessCode = '';

        for ($i = 0; $i < $maxLoop; $i++) {
            $payloadEach = array_merge($basePayload, [
                'cacheType' => 1,
                'isShowEachAirline' => true,
                'airlineAccessCode' => $currentAirlineAccessCode,
            ]);

            $respEach = $this->post('Airline/ScheduleAllAirline', $payloadEach);

            Log::info('ScheduleAllAirline each-airline response', [
                'loop' => $i,
                'airlineAccessCode_sent' => $currentAirlineAccessCode,
                'response' => $respEach,
            ]);

            $aggregate['totalAirline'] = (int) ($respEach['totalAirline'] ?? $aggregate['totalAirline'] ?? 0);
            $aggregate['airlineIndex'] = (int) ($respEach['airlineIndex'] ?? $aggregate['airlineIndex'] ?? 0);
            $aggregate['airlineAccessCode'] = (string) ($respEach['airlineAccessCode'] ?? '');

            $depart = (array) ($respEach['journeyDepart'] ?? []);
            $return = (array) ($respEach['journeyReturn'] ?? []);

            if (!empty($depart)) {
                $aggregate['journeyDepart'] = array_merge($aggregate['journeyDepart'], $depart);
            }

            if (!empty($return)) {
                $aggregate['journeyReturn'] = array_merge($aggregate['journeyReturn'], $return);
            }

            if (!empty($aggregate['journeyDepart'])) {
                $aggregate['status'] = 'SUCCESS';
                $aggregate['respMessage'] = 'SUCCESS';
            }

            $nextCode = (string) ($respEach['airlineAccessCode'] ?? '');
            $pointer = $nextCode . '|' . (string) ($respEach['airlineIndex'] ?? '');

            if ($pointer !== '|' && in_array($pointer, $seen, true)) {
                break;
            }

            $seen[] = $pointer;
            $currentAirlineAccessCode = $nextCode;

            $totalAirline = (int) ($respEach['totalAirline'] ?? 0);
            $airlineIndex = (int) ($respEach['airlineIndex'] ?? 0);

            // stop kalau sudah sampai index airline terakhir
            if ($totalAirline > 0 && $airlineIndex >= max(0, $totalAirline - 1)) {
                break;
            }

            // stop kalau pointer reset ke 0 lagi dan tetap tidak ada hasil
            if ($i > 0 && $airlineIndex === 0 && empty($depart) && empty($return)) {
                break;
            }
        }

        if (!empty($aggregate['journeyDepart'])) {
            return $aggregate;
        }

        Log::warning('ScheduleAllAirline fallback to Airline/List + Airline/Schedule', [
            'base_params' => $params,
        ]);

        $airlineListResp = $this->airlineList();
        $airlines = (array) ($airlineListResp['airlines'] ?? []);

        $final = [
            'journeyDepart' => [],
            'journeyReturn' => [],
            'status' => 'FAILED',
            'respMessage' => 'No flights returned from all fallback methods.',
            'userID' => $this->userId(),
            'accessToken' => '',
        ];

        foreach ($airlines as $airline) {
            $airlineId = (string) ($airline['id'] ?? '');
            $airlineName = (string) ($airline['name'] ?? '');

            if ($airlineId === '') {
                continue;
            }

            try {
                $respOne = $this->scheduleByAirline($params, $airlineId);

                Log::info('Airline/Schedule per-airline response', [
                    'airlineID' => $airlineId,
                    'airlineName' => $airlineName,
                    'response' => $respOne,
                ]);

                $depart = (array) ($respOne['journeyDepart'] ?? []);
                $return = (array) ($respOne['journeyReturn'] ?? []);

                if (!empty($depart)) {
                    $final['journeyDepart'] = array_merge($final['journeyDepart'], $depart);
                }

                if (!empty($return)) {
                    $final['journeyReturn'] = array_merge($final['journeyReturn'], $return);
                }
            } catch (\Throwable $e) {
                Log::warning('Airline/Schedule per-airline exception', [
                    'airlineID' => $airlineId,
                    'airlineName' => $airlineName,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($final['journeyDepart'])) {
            $final['status'] = 'SUCCESS';
            $final['respMessage'] = 'SUCCESS';
            return $final;
        }

        return $respLive ?: $resp;
    }

    public function priceAllAirline(array $params): array
    {
        $tripType = (string) Arr::get($params, 'tripType', 'OneWay');
        $departDate = $this->normalizeFlightDate((string) Arr::get($params, 'departDate'));
        $returnDate = $this->normalizeFlightDate((string) Arr::get($params, 'returnDate'));

        $airlineID = trim((string) Arr::get($params, 'airlineID', ''));
        $origin = strtoupper(trim((string) Arr::get($params, 'origin', '')));
        $destination = strtoupper(trim((string) Arr::get($params, 'destination', '')));
        $journeyDepartReference = trim((string) Arr::get($params, 'journeyDepartReference', ''));
        $journeyReturnReference = trim((string) Arr::get($params, 'journeyReturnReference', ''));

        if ($airlineID === '') {
            throw new \InvalidArgumentException('airlineID required');
        }

        if ($origin === '') {
            throw new \InvalidArgumentException('origin required');
        }

        if ($destination === '') {
            throw new \InvalidArgumentException('destination required');
        }

        if ($tripType === '') {
            throw new \InvalidArgumentException('tripType required');
        }

        if ($departDate === '') {
            throw new \InvalidArgumentException('departDate required');
        }

        if ($journeyDepartReference === '') {
            throw new \InvalidArgumentException('journeyDepartReference required');
        }

        $payload = [
            'airlineID' => $airlineID,
            'origin' => $origin,
            'destination' => $destination,
            'tripType' => $tripType,
            'departDate' => $departDate,
            'returnDate' => $tripType === 'RoundTrip' ? $returnDate : '',
            'paxAdult' => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild' => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant' => (int) Arr::get($params, 'paxInfant', 0),

            // tetap kirim, walau kosong, sesuai doc
            'airlineAccessCode' => (string) Arr::get($params, 'airlineAccessCode', ''),

            'journeyDepartReference' => $journeyDepartReference,
            'journeyReturnReference' => $tripType === 'RoundTrip' ? $journeyReturnReference : '',

            // kalau post() lu tidak auto inject, field ini wajib ada
            'userID' => (string) Arr::get($params, 'userID', ''),
            'accessToken' => (string) Arr::get($params, 'accessToken', ''),
        ];

        return $this->post('Airline/PriceAllAirline', $payload);
    }

    public function priceAirline(array $params): array
    {
        $tripType = (string) Arr::get($params, 'tripType', 'OneWay');

        $departDate = (string) Arr::get($params, 'departDate', '');
        $returnDate = (string) Arr::get($params, 'returnDate', '');

        $payload = [
            'airlineID'   => (string) Arr::get($params, 'airlineID', ''),
            'origin'      => strtoupper(trim((string) Arr::get($params, 'origin', ''))),
            'destination' => strtoupper(trim((string) Arr::get($params, 'destination', ''))),
            'tripType'    => $tripType,

            // FIX: format Y-m-d konsisten dengan semua endpoint lain yang berhasil
            'departDate'  => $departDate !== '' ? Carbon::parse($departDate)->format('Y-m-d') : '',

            'paxAdult'  => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild'  => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant' => (int) Arr::get($params, 'paxInfant', 0),

            'searchKey' => (string) Arr::get($params, 'searchKey', ''),
            'promoCode' => (string) Arr::get($params, 'promoCode', ''),

            'schDeparts' => array_values((array) Arr::get($params, 'schDeparts', [])),
        ];

        // FIX ROOT CAUSE:
        // returnDate dan schReturns HANYA disertakan untuk RoundTrip.
        // Mengirim returnDate="" (empty string) ke .NET DateTimeOffset/DateTime
        // menyebabkan seluruh request object gagal diparse → semua field null → FAILED.
        // Solusi: unset (jangan kirim field sama sekali) untuk OneWay,
        // persis seperti yang dilakukan bookingAirline(), issuedAirline(), scheduleAllAirline().
        if ($tripType === 'RoundTrip') {
            $payload['returnDate'] = $returnDate !== '' ? Carbon::parse($returnDate)->format('Y-m-d') : '';
            $payload['schReturns'] = array_values((array) Arr::get($params, 'schReturns', []));
        }
        // OneWay: returnDate dan schReturns tidak ada di payload → .NET skip → OK

        $this->assertRequired($payload, [
            'airlineID',
            'origin',
            'destination',
            'tripType',
            'departDate',
            'schDeparts',
        ], 'Airline/Price');

        return $this->post('Airline/Price', $payload);
    }

    public function baggageAndMeal(array $params): array
    {
        $payload = [
            'airlineID' => (string) Arr::get($params, 'airlineID', ''),
            'origin' => strtoupper(trim((string) Arr::get($params, 'origin', ''))),
            'destination' => strtoupper(trim((string) Arr::get($params, 'destination', ''))),
            'tripType' => (string) Arr::get($params, 'tripType', 'OneWay'),
            'departDate' => $this->normalizeFlightDate((string) Arr::get($params, 'departDate')),
            'returnDate' => $this->normalizeFlightDate((string) Arr::get($params, 'returnDate')),
            'schDepart' => (string) Arr::get($params, 'schDepart', ''),
            'schReturn' => (string) Arr::get($params, 'schReturn', ''),
            'paxAdult' => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild' => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant' => (int) Arr::get($params, 'paxInfant', 0),
            'departureAirlineSegmentCode' => (string) Arr::get($params, 'departureAirlineSegmentCode', ''),
            'departureFareBasisCode' => (string) Arr::get($params, 'departureFareBasisCode', ''),
            'returnAirlineSegmentCode' => (string) Arr::get($params, 'returnAirlineSegmentCode', ''),
            'returnFareBasisCode' => (string) Arr::get($params, 'returnFareBasisCode', ''),
            'contactFirstName' => (string) Arr::get($params, 'contactFirstName', ''),
            'contactLastName' => (string) Arr::get($params, 'contactLastName', ''),
            'contactTitle' => (string) Arr::get($params, 'contactTitle', ''),
            'contactCountryCodePhone' => (string) Arr::get($params, 'contactCountryCodePhone', ''),
            'contactAreaCodePhone' => (string) Arr::get($params, 'contactAreaCodePhone', ''),
            'contactRemainingPhoneNo' => (string) Arr::get($params, 'contactRemainingPhoneNo', ''),
            'contactEmail' => (string) Arr::get($params, 'contactEmail', ''),
            'paxDetails' => array_values((array) Arr::get($params, 'paxDetails', [])),
            'insurance' => (bool) Arr::get($params, 'insurance', false),
        ];

        $payload = $this->prepareAddonPayload($payload);

        if (($payload['departureAirlineSegmentCode'] ?? '') === '') {
            $payload['departureAirlineSegmentCode'] = null;
        }
        if (($payload['departureFareBasisCode'] ?? '') === '') {
            $payload['departureFareBasisCode'] = null;
        }

        $this->assertRequired($payload, [
            'airlineID',
            'origin',
            'destination',
            'tripType',
            'departDate',
            'schDepart',
            'contactFirstName',
            'contactLastName',
            'contactTitle',
            'contactCountryCodePhone',
            'contactAreaCodePhone',
            'contactRemainingPhoneNo',
            'contactEmail',
            'paxDetails',
        ], 'Airline/BaggageAndMeal');

        return $this->post('Airline/BaggageAndMeal', $payload);
    }

    public function seat(array $params): array
    {
        $payload = [
            'airlineID' => (string) Arr::get($params, 'airlineID', ''),
            'origin' => strtoupper(trim((string) Arr::get($params, 'origin', ''))),
            'destination' => strtoupper(trim((string) Arr::get($params, 'destination', ''))),
            'tripType' => (string) Arr::get($params, 'tripType', 'OneWay'),
            'departDate' => $this->normalizeFlightDate((string) Arr::get($params, 'departDate')),
            'returnDate' => $this->normalizeFlightDate((string) Arr::get($params, 'returnDate')),
            'schDepart' => (string) Arr::get($params, 'schDepart', ''),
            'schReturn' => (string) Arr::get($params, 'schReturn', ''),
            'paxAdult' => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild' => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant' => (int) Arr::get($params, 'paxInfant', 0),
            'departureAirlineSegmentCode' => (string) Arr::get($params, 'departureAirlineSegmentCode', ''),
            'departureFareBasisCode' => (string) Arr::get($params, 'departureFareBasisCode', ''),
            'returnAirlineSegmentCode' => (string) Arr::get($params, 'returnAirlineSegmentCode', ''),
            'returnFareBasisCode' => (string) Arr::get($params, 'returnFareBasisCode', ''),
            'contactFirstName' => (string) Arr::get($params, 'contactFirstName', ''),
            'contactLastName' => (string) Arr::get($params, 'contactLastName', ''),
            'contactTitle' => (string) Arr::get($params, 'contactTitle', ''),
            'contactCountryCodePhone' => (string) Arr::get($params, 'contactCountryCodePhone', ''),
            'contactAreaCodePhone' => (string) Arr::get($params, 'contactAreaCodePhone', ''),
            'contactRemainingPhoneNo' => (string) Arr::get($params, 'contactRemainingPhoneNo', ''),
            'contactEmail' => (string) Arr::get($params, 'contactEmail', ''),
            'paxDetails' => array_values((array) Arr::get($params, 'paxDetails', [])),
            'insurance' => (bool) Arr::get($params, 'insurance', false),
        ];

        $payload = $this->prepareAddonPayload($payload);

        if (($payload['departureAirlineSegmentCode'] ?? '') === '') {
            $payload['departureAirlineSegmentCode'] = null;
        }
        if (($payload['departureFareBasisCode'] ?? '') === '') {
            $payload['departureFareBasisCode'] = null;
        }

        $this->assertRequired($payload, [
            'airlineID',
            'origin',
            'destination',
            'tripType',
            'departDate',
            'schDepart',
            'contactFirstName',
            'contactLastName',
            'contactTitle',
            'contactCountryCodePhone',
            'contactAreaCodePhone',
            'contactRemainingPhoneNo',
            'contactEmail',
            'paxDetails',
        ], 'Airline/Seat');

        return $this->post('Airline/Seat', $payload);
    }

    public function bookingAirline(array $params): array
    {
        $payload = [
            'airlineID' => (string) Arr::get($params, 'airlineID', ''),
            'origin' => strtoupper(trim((string) Arr::get($params, 'origin', ''))),
            'destination' => strtoupper(trim((string) Arr::get($params, 'destination', ''))),
            'tripType' => (string) Arr::get($params, 'tripType', 'OneWay'),
            'departDate' => $this->normalizeFlightDate((string) Arr::get($params, 'departDate')),
            'returnDate' => $this->normalizeFlightDate((string) Arr::get($params, 'returnDate')),
            'paxAdult' => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild' => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant' => (int) Arr::get($params, 'paxInfant', 0),
            'schDeparts' => array_values((array) Arr::get($params, 'schDeparts', [])),
            'schReturns' => array_values((array) Arr::get($params, 'schReturns', [])),
            'contactFirstName' => (string) Arr::get($params, 'contactFirstName', ''),
            'contactLastName' => (string) Arr::get($params, 'contactLastName', ''),
            'contactTitle' => (string) Arr::get($params, 'contactTitle', ''),
            'contactCountryCodePhone' => (string) Arr::get($params, 'contactCountryCodePhone', ''),
            'contactAreaCodePhone' => (string) Arr::get($params, 'contactAreaCodePhone', ''),
            'contactRemainingPhoneNo' => (string) Arr::get($params, 'contactRemainingPhoneNo', ''),
            'contactEmail' => (string) Arr::get($params, 'contactEmail', ''),
            'paxDetails' => array_values((array) Arr::get($params, 'paxDetails', [])),
            'searchKey' => (string) Arr::get($params, 'searchKey', ''),
            'insurance' => (bool) Arr::get($params, 'insurance', false),
            'promoCode' => (string) Arr::get($params, 'promoCode', ''),
        ];

        $payload = $this->prepareBookingPayload($payload);
        // Hapus field internal _typeString sebelum dikirim ke supplier
        $payload['paxDetails'] = array_map(function ($pax) {
            unset($pax['_typeString']);
            return $pax;
        }, $payload['paxDetails']);

        $this->assertRequired($payload, [
            'airlineID',
            'origin',
            'destination',
            'tripType',
            'departDate',
            'schDeparts',
            'contactFirstName',
            'contactLastName',
            'contactTitle',
            'contactCountryCodePhone',
            'contactAreaCodePhone',
            'contactRemainingPhoneNo',
            'contactEmail',
            'paxDetails',
        ], 'Airline/Booking');

        return $this->post('Airline/Booking', $payload, true, true, 90);
    }

    public function issuedAirline(array $params): array
    {
        $payload = [
            'airlineID' => (string) Arr::get($params, 'airlineID', ''),
            'origin' => strtoupper(trim((string) Arr::get($params, 'origin', ''))),
            'destination' => strtoupper(trim((string) Arr::get($params, 'destination', ''))),
            'tripType' => (string) Arr::get($params, 'tripType', 'OneWay'),
            'departDate' => $this->normalizeFlightDate((string) Arr::get($params, 'departDate')),
            'returnDate' => $this->normalizeFlightDate((string) Arr::get($params, 'returnDate')),
            'bookingCode' => (string) Arr::get($params, 'bookingCode', ''),
            'bookingDate' => (string) Arr::get($params, 'bookingDate', ''),
            'airlineAccessCode' => (string) Arr::get($params, 'airlineAccessCode', ''),
        ];

        $payload = $this->prepareIssuedPayload($payload);
        $this->assertRequired($payload, [
            'airlineID',
            'origin',
            'destination',
            'tripType',
            'departDate',
            'bookingCode',
            'bookingDate',
        ], 'Airline/Issued');

        return $this->post('Airline/Issued', $payload);
    }

    public function bookingDetailAirline(array $params): array
    {
        $payload = [
            'bookingCode' => (string) Arr::get($params, 'bookingCode', ''),
            'referenceNo' => (string) Arr::get($params, 'referenceNo', ''),
            'bookingDate' => (string) Arr::get($params, 'bookingDate', ''),
        ];

        if ($payload['referenceNo'] === '') {
            unset($payload['referenceNo']);
        }

        $this->assertRequired($payload, [
            'bookingCode',
            'bookingDate',
        ], 'Airline/BookingDetail');

        return $this->post('Airline/BookingDetail', $payload);
    }

    public function bookingListAirline(array $params): array
    {
        $payload = [
            'filterByStatus' => (int) Arr::get($params, 'filterByStatus', 0),
            'startDate' => (string) Arr::get($params, 'startDate', ''),
            'endDate' => (string) Arr::get($params, 'endDate', ''),
        ];

        $this->assertRequired($payload, [
            'filterByStatus',
            'startDate',
            'endDate',
        ], 'Airline/BookingList');

        return $this->post('Airline/BookingList', $payload);
    }

    public function scheduleByAirline(array $params, string $airlineId): array
    {
        $tripType = (string) Arr::get($params, 'tripType', 'OneWay');
        $departDate = (string) Arr::get($params, 'departDate');
        $returnDate = (string) Arr::get($params, 'returnDate');

        $payload = [
            'airlineID'         => $airlineId,
            'tripType'          => $tripType,
            'origin'            => strtoupper(trim((string) Arr::get($params, 'origin'))),
            'destination'       => strtoupper(trim((string) Arr::get($params, 'destination'))),
            'departDate'        => Carbon::parse($departDate)->format('Y-m-d'),
            'returnDate'        => $returnDate ? Carbon::parse($returnDate)->format('Y-m-d') : null,
            'paxAdult'          => (int) Arr::get($params, 'paxAdult', 1),
            'paxChild'          => (int) Arr::get($params, 'paxChild', 0),
            'paxInfant'         => (int) Arr::get($params, 'paxInfant', 0),
            'promoCode'         => (string) Arr::get($params, 'promoCode', ''),
            'airlineAccessCode' => '',
        ];

        if ($payload['returnDate'] === null) {
            unset($payload['returnDate']);
        }

        $resp = $this->post('Airline/Schedule', $payload);

        // kalau supplier tertentu minta airlineAccessCode, retry sekali pakai code dari response
        $accessCode = (string) ($resp['airlineAccessCode'] ?? '');
        if ($accessCode !== '' && !$this->hasJourneys($resp)) {
            $payload['airlineAccessCode'] = $accessCode;
            $retry = $this->post('Airline/Schedule', $payload);

            Log::info('Airline/Schedule retry with airlineAccessCode', [
                'airlineID' => $airlineId,
                'airlineAccessCode' => $accessCode,
                'response' => $retry,
            ]);

            return $retry;
        }

        return $resp;
    }

    protected function normalizeFlightDate(?string $value): string
    {
        if (!$value || $value === '0001-01-01T00:00:00') {
            return '0001-01-01T00:00:00';
        }

        return Carbon::parse($value)->format('Y-m-d') . 'T00:00:00';
    }

    protected function prepareAddonPayload(array $payload): array
    {
        if (($payload['tripType'] ?? 'OneWay') !== 'RoundTrip') {

            $payload['returnDate'] = '0001-01-01T00:00:00';
            unset(
                $payload['schReturn'],
                $payload['returnAirlineSegmentCode'],
                $payload['returnFareBasisCode']
            );
        } else {
            if (($payload['returnDate'] ?? null) === null || $payload['returnDate'] === '') {
                throw new RuntimeException('Payload RoundTrip wajib punya returnDate.');
            }
        }

        return $payload;
    }

    protected function prepareBookingPayload(array $payload): array
    {
        if (($payload['tripType'] ?? 'OneWay') !== 'RoundTrip') {

            $payload['returnDate'] = '0001-01-01T00:00:00';
            $payload['schReturns'] = null;
        } else {
            if (($payload['returnDate'] ?? null) === null || $payload['returnDate'] === '') {
                throw new RuntimeException('Payload RoundTrip wajib punya returnDate.');
            }
        }

        if (empty($payload['schReturns'])) {
            unset($payload['schReturns']);
        }
        if (!array_key_exists('searchKey', $payload)) {
            $payload['searchKey'] = '';
        }

        if (($payload['promoCode'] ?? '') === '') {
            unset($payload['promoCode']);
        }

        return $payload;
    }

    protected function prepareIssuedPayload(array $payload): array
    {
        if (($payload['tripType'] ?? 'OneWay') !== 'RoundTrip') {
            unset($payload['returnDate']);
        }

        if (($payload['airlineAccessCode'] ?? '') === '') {
            unset($payload['airlineAccessCode']);
        }

        return $payload;
    }

    protected function assertRequired(array $payload, array $keys, string $endpoint): void
    {
        $missing = [];

        foreach ($keys as $key) {
            $value = $payload[$key] ?? null;

            $empty =
                $value === null ||
                $value === '' ||
                (is_array($value) && count($value) === 0);

            if ($empty) {
                $missing[] = $key;
            }
        }

        if ($missing !== []) {
            throw new RuntimeException(
                $endpoint . ' payload belum lengkap. Missing: ' . implode(', ', $missing)
            );
        }
    }

    protected function cleanEmpty(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if ($value === null || $value === '') {
                unset($payload[$key]);
            }
        }

        return $payload;
    }
    protected function post(string $endpoint, array $payload, bool $injectAuth = true, bool $logFailed = true, ?int $timeoutOverride = null): array
    {
        $base = $this->baseUrl();
        $url = $base . '/' . ltrim($endpoint, '/');

        if ($injectAuth) {
            $payload['userID'] = $this->userId();
            $payload['accessToken'] = $this->accessToken();
        }

        $timeout = $timeoutOverride ?? (int) config('darmawisata.timeout', 30);
        Log::info('Darmawisata request', [
            'endpoint' => $endpoint,
            'url' => $url,
            'payload' => $payload,
        ]);
        $resp = Http::timeout($timeout)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if (!$resp->ok()) {
            Log::error('Darmawisata HTTP error', [
                'endpoint' => $endpoint,
                'url' => $url,
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);

            throw new \RuntimeException('Darmawisata HTTP error: ' . $resp->status());
        }

        $data = $resp->json();
        if (!is_array($data)) {
            Log::error('Darmawisata invalid JSON', [
                'endpoint' => $endpoint,
                'body' => $resp->body(),
            ]);
            throw new \RuntimeException('Darmawisata invalid JSON response.');
        }

        if ($logFailed && !$this->isSuccess($data)) {
            $msg = (string) ($data['respMessage'] ?? 'Unknown error');
            Log::warning('Darmawisata API FAILED', [
                'endpoint' => $endpoint,
                'message' => $msg,
                'data' => $data,
            ]);
        }

        return $data;
    }

    protected function isSuccess(array $data): bool
    {
        $status = strtoupper((string) ($data['status'] ?? ''));
        return $status === 'SUCCESS';
    }

    protected function hasJourneys(array $data): bool
    {
        return count((array) ($data['journeyDepart'] ?? [])) > 0
            || count((array) ($data['journeyReturn'] ?? [])) > 0;
    }

    protected function makeToken(): string
    {
        return now('Asia/Jakarta')->format('YmdHis');
    }

    protected function baseUrl(): string
    {
        $value = rtrim((string) config('darmawisata.base_url'), '/');

        Log::info('DARMA_BASEURL_RUNTIME_CHECK', [
            'config_base_url' => config('darmawisata.base_url'),
            'final_base_url' => $value,
            'env_base_url' => env('DARMAWISATA_BASE_URL'),
        ]);

        return $value;
    }

    protected function userId(): string
    {
        return trim((string) config('darmawisata.user_id'));
    }

    /**
     * @return array{0:string,1:string} [passwordRaw, passwordMd5]
     */
    protected function normalizedPasswords(): array
    {
        $raw = trim((string) config('darmawisata.password'));

        $isAlreadyMd5 = (bool) preg_match('/^[a-f0-9]{32}$/i', $raw);
        $md5 = $isAlreadyMd5 ? strtolower($raw) : md5($raw);

        return [$raw, $md5];
    }

    protected function candidateSecuritySchemes(): array
    {
        $primary = strtolower((string) config('darmawisata.security_scheme', 'auto'));

        $list = config('darmawisata.security_schemes', []);
        if (!is_array($list) || count($list) === 0) {
            $list = [
                'md5_password_token',
                'md5_token_password',
                'md5_userid_password_token',
                'md5_userid_token_password',
                'md5_md5password_token',
                'md5_token_md5password',
                'md5_userid_md5password_token',
                'md5_userid_token_md5password',
            ];
        }

        $out = [];

        if ($primary !== '' && $primary !== 'auto') {
            $out[] = $primary;
        }

        foreach ($list as $s) {
            $s = strtolower(trim((string) $s));
            if ($s === '' || $s === 'auto') {
                continue;
            }
            if (!in_array($s, $out, true)) {
                $out[] = $s;
            }
        }

        return $out ?: ['md5_password_token'];
    }

    protected function makeSecurityCode(string $token, string $scheme): string
    {
        [$passwordRaw, $passwordMd5] = $this->normalizedPasswords();
        $userId = $this->userId();

        return match (strtolower($scheme)) {
            'md5_token_password' => md5($token . $passwordRaw),
            'md5_userid_password_token' => md5($userId . $passwordRaw . $token),
            'md5_userid_token_password' => md5($userId . $token . $passwordRaw),
            'md5_password_userid_token' => md5($passwordRaw . $userId . $token),

            'md5_md5password_token' => md5($passwordMd5 . $token),
            'md5_token_md5password' => md5($token . $passwordMd5),
            'md5_userid_md5password_token' => md5($userId . $passwordMd5 . $token),
            'md5_userid_token_md5password' => md5($userId . $token . $passwordMd5),

            default => md5($passwordRaw . $token),
        };
    }

    public function agentBalance(): array
    {
        return $this->post('Agent/Balance', []);
    }
}
