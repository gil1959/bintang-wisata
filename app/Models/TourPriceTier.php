<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'audience_type',
        'min_pax',
        'max_pax',
        'price_per_pax',
    ];

    protected $casts = [
        'min_pax' => 'integer',
        'max_pax' => 'integer',
        'price_per_pax' => 'float',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
