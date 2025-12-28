<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipPackageTier extends Model
{
    protected $fillable = [
        'ship_package_id',
        'type',
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
        return $this->belongsTo(ShipPackage::class, 'ship_package_id');
    }
}
