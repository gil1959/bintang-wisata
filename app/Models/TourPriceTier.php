<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'audience_type',   // domestic / wna
        'min_pax',
        'max_pax',
        'price_per_pax',   // diartikan: HARGA PER PAKET
    ];

    protected $casts = [
        'price_per_pax' => 'float',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
