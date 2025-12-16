<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        // IDENTITAS ORDER
        'invoice_number',
        'type',           // tour / rent_car
        'product_id',
        'product_name',

        // CUSTOMER
        'customer_name',
        'customer_email',
        'customer_phone',

        'pickup_date',
        'return_date',

        // DATA KHUSUS TOUR
        'departure_date',
        'participants',

        // DATA KHUSUS RENT CAR
        'total_days',

        // BILLING ADDRESS
        'billing_first_name',
        'billing_last_name',
        'billing_country',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal',
        'billing_phone',

        // HARGA
        'subtotal',
        'discount',
        'final_price',

        // PAYMENT
        'payment_method',
        'payment_status',   // waiting_payment, waiting_verification, paid, failed
        'order_status',     // pending, approved, rejected
    ];

    protected $casts = [
        'departure_date' => 'date',
        'pickup_date' => 'date',
        'return_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }
}
