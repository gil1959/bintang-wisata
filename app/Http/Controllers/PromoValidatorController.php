<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\PromoUserUsage;
use Illuminate\Http\Request;

class PromoValidatorController extends Controller
{
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $promo = Promo::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan.'
            ]);
        }

        // Check usage (once per user)
        if (PromoUserUsage::where('user_id', auth()->id())
            ->where('promo_id', $promo->id)->exists()
        ) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo sudah pernah digunakan.'
            ]);
        }

        // hitung diskon
        $basePrice = $request->price;
        $discount = 0;

        if ($promo->type === 'nominal') {
            $discount = $promo->value;
        } else {
            $discount = ($basePrice * $promo->value / 100);
        }

        $final = max(0, $basePrice - $discount);

        return response()->json([
            'valid' => true,
            'promo_id' => $promo->id,
            'original_price' => $basePrice,
            'discount' => $discount,
            'final_price' => $final,
        ]);
    }
}
