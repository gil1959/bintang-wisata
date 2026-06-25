<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
