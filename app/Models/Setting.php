<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];
    public static function getValue(string $key, $default = null)
    {
        $val = static::where('key', $key)->value('value');
        return ($val === null || $val === '') ? $default : $val;
    }

    public static function invoiceAdminEmail(): ?string
    {
        return static::getValue('invoice_admin_email', static::getValue('footer_email'));
    }
}
