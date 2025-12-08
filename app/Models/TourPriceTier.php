<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'audience',
        'label',
        'min_peserta',
        'max_peserta',
        'harga_per_orang',
        'sort_order',
    ];

    public function package()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    public function isCustom()
    {
        return is_null($this->min_peserta) && is_null($this->max_peserta);
    }
}
