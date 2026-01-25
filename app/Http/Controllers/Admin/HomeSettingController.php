<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Article;


class HomeSettingController extends Controller
{
    public function edit()
    {
        $raw = Setting::getValue('home_tabs', null);
        $tabs = is_string($raw) ? json_decode($raw, true) : null;
        $tabs = is_array($tabs) ? $tabs : [];

        // Normalize like frontend expects
        $normalized = [];
        foreach ($tabs as $t) {
            if (!is_array($t)) continue;
            $label = trim((string)($t['label'] ?? ''));
            $url   = trim((string)($t['url'] ?? ''));
            $icon  = trim((string)($t['icon'] ?? ''));
            if ($label === '' && $url === '' && $icon === '') continue;
            $normalized[] = [
                'label' => $label,
                'url'   => $url,
                'icon'  => $icon,
            ];
        }

        // If empty, show defaults (same as AppServiceProvider fallback)
        if (count($normalized) === 0) {
            $normalized = [
                ['label' => 'To Do',            'icon' => 'clipboard-check', 'url' => '/to-do'],
                ['label' => 'Jemputan Bandara', 'icon' => 'plane',           'url' => '/airport-transfers'],
                ['label' => 'Ferry',            'icon' => 'ship',            'url' => '/ferry'],
                ['label' => 'Travel',           'icon' => 'bus',             'url' => '/travel'],
                ['label' => 'Sewa Mobil',       'icon' => 'car',             'url' => route('rentcar.index')],
            ];
        }
// ===== HOME ARTICLES SETTINGS (WAJIB ADA SEBELUM return view) =====
$articlesEnabled = (Setting::getValue('home_articles_enabled', '0') === '1');
$articlesTitle = Setting::getValue('home_articles_title', 'Baca dan bangkitkan semangat liburanmu');
$articlesDesc = Setting::getValue('home_articles_desc', '');
$articlesButtonText = Setting::getValue('home_articles_button_text', 'Baca Artikel Inspirasi');
$articlesButtonUrl = Setting::getValue('home_articles_button_url', '/artikel');
$articlesMode = Setting::getValue('home_articles_mode', 'custom');

$customIdsRaw = Setting::getValue('home_articles_custom_ids', '[]');
$customIds = json_decode($customIdsRaw, true);
$customIds = is_array($customIds) ? array_values(array_unique(array_map('intval', $customIds))) : [];
$customIds = array_values(array_filter($customIds, fn($id) => $id > 0));
$customIds = array_slice($customIds, 0, 12);

$selectedArticles = collect();
if (count($customIds) > 0) {
    $rows = Article::query()
        ->whereIn('id', $customIds)
        ->get(['id','title','slug','cover_image','is_published','published_at'])
        ->keyBy('id');

    $ordered = [];
    foreach ($customIds as $id) {
        if (isset($rows[$id])) $ordered[] = $rows[$id];
    }
    $selectedArticles = collect($ordered);
}
// ===== END HOME ARTICLES SETTINGS =====

        return view('admin.settings.home', [
    'tabs' => $normalized,
    'articlesSettings' => [
        'enabled' => $articlesEnabled,
        'title'   => $articlesTitle,
        'desc'    => $articlesDesc,
        'button_text' => $articlesButtonText,
        'button_url'  => $articlesButtonUrl,
        'mode'    => $articlesMode,
        'custom_ids' => $customIds,
    ],
    'selectedArticles' => $selectedArticles,
]);

    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tabs' => ['nullable', 'array'],
            'tabs.*.label' => ['nullable', 'string', 'max:40'],
            'tabs.*.icon'  => ['nullable', 'string', 'max:40'],
            'tabs.*.url'   => ['nullable', 'string', 'max:255'],
            'home_articles_enabled' => ['nullable', 'boolean'],
'home_articles_title' => ['nullable', 'string', 'max:80'],
'home_articles_desc' => ['nullable', 'string', 'max:160'],
'home_articles_button_text' => ['nullable', 'string', 'max:40'],
'home_articles_button_url' => ['nullable', 'string', 'max:255'],
'home_articles_mode' => ['nullable', 'in:auto,custom'],
'home_articles_custom_ids' => ['nullable', 'string'],
 'tabs.*.icon_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    'tabs.*.icon_image_existing' => ['nullable', 'string', 'max:255'],
    'home_discount_banner_title' => ['nullable', 'string', 'max:120'],
'home_mission_banner_title'  => ['nullable', 'string', 'max:120'],


        ]);

        $tabs = $validated['tabs'] ?? [];

       $clean = [];

foreach ($tabs as $idx => $t) {
    $label = trim((string)($t['label'] ?? ''));
    $url   = trim((string)($t['url'] ?? ''));
    $icon  = trim((string)($t['icon'] ?? ''));

    if ($label === '' || $url === '') {
        continue;
    }

    // default: keep existing icon image kalau ada
    $iconImage = trim((string)($t['icon_image_existing'] ?? ''));

    // kalau ada upload baru, replace
    if ($request->hasFile("tabs.$idx.icon_image")) {
        $path = $request->file("tabs.$idx.icon_image")->store('home-tabs', 'public');
        $iconImage = $path; // simpan relatif, contoh: home-tabs/xxxx.webp
    }

    $clean[] = [
        'label' => $label,
        'url'   => $url,
        'icon'  => $icon !== '' ? $icon : 'sparkles',
        'icon_image' => $iconImage,
    ];
}

Setting::updateOrCreate(
    ['key' => 'home_tabs'],
    ['value' => json_encode($clean, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]
);
$enabled = (bool)($validated['home_articles_enabled'] ?? false);
$title = trim((string)($validated['home_articles_title'] ?? ''));
$desc = trim((string)($validated['home_articles_desc'] ?? ''));
$buttonText = trim((string)($validated['home_articles_button_text'] ?? ''));
$buttonUrl = trim((string)($validated['home_articles_button_url'] ?? ''));
$mode = (string)($validated['home_articles_mode'] ?? 'custom');

$raw = (string)($validated['home_articles_custom_ids'] ?? '[]');
$ids = json_decode($raw, true);
$ids = is_array($ids) ? $ids : [];
$ids = array_values(array_unique(array_map('intval', $ids)));
$ids = array_values(array_filter($ids, fn($id) => $id > 0));
$ids = array_slice($ids, 0, 12);

if (count($ids) > 0) {
    $exists = Article::query()->whereIn('id', $ids)->pluck('id')->all();
    $exists = array_map('intval', $exists);
    $set = array_flip($exists);

    $filtered = [];
    foreach ($ids as $id) {
        if (isset($set[$id])) $filtered[] = $id;
    }
    $ids = $filtered;
}


Setting::updateOrCreate(['key' => 'home_articles_enabled'], ['value' => $enabled ? '1' : '0']);
Setting::updateOrCreate(['key' => 'home_articles_title'], ['value' => $title]);
Setting::updateOrCreate(['key' => 'home_articles_desc'], ['value' => $desc]);
Setting::updateOrCreate(['key' => 'home_articles_button_text'], ['value' => $buttonText]);
Setting::updateOrCreate(['key' => 'home_articles_button_url'], ['value' => $buttonUrl]);
Setting::updateOrCreate(['key' => 'home_articles_mode'], ['value' => $mode]);
Setting::updateOrCreate(
    ['key' => 'home_articles_custom_ids'],
    ['value' => json_encode($ids, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]
);
$discountTitle = trim((string)($validated['home_discount_banner_title'] ?? ''));
$missionTitle  = trim((string)($validated['home_mission_banner_title'] ?? ''));

Setting::updateOrCreate(['key' => 'home_discount_banner_title'], ['value' => $discountTitle]);
Setting::updateOrCreate(['key' => 'home_mission_banner_title'],  ['value' => $missionTitle]);

        return back()->with('success', 'Home tabs berhasil disimpan.');
    }

    public function searchArticles(Request $request)
{
    $q = trim((string)$request->get('q', ''));

    if (mb_strlen($q) < 2) {
        return response()->json(['data' => []]);
    }

    $items = Article::query()
        ->where(function ($w) use ($q) {
            $w->where('title', 'like', "%{$q}%")
              ->orWhere('slug', 'like', "%{$q}%");
        })
        ->orderByDesc('published_at')
        ->limit(12)
        ->get(['id','title','slug','cover_image','is_published','published_at']);

    return response()->json(['data' => $items]);
}

}
