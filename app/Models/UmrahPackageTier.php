<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmrahPackageTier extends Model
{
    protected $fillable = [
        'umrah_package_id',
        'label_text',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'int',
        'sort_order' => 'int',
    ];

    public function package()
    {
        return $this->belongsTo(UmrahPackage::class, 'umrah_package_id');
    }
}
