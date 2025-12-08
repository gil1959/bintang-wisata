<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourItinerary extends Model
{
    protected $table = 'tour_itineraries'; // ← WAJIB BANGET

    protected $fillable = [
        'tour_package_id',
        'time',
        'title',
        'sort_order',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
