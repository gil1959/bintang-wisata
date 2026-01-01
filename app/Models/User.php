<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'address',
    'full_address',
    'sub_district',
    'email_verified_at',
   // affiliate
'is_affiliate',
'affiliate_status',
'affiliate_requested_at',
'affiliate_reviewed_at',
'affiliate_reviewed_by',
'affiliate_review_note',
'affiliate_commission_type',
'affiliate_commission_value',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_affiliate' => 'boolean',
        'affiliate_requested_at' => 'datetime',
    'affiliate_reviewed_at' => 'datetime',
    ];
}
