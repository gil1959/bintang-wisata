<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightPriceOverride extends Model
{
    protected $fillable = [
        'pricing_key',
        'airline_id',
        'airline_name',
        'flight_number',
        'origin',
        'origin_name',
        'destination',
        'destination_name',
        'trip_type',
        'depart_date',
        'return_date',
        'pax_adult',
        'pax_child',
        'pax_infant',
        'journey_reference',
        'journey_return_reference',
        'currency',
        'supplier_price',
        'manual_price',
        'is_active',
        'payload_snapshot',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'depart_date' => 'date',
        'return_date' => 'date',
        'supplier_price' => 'float',
        'manual_price' => 'float',
        'is_active' => 'boolean',
    ];

    public function getPayloadSnapshotArrayAttribute(): array
    {
        $raw = $this->payload_snapshot;

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
