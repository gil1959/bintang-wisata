<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use App\Models\TourCategory;
use App\Models\DestinationInspiration;
use App\Models\ClientLogo;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

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
            ->with('category')
            ->take(6)
            ->get();

        $inspirations = DestinationInspiration::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $clientLogos = ClientLogo::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // ===================== PROMO TOURS (Home Section) =====================
        $promoEnabled = filter_var(Setting::getValue('home_promo_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
        $promoMode = Setting::getValue('home_promo_mode', 'auto'); // auto | custom

        $promoTours = collect();

        if ($promoEnabled) {
            $customIds = json_decode(Setting::getValue('home_promo_custom_ids', '[]'), true);
            $customIds = is_array($customIds) ? array_values(array_unique(array_map('intval', $customIds))) : [];

            if ($promoMode === 'custom' && count($customIds) > 0) {
                // Ambil sesuai urutan admin (FIELD untuk MySQL)
                $idsCsv = implode(',', $customIds);

                $promoTours = TourPackage::query()
                    ->where('is_active', true)
                    ->whereIn('id', $customIds)
                    ->orderByRaw(DB::raw("FIELD(id, {$idsCsv})"))
                    ->get();
            } else {
                // AUTO: label PROMO
                $promoTours = TourPackage::query()
                    ->where('is_active', true)
                    ->where('label', 'PROMO')
                    ->latest()
                    ->get();
            }
        }

        return view('front.home', compact('packages', 'inspirations', 'clientLogos', 'promoTours'));
    }
}
