<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\Setting; 
class DocumentationController extends Controller
{
    public function tour()
    {
        return $this->renderDocs('tour', 'Dokumentasi Paket Tour');
    }

    public function ship()
    {
        return $this->renderDocs('ship', 'Dokumentasi Sewa Kapal');
    }

    public function umrah()
    {
        return $this->renderDocs('umrah', 'Dokumentasi Umrah');
    }

    private function renderDocs(string $category, string $pageTitle)
    {
        $photos = Documentation::query()
            ->where('category', $category)
            ->where('type', 'photo')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $videos = Documentation::query()
            ->where('category', $category)
            ->where('type', 'video')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $heroBadge = Setting::getValue("docs_{$category}_hero_badge", Setting::getValue('docs_hero_badge', $pageTitle));
$heroTitle = Setting::getValue("docs_{$category}_hero_title", Setting::getValue('docs_hero_title', $pageTitle));
$heroDesc  = Setting::getValue(
    "docs_{$category}_hero_desc",
    Setting::getValue('docs_hero_desc', 'Galeri dokumentasi perjalanan dan aktivitas layanan kami, terdiri dari foto dan video.')
);

return view('front.pages.docs', compact('photos', 'videos', 'pageTitle', 'heroBadge', 'heroTitle', 'heroDesc'));
    }
}
