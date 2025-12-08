<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Halaman list paket (homepage)
     */
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

    /**
     * Halaman detail paket (pakai slug binding)
     */
    public function show(TourPackage $tourPackage)
    {
        $tourPackage->load([
            'tiers',
            'itineraries',
            'photos'
        ]);

        return view('front.tours.show', [
            'package' => $tourPackage
        ]);
    }
}
