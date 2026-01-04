<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicePackageTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'mice_package_id',
        'type', // domestic | foreign
        'label_text',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'integer',
        'sort_order' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(MicePackage::class, 'mice_package_id');
    }
}
