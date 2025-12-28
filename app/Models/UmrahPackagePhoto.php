<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmrahPackagePhoto extends Model
{
    protected $fillable = [
        'umrah_package_id',
        'file_path',
    ];

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }
}
