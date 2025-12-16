<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use App\Models\TourCategory;

class TourController extends Controller
{
    /**
     * Halaman list paket (homepage)
     */
    public function index(Request $request)
    {
        $query = TourPackage::query()
            ->where('is_active', true)
            ->with('category');

        $search = $request->get('q');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $packages = $query->orderBy('title')->paginate(12)->withQueryString();
        $categories = TourCategory::orderBy('name')->get();

        return view('front.tours.index', compact('packages', 'search', 'categories'));
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
    public function home()
    {
        $packages = TourPackage::query()
            ->where('is_active', true)
            ->latest()
            ->with('category') // supaya home bisa tampil kategori aman
            ->take(6)
            ->get();

        return view('front.home', compact('packages'));
    }
}
