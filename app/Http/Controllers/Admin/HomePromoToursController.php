<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TourPackage;
use App\Models\ShipPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomePromoToursController extends Controller
{
    public function edit()
    {
        $settings = Setting::pluck('value', 'key');

        $promoCandidates = TourPackage::query()
            ->where('is_active', true)
            ->where('label', 'PROMO')
            ->orderBy('title')
            ->get(['id', 'title']);

        // parse selected ids
        $selectedIdsRaw = $settings['home_promo_custom_ids'] ?? '[]';
        $selectedIds = is_array($selectedIdsRaw) ? $selectedIdsRaw : json_decode($selectedIdsRaw, true);
        $selectedIds = is_array($selectedIds) ? array_values(array_unique(array_map('intval', $selectedIds))) : [];
$promoShipCandidates = ShipPackage::query()
    ->where('is_active', true)
    ->where('label', 'PROMO')
    ->orderBy('title')
    ->get(['id', 'title']);

// parse selected ship ids
$selectedShipIdsRaw = $settings['home_ship_promo_custom_ids'] ?? '[]';
$selectedShipIds = is_array($selectedShipIdsRaw) ? $selectedShipIdsRaw : json_decode($selectedShipIdsRaw, true);
$selectedShipIds = is_array($selectedShipIds) ? array_values(array_unique(array_map('intval', $selectedShipIds))) : [];

        return view('admin.home-sections.promo-tours', [
    'settings' => $settings,
    'promoCandidates' => $promoCandidates,
    'selectedIds' => $selectedIds,

    // NEW: ships
    'promoShipCandidates' => $promoShipCandidates,
    'selectedShipIds' => $selectedShipIds,
]);

    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'home_promo_enabled' => ['nullable', 'boolean'],
            'home_promo_badge'   => ['nullable', 'string', 'max:30'],
            'home_promo_title'   => ['nullable', 'string', 'max:140'],
            'home_promo_desc'    => ['nullable', 'string', 'max:240'],
            'home_promo_mode'    => ['nullable', Rule::in(['auto', 'custom'])],

            // FIX: jangan dibatasi 5, karena 5 itu hanya untuk tampilan per layar (CSS), bukan jumlah data
            'home_promo_custom_ids' => ['nullable', 'array'],
            'home_promo_custom_ids.*' => ['integer', 'exists:tour_packages,id'],
              'home_ship_promo_enabled' => ['nullable', 'boolean'],
    'home_ship_promo_badge'   => ['nullable', 'string', 'max:30'],
    'home_ship_promo_title'   => ['nullable', 'string', 'max:140'],
    'home_ship_promo_desc'    => ['nullable', 'string', 'max:240'],
    'home_ship_promo_mode'    => ['nullable', Rule::in(['auto', 'custom'])],
    'home_ship_promo_custom_ids' => ['nullable', 'array'],
    'home_ship_promo_custom_ids.*' => ['integer', 'exists:ship_packages,id'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'home_promo_enabled'],
            ['value' => $request->boolean('home_promo_enabled') ? '1' : '0']
        );

        Setting::updateOrCreate(['key' => 'home_promo_badge'], ['value' => $data['home_promo_badge'] ?? 'PROMO']);
        Setting::updateOrCreate(['key' => 'home_promo_title'], ['value' => $data['home_promo_title'] ?? 'Paket Tour Promo']);
        Setting::updateOrCreate(['key' => 'home_promo_desc'],  ['value' => $data['home_promo_desc'] ?? '']);
        Setting::updateOrCreate(['key' => 'home_promo_mode'],  ['value' => $data['home_promo_mode'] ?? 'auto']);

        $customIds = $data['home_promo_custom_ids'] ?? [];
        $customIds = array_values(array_unique(array_map('intval', $customIds)));

        Setting::updateOrCreate(
            ['key' => 'home_promo_custom_ids'],
            ['value' => json_encode($customIds)]
        );
// ===================== SAVE HOME SHIP PROMO (NEW) =====================
Setting::updateOrCreate(
    ['key' => 'home_ship_promo_enabled'],
    ['value' => $request->boolean('home_ship_promo_enabled') ? '1' : '0']
);

Setting::updateOrCreate(['key' => 'home_ship_promo_badge'], ['value' => $data['home_ship_promo_badge'] ?? 'PROMO KAPAL']);
Setting::updateOrCreate(['key' => 'home_ship_promo_title'], ['value' => $data['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo']);
Setting::updateOrCreate(['key' => 'home_ship_promo_desc'],  ['value' => $data['home_ship_promo_desc'] ?? '']);
Setting::updateOrCreate(['key' => 'home_ship_promo_mode'],  ['value' => $data['home_ship_promo_mode'] ?? 'auto']);

$shipCustomIds = $data['home_ship_promo_custom_ids'] ?? [];
$shipCustomIds = array_values(array_unique(array_map('intval', $shipCustomIds)));

Setting::updateOrCreate(
    ['key' => 'home_ship_promo_custom_ids'],
    ['value' => json_encode($shipCustomIds)]
);

        return redirect()
            ->route('admin.home-sections.promo-tours.edit')
            ->with('success', 'Pengaturan section Promo berhasil disimpan.');
    }
}
