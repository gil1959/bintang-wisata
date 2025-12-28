<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationInspiration extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'icon',
        'tour_category_id',
        'sort_order',
        'is_active',
        'tour_subcategory_id',

    ];

    public function tourCategory()
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }
    public function tourSubcategory()
{
    return $this->belongsTo(TourCategory::class, 'tour_subcategory_id');
}

}
