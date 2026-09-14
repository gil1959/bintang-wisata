<?php

namespace App\Services;

use App\Models\FlightPriceOverride;

class FlightPriceOverrideService
{
    public function makePricingKey(array $search, array $journey, array $journeyReturn = []): string
    {
        $payload = [
            'tripType' => (string) ($search['tripType'] ?? 'OneWay'),
            'origin' => strtoupper((string) ($search['origin'] ?? data_get($journey, 'jiOrigin', ''))),
            'destination' => strtoupper((string) ($search['destination'] ?? data_get($journey, 'jiDestination', ''))),
            'departDate' => (string) ($search['departDate'] ?? ''),
            'returnDate' => (string) ($search['returnDate'] ?? ''),
            'paxAdult' => (int) ($search['paxAdult'] ?? 1),
            'paxChild' => (int) ($search['paxChild'] ?? 0),
            'paxInfant' => (int) ($search['paxInfant'] ?? 0),
            'airlineID' => (string) data_get($journey, 'airlineID', ''),
            'journeyReference' => (string) data_get($journey, 'journeyReference', ''),
            'journeyReturnReference' => (string) data_get($journeyReturn, 'journeyReference', ''),
            'flightNumber' => (string) data_get($journey, 'segment.0.flightDetail.0.flightNumber', ''),
            'returnFlightNumber' => (string) data_get($journeyReturn, 'segment.0.flightDetail.0.flightNumber', ''),
            'departTime' => (string) data_get($journey, 'jiDepartTime', ''),
            'returnDepartTime' => (string) data_get($journeyReturn, 'jiDepartTime', ''),
        ];

        return sha1(json_encode($payload));
    }

    public function findActiveOverride(array $search, array $journey, array $journeyReturn = []): ?FlightPriceOverride
    {
        return FlightPriceOverride::query()
            ->where('pricing_key', $this->makePricingKey($search, $journey, $journeyReturn))
            ->where('is_active', true)
            ->latest('id')
            ->first();
    }

    public function buildSnapshot(
        array $search,
        array $journey,
        array $journeyReturn = [],
        ?float $supplierPrice = null,
        string $currency = 'IDR'
    ): array {
        $origin = strtoupper((string) ($search['origin'] ?? data_get($journey, 'jiOrigin', '')));
        $destination = strtoupper((string) ($search['destination'] ?? data_get($journey, 'jiDestination', '')));

        return [
            'pricing_key' => $this->makePricingKey($search, $journey, $journeyReturn),
            'airline_id' => (string) data_get($journey, 'airlineID', ''),
            'airline_name' => (string) data_get($journey, 'airlineName', ''),
            'flight_number' => (string) data_get($journey, 'segment.0.flightDetail.0.flightNumber', ''),
            'origin' => $origin,
            'origin_name' => (string) data_get($journey, 'jiOriginName', $origin),
            'destination' => $destination,
            'destination_name' => (string) data_get($journey, 'jiDestinationName', $destination),
            'trip_type' => (string) ($search['tripType'] ?? 'OneWay'),
            'depart_date' => !empty($search['departDate']) ? $search['departDate'] : null,
            'return_date' => !empty($search['returnDate']) ? $search['returnDate'] : null,
            'pax_adult' => (int) ($search['paxAdult'] ?? 1),
            'pax_child' => (int) ($search['paxChild'] ?? 0),
            'pax_infant' => (int) ($search['paxInfant'] ?? 0),
            'journey_reference' => (string) data_get($journey, 'journeyReference', ''),
            'journey_return_reference' => (string) data_get($journeyReturn, 'journeyReference', ''),
            'currency' => strtoupper(trim($currency)) !== '' ? strtoupper(trim($currency)) : 'IDR',
            'supplier_price' => is_numeric($supplierPrice) ? round((float) $supplierPrice, 2) : null,
            'payload_snapshot' => [
                'search' => $search,
                'journey' => $journey,
                'journey_return' => $journeyReturn,
            ],
        ];
    }

    public function resolve(
        array $search,
        array $journey,
        array $journeyReturn = [],
        ?float $supplierPrice = null,
        string $currency = 'IDR'
    ): array {
        $override = $this->findActiveOverride($search, $journey, $journeyReturn);

        $supplier = is_numeric($supplierPrice) ? (int) round((float) $supplierPrice) : null;
        $manual = ($override && is_numeric($override->manual_price)) ? (int) round((float) $override->manual_price) : null;
        $final = $manual !== null ? $manual : $supplier;

        return [
            'pricing_key' => $this->makePricingKey($search, $journey, $journeyReturn),
            'currency' => strtoupper(trim($currency)) !== '' ? strtoupper(trim($currency)) : 'IDR',
            'supplier_price' => $supplier,
            'manual_price' => $manual,
            'final_price' => $final,
            'is_manual' => $manual !== null,
            'override_id' => $override?->id,
            'override' => $override,
        ];
    }
}
