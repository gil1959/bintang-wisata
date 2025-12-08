<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackageTier extends Model
{
    protected $fillable = [
        'tour_package_id',
        'type',
        'is_custom',
        'min_people',
        'max_people',
        'price',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
