<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourController extends Controller
{
    // Halaman list paket (home)
    public function index(Request $request)
    {
        $query = TourPackage::query()
            ->where('is_active', true);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $packages = $query->orderBy('title')->paginate(12);

        return view('front.tours.index', compact('packages', 'search'));
    }

    // Halaman detail paket (pakai slug binding)
    public function show(TourPackage $tourPackage)
    {
        // kalau mau, load relasi harga + itinerary
        $tourPackage->load([
            'priceTiers' => function ($q) {
                $q->orderBy('audience_type')->orderBy('min_pax');
            },
            'itineraries' => function ($q) {
                $q->orderBy('day_number')->orderBy('sort_order');
            },
            'images' => function ($q) {
                $q->orderBy('sort_order');
            },
        ]);

        return view('front.tours.show', compact('tourPackage'));
    }
}
