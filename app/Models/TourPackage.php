<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'destination',
        'duration_text',
        'short_description',
        'flight_surcharge_per_pax',
        'description',
        'include_flight_option',
        'is_active',
        'thumbnail_path',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'include_flight_option' => 'boolean',
        'is_active' => 'boolean',
        'flight_surcharge_per_pax' => 'decimal:2',
    ];

    // === RELASI ===

    public function priceTiers()
    {
        return $this->hasMany(TourPriceTier::class);
    }

    public function getBasePricePerPax(string $audienceType, int $paxCount): ?float
    {
        $tier = $this->priceTiers()
            ->where('audience_type', $audienceType) // 'domestic' atau 'wna'
            ->where('min_pax', '<=', $paxCount)
            ->where('max_pax', '>=', $paxCount)
            ->orderBy('min_pax')
            ->first();

        return $tier?->price_per_pax; // null kalau tidak ketemu
    }

    /**
     * Hitung total harga untuk 1 booking paket ini.
     */
    public function calculateTotalPrice(
        string $audienceType,       // 'domestic' / 'wna'
        int $paxCount,              // jumlah orang
        bool $withFlight = false    // true kalau pilih "dengan tiket pesawat"
    ): ?float {
        $base = $this->getBasePricePerPax($audienceType, $paxCount);

        if ($base === null) {
            // tidak ada tier yang cocok -> biar di-handle manual / via WA
            return null;
        }

        $perPax = $base;

        if ($withFlight && $this->include_flight_option && $this->flight_surcharge_per_pax) {
            $perPax += $this->flight_surcharge_per_pax;
        }

        return $perPax * $paxCount;
    }

    public function itineraries()
    {
        return $this->hasMany(TourItinerary::class)
            ->orderBy('day_number')
            ->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(TourImage::class)
            ->orderBy('sort_order');
    }

    // scope bantuan: hanya paket aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
