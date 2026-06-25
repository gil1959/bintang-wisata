<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourItinerary extends Model
{
    protected $table = 'tour_itineraries'; // ← WAJIB BANGET

    protected $fillable = [
        'tour_package_id',
        'title',
        'sort_order',
        'title_en',

    ];


    public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
