<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'reason',
        'identity_type',
        'identity_file_path',
       
        'legal_document_path',
        'password_hash',
        'password_enc',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_note',
        'partner_type',
'bank_name',
'bank_account_number',
'bank_account_holder',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];
}
