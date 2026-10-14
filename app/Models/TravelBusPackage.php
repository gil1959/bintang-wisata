<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelBusPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_partner_id',
        'slug',
        'armada_name',
        'armada_type',
        'travel_type',
        'departure_location',
        'arrival_location',
        'route_details',
        'departure_time',
        'arrival_time',
        'arrival_days_offset',
        'duration_minutes',
        'duration_text',
        'price',
        'facilities',
        'thumbnail_path',
        'description',
        'terms_conditions',
        'is_active',
        'partner_review_status',
        'partner_review_note',
        'partner_reviewed_by',
        'partner_reviewed_at',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'facilities' => 'array',
        'route_details' => 'array',
        'arrival_days_offset' => 'integer',
        'duration_minutes' => 'integer',
        'partner_reviewed_at' => 'datetime',
    ];

    public function photos()
    {
        return $this->hasMany(TravelBusPackagePhoto::class, 'travel_bus_package_id');
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'created_by_partner_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Helper untuk memformat durasi (misal: 1800 menit -> 30 Jam atau 1 Hari 6 Jam)
    public function getFormattedDurationAttribute()
    {
        if (!empty($this->duration_text)) {
            return $this->duration_text;
        }

        if ($this->duration_minutes <= 0) {
            return '-';
        }

        $totalHours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($totalHours >= 24) {
            $days = floor($totalHours / 24);
            $remainingHours = $totalHours % 24;
            $text = "{$days} Hari";
            if ($remainingHours > 0) {
                $text .= " {$remainingHours} Jam";
            }
            if ($minutes > 0) {
                $text .= " {$minutes} Menit";
            }
            return $text;
        }

        if ($minutes > 0) {
            return "{$totalHours}j {$minutes}m";
        }

        return "{$totalHours} Jam";
    }

    // Helper format jam keberangkatan H:i
    public function getFormattedDepartureTimeAttribute()
    {
        return date('H:i', strtotime($this->departure_time));
    }

    // Helper format jam tiba H:i (+1 Hari jika lewat hari)
    public function getFormattedArrivalTimeAttribute()
    {
        $time = date('H:i', strtotime($this->arrival_time));
        if ($this->arrival_days_offset > 0) {
            return "{$time} (+{$this->arrival_days_offset} Hari)";
        }
        return $time;
    }
}
