<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackagePhoto extends Model
{
    protected $fillable = [
        'tour_package_id',
        'file_path',
    ];


    public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
