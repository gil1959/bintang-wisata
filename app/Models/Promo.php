<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'type',         // percentage / nominal
        'value',        // angka diskon
        'max_discount', // optional
        'min_price',    // minimal belanja
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | VALIDASI PROMO
    |--------------------------------------------------------------------------
    */

    public function is_valid_for($price)
    {
        if (!$this->is_active) return false;

        $today = Carbon::today();

        if ($this->start_date && $today->lt($this->start_date)) return false;
        if ($this->end_date && $today->gt($this->end_date)) return false;

        if ($this->min_price && $price < $this->min_price) return false;

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | PERHITUNGAN DISKON
    |--------------------------------------------------------------------------
    */

    public function calculate_discount($price)
    {
        if ($this->type === 'percentage') {
            $disc = $price * ($this->value / 100);
        } else {
            $disc = $this->value;
        }

        if ($this->max_discount) {
            $disc = min($disc, $this->max_discount);
        }

        return max(0, $disc);
    }
}
