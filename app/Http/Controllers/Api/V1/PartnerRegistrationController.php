<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class PartnerRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Reuse the website validation, document uploads, pending approval and emails.
        app(\App\Http\Controllers\Front\PartnerRegistrationController::class)->store($request);
        return response()->json(['message'=>'Pendaftaran partner diterima dan sedang menunggu verifikasi admin.'],201);
    }
}
