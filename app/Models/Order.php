<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\PartnerPayoutService;

class Order extends Model
{
    protected $fillable = [
        'user_id',

        // IDENTITAS ORDER
        'invoice_number',
        'type',           // tour / rent_car
        'product_id',
        'product_name',
// PROMO (guest: enforce by email/phone)
'promo_id',
'promo_code',
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
        'unique_code',
        'payable_amount',

        // HARGA
        'subtotal',
        'discount',
        'final_price',

        // PAYMENT
        'payment_method',
        'payment_status',   
        'order_status',    
        'affiliate_user_id',
'affiliate_link_id',
'affiliate_ref',
'affiliate_commission_type',
'affiliate_commission_value',
'affiliate_commission_amount',
'affiliate_commission_status',
'affiliate_commission_set_by',
'affiliate_commission_set_at',
    ];

    protected $casts = [
         'user_id' => 'integer',
        'departure_date' => 'date',
        'pickup_date' => 'date',
        'return_date' => 'date',
        'affiliate_commission_set_at' => 'datetime',

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
    protected static function booted()
{
    static::saved(function (Order $order) {
        // cegah call kalau bukan paid+approved (service juga ngecek, ini buat hemat query)
        if ($order->payment_status !== 'paid' || $order->order_status !== 'approved') {
            return;
        }

        // jalankan payout sekali
        app(PartnerPayoutService::class)->creditIfEligible($order);
    });
}

}
