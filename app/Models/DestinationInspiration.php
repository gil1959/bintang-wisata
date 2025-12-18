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
    ];

    public function tourCategory()
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }
}
