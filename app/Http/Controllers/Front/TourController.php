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
use App\Models\ShipPackage;
use Illuminate\Support\Collection;
use App\Models\Article;
class TourController extends Controller
{
    /**
     * Halaman list paket (homepage)
     */
   public function index(Request $request, ?string $categorySlug = null, ?string $subcategorySlug = null)
{
    $query = TourPackage::query()
        ->where('is_active', true)
        ->with(['category', 'tiers']);

    $search = $request->get('q');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('destination', 'like', "%{$search}%");
        });
    }

    // === NEW: kategori/subkategori via URL path (/paket-tour/{category}/{subcategory?}) ===
    $activeCategory = null;
    $activeSubcategory = null;

    if ($categorySlug) {
        $activeCategory = TourCategory::query()
            ->whereNull('parent_id')
            ->where('slug', $categorySlug)
            ->firstOrFail();

        $query->where('category_id', $activeCategory->id);
    }

    if ($subcategorySlug) {
        if (!$activeCategory) {
            abort(404);
        }

        $activeSubcategory = TourCategory::query()
            ->where('parent_id', $activeCategory->id)
            ->where('slug', $subcategorySlug)
            ->firstOrFail();

        $query->where('subcategory_id', $activeSubcategory->id);
    }

    // Backward-compat: masih terima filter lama via query string (ID), kalau route slug kosong.
    if (!$categorySlug && $request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if (!$subcategorySlug && $request->filled('subcategory')) {
        $query->where('subcategory_id', $request->subcategory);
    }

    $packages = $query->orderBy('title')->paginate(12)->appends($request->except('page'));

    $categories = TourCategory::with('children')
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get();

    $tourMainCategories = $categories;

    return view('front.tours.index', compact(
        'packages',
        'categories',
        'tourMainCategories',
        'activeCategory',
        'activeSubcategory'
    ));
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
            ->take(10)
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


$homeArticlesEnabled = (Setting::getValue('home_articles_enabled', '0') === '1');
$homeArticlesTitle = Setting::getValue('home_articles_title', 'Baca dan bangkitkan semangat liburanmu');
$homeArticlesDesc = Setting::getValue('home_articles_desc', '');
$homeArticlesButtonText = Setting::getValue('home_articles_button_text', 'Baca Artikel Inspirasi');
$homeArticlesButtonUrl = Setting::getValue('home_articles_button_url', route('articles'));
$homeArticlesMode = Setting::getValue('home_articles_mode', 'custom');

$homeArticles = collect();

if ($homeArticlesEnabled) {
    if ($homeArticlesMode === 'auto') {
        $homeArticles = Article::query()
            ->where('is_published', 1)
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();
    } else {
        $idsRaw = Setting::getValue('home_articles_custom_ids', '[]');
        $ids = json_decode($idsRaw, true);
        $ids = is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
        $ids = array_values(array_filter($ids, fn ($id) => $id > 0));
        $ids = array_slice($ids, 0, 12);

        if (count($ids) > 0) {
            $rows = Article::query()
                ->whereIn('id', $ids)
                ->where('is_published', 1)
                ->get()
                ->keyBy('id');

            $ordered = [];
            foreach ($ids as $id) {
                if (isset($rows[$id])) $ordered[] = $rows[$id];
            }
            $homeArticles = collect($ordered)->take(4);
        }
    }
}

        // ===================== PROMO SHIP PACKAGES (Home Section) =====================
$shipPromoEnabled = filter_var(Setting::getValue('home_ship_promo_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
$shipPromoMode = Setting::getValue('home_ship_promo_mode', 'auto'); // auto | custom

$promoShips = collect();

if ($shipPromoEnabled) {
    $customShipIds = json_decode(Setting::getValue('home_ship_promo_custom_ids', '[]'), true);
    $customShipIds = is_array($customShipIds) ? array_values(array_unique(array_map('intval', $customShipIds))) : [];

    if ($shipPromoMode === 'custom' && count($customShipIds) > 0) {
        // Ambil sesuai urutan admin (FIELD untuk MySQL)
        $idsCsv = implode(',', $customShipIds);

        $promoShips = ShipPackage::query()
            ->where('is_active', true)
            ->whereIn('id', $customShipIds)
            ->orderByRaw(DB::raw("FIELD(id, {$idsCsv})"))
            ->get();
    } else {
        // AUTO: label PROMO
        $promoShips = ShipPackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->latest()
            ->get();
    }
}

$homeDiscountBanners = \App\Models\HomePromoBanner::query()
    ->where('section', 'discount')
    ->where('is_active', 1)
    ->orderBy('sort_order')
    ->orderBy('id')
    ->get();

$homeMissionBanners = \App\Models\HomePromoBanner::query()
    ->where('section', 'missions')
    ->where('is_active', 1)
    ->orderBy('sort_order')
    ->orderBy('id')
    ->get();
$tourMainCategories = TourCategory::query()
    ->whereNull('parent_id')
    ->with('children')
    ->orderBy('name')
    ->get();

      return view('front.home', compact(
    'packages',
    'inspirations',
    'clientLogos',
    'promoTours',
    'promoShips',
    'homeDiscountBanners',
    'homeMissionBanners',
    'tourMainCategories'
))->with([
    'homeArticlesEnabled' => $homeArticlesEnabled,
    'homeArticlesTitle' => $homeArticlesTitle,
    'homeArticlesDesc' => $homeArticlesDesc,
    'homeArticlesButtonText' => $homeArticlesButtonText,
    'homeArticlesButtonUrl' => $homeArticlesButtonUrl,
    'homeArticlesMode' => $homeArticlesMode,
    'homeArticles' => $homeArticles,
]);

    }
}
