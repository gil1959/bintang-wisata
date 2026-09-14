@extends('layouts.front')

@section('meta')
    @php
        $pkgOrArticle = isset($article) ? $article : ($package ?? null);
        $mTitle = $pkgOrArticle->seo_title ?? $pkgOrArticle->title ?? 'Bintang Wisata Holiday';
        $mDesc = $pkgOrArticle->seo_description ?? $pkgOrArticle->short_description ?? $pkgOrArticle->excerpt ?? 'Liburan impian jadi nyata dengan pelayanan bintang lima.';
        $mKey = $pkgOrArticle->seo_keywords ?? 'restoran, kuliner, paket tour, paket wisata, bintang wisata holiday';
        $mImage = !empty($pkgOrArticle->seo_image_path) ? asset('storage/' . $pkgOrArticle->seo_image_path) : (!empty($package->thumbnail_path) ? asset('storage/' . $package->thumbnail_path) : asset('logo-atau-banner.jpg'));
        $sTitle = $pkgOrArticle->social_title ?? $mTitle;
        $sDesc = $pkgOrArticle->social_description ?? $mDesc;
    @endphp
    <title>{{ $mTitle }} | Bintang Wisata Holiday</title>
    <meta name="description" content="{{ $mDesc }}">
    <meta name="keywords" content="{{ $mKey }}">
    <meta name="author" content="Bintang Wisata Holiday">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $sTitle }}">
    <meta property="og:description" content="{{ $sDesc }}">
    <meta property="og:image" content="{{ $mImage }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $sTitle }}">
    <meta property="twitter:description" content="{{ $sDesc }}">
    <meta property="twitter:image" content="{{ $mImage }}">
@endsection

@push('styles')
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f8fb;
        }
    </style>
@endpush

@php
$isEn = app()->getLocale() === 'en';

$title = $isEn ? ($package->title_en ?: $package->title) : $package->title;
$seoTitle = $isEn ? ($package->seo_title_en ?: $package->seo_title ?: $title) : ($package->seo_title ?: $title);
$descHtml = $isEn ? ($package->long_description_en ?: $package->long_description) : $package->long_description;
$address = $isEn ? ($package->address_en ?: $package->address) : $package->address;
$note = $isEn ? ($package->note_en ?: $package->note) : $package->note;

$nearbyPlaces = $package->nearby_places;
if (!is_array($nearbyPlaces)) $nearbyPlaces = [];

$facilities = $isEn ? ($package->facilities_en ?: $package->facilities) : $package->facilities;
if (!is_array($facilities)) $facilities = [];

$keunggulan = $isEn ? ($package->keunggulan_en ?: $package->keunggulan) : $package->keunggulan;
if (!is_array($keunggulan)) $keunggulan = [];

// 100% REAL PHOTOS ONLY (No placeholder URLs, no unsplash fallbacks)
$realPhotos = [];
if (!empty($package->thumbnail_path)) {
    $realPhotos[] = [
        'src' => asset('storage/' . $package->thumbnail_path),
        'alt' => $title . ' - Thumbnail',
    ];
}
if ($package->photos) {
    foreach ($package->photos as $p) {
        if (!empty($p->file_path)) {
            $realPhotos[] = [
                'src' => asset('storage/' . $p->file_path),
                'alt' => $title . ' - Galeri',
            ];
        }
    }
}

// 100% REAL REVIEWS ONLY (No fake ratings, no fake count)
$approvedReviews = $package->reviews()->approved()->latest()->get();
$reviewCount = $approvedReviews->count();
$avg = $reviewCount > 0 ? round((float) $approvedReviews->avg('rating'), 1) : null;
@endphp

@section('title', $seoTitle)

@section('content')
<!-- BEGIN: MainContentContainer -->
<main class="max-w-[1280px] mx-auto px-4 py-6 space-y-6">

    <!-- BEGIN: TopImageGallery (Dynamic Real Photos Only) -->
    @if(count($realPhotos) === 0)
        <section class="w-full h-64 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col items-center justify-center text-slate-400">
            <i class="fa-solid fa-image text-4xl text-slate-300 mb-2"></i>
            <span class="text-xs font-semibold">Foto galeri belum diunggah</span>
        </section>
    @elseif(count($realPhotos) === 1)
        <section class="w-full h-[380px] md:h-[460px] rounded-2xl overflow-hidden bg-white shadow-sm cursor-pointer group" onclick="openLightbox(0)">
            <img alt="{{ $realPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[0]['src'] }}">
        </section>
    @elseif(count($realPhotos) === 2)
        <section class="w-full rounded-2xl overflow-hidden bg-white shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-1.5 h-[380px] md:h-[460px]">
                <div class="h-full relative overflow-hidden group cursor-pointer" onclick="openLightbox(0)">
                    <img alt="{{ $realPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[0]['src'] }}">
                </div>
                <div class="h-full relative overflow-hidden group cursor-pointer" onclick="openLightbox(1)">
                    <img alt="{{ $realPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[1]['src'] }}">
                </div>
            </div>
        </section>
    @elseif(count($realPhotos) === 3)
        <section class="w-full rounded-2xl overflow-hidden bg-white shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-1.5 h-[420px] md:h-[480px]">
                <div class="md:col-span-7 h-full relative overflow-hidden group cursor-pointer" onclick="openLightbox(0)">
                    <img alt="{{ $realPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[0]['src'] }}">
                </div>
                <div class="md:col-span-5 grid grid-rows-2 gap-1.5 h-full">
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(1)">
                        <img alt="{{ $realPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[1]['src'] }}">
                    </div>
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(2)">
                        <img alt="{{ $realPhotos[2]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[2]['src'] }}">
                    </div>
                </div>
            </div>
        </section>
    @elseif(count($realPhotos) === 4)
        <section class="w-full rounded-2xl overflow-hidden bg-white shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-1.5 h-[420px] md:h-[480px]">
                <div class="md:col-span-6 h-full relative overflow-hidden group cursor-pointer" onclick="openLightbox(0)">
                    <img alt="{{ $realPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[0]['src'] }}">
                </div>
                <div class="md:col-span-6 grid grid-cols-2 grid-rows-2 gap-1.5 h-full">
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(1)">
                        <img alt="{{ $realPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[1]['src'] }}">
                    </div>
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(2)">
                        <img alt="{{ $realPhotos[2]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[2]['src'] }}">
                    </div>
                    <div class="col-span-2 relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(3)">
                        <img alt="{{ $realPhotos[3]['alt'] }}" class="w-full h-full object-cover filter brightness-75 group-hover:scale-105 transition duration-500" src="{{ $realPhotos[3]['src'] }}">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center gap-2 text-white font-medium text-xs sm:text-sm">
                            <i class="fa-regular fa-images"></i>
                            <span>Lihat Semua Foto (4)</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        {{-- 5 or more photos: Full Stitch Grid with +X overlay on the last slot --}}
        <section class="w-full rounded-2xl overflow-hidden bg-white shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-1.5 h-[420px] md:h-[480px]">
                <div class="md:col-span-5 h-full relative overflow-hidden group cursor-pointer" onclick="openLightbox(0)">
                    <img alt="{{ $realPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[0]['src'] }}">
                </div>

                <div class="md:col-span-3 grid grid-rows-2 gap-1.5 h-full">
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(1)">
                        <img alt="{{ $realPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[1]['src'] }}">
                    </div>
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(2)">
                        <img alt="{{ $realPhotos[2]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[2]['src'] }}">
                    </div>
                </div>

                <div class="md:col-span-4 grid grid-cols-2 grid-rows-2 gap-1.5 h-full">
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(3)">
                        <img alt="{{ $realPhotos[3]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[3]['src'] }}">
                    </div>
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(4)">
                        <img alt="{{ $realPhotos[4]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[4]['src'] }}">
                    </div>
                    @if(isset($realPhotos[5]))
                    <div class="relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox(5)">
                        <img alt="{{ $realPhotos[5]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $realPhotos[5]['src'] }}">
                    </div>
                    @endif
                    @php $lastSlotIdx = isset($realPhotos[6]) ? 6 : (isset($realPhotos[5]) ? 5 : 4); @endphp
                    <div class="{{ !isset($realPhotos[5]) ? 'col-span-2' : '' }} relative overflow-hidden group h-full cursor-pointer" onclick="openLightbox({{ $lastSlotIdx }})">
                        <img alt="{{ $realPhotos[$lastSlotIdx]['alt'] }}" class="w-full h-full object-cover filter brightness-75 group-hover:scale-105 transition duration-500" src="{{ $realPhotos[$lastSlotIdx]['src'] }}">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center gap-2 text-white font-medium text-xs sm:text-sm">
                            <i class="fa-regular fa-images"></i>
                            <span>Lihat Semua Foto ({{ count($realPhotos) }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- END: TopImageGallery -->

    <!-- BEGIN: HeaderAndDetails -->
    @php
        $defaultNearby = [
            ['name' => 'Taman Kelinci Ciwidey', 'distance' => '1.27 km'],
            ['name' => 'Desa Wisata Lebakmuncang', 'distance' => '452 m'],
            ['name' => 'Taman Kelinci Ciwidey', 'distance' => '1.27 km'],
        ];
        $nearbyList = !empty($nearbyPlaces) && count($nearbyPlaces) > 0 ? $nearbyPlaces : $defaultNearby;

        $defaultFacilities = ['Restoran', 'Parkir', 'WiFi', 'Musholla'];
        $facilityList = !empty($facilities) && count($facilities) > 0 ? $facilities : $defaultFacilities;

        $defaultKeunggulan = [
            'Harga terbaik di kelasnya',
            'Kualitas layanan terjamin',
            'Bahan baku pilihan segar setiap hari'
        ];
        $keunggulanList = !empty($keunggulan) && count($keunggulan) > 0 ? $keunggulan : $defaultKeunggulan;

        $addressText = !empty($address) ? $address : 'Jl. Babakan lampit, rt 01 rw 09, desa panundaan kecamatan ciwidey, Ciwidey, Bandung, Jawa Barat, ...';
        $mapsUrl = !empty($package->maps_url) ? $package->maps_url : ('https://maps.google.com/?q=' . urlencode($title . ' ' . $addressText));

        $noteText = !empty($note) ? $note : 'Untuk Informasi anda bisa menghubungi kontak Bintang Wisata.';

        $hasRealReviews = $reviewCount > 0;
        $ratingScore = $hasRealReviews ? number_format($avg > 5 ? $avg : $avg * 2, 1, ',', '.') : '-';
        $ratingCount = $hasRealReviews ? $reviewCount : 0;
        $ratingLabel = $hasRealReviews 
            ? (($avg * 2) >= 8.5 ? 'Mengesankan' : (($avg * 2) >= 7.5 ? 'Sangat Baik' : (($avg * 2) >= 6.0 ? 'Baik' : 'Cukup Baik'))) 
            : 'Belum Ada Ulasan';

        $rawCs = !empty($package->cs_contact) ? $package->cs_contact : ($siteSettings['whatsapp_number'] ?? '6285709271847');
        $waPhone = preg_replace('/[^0-9]/', '', $rawCs);
        if (str_starts_with($waPhone, '0')) {
            $waPhone = '62' . substr($waPhone, 1);
        }
        $csWhatsappUrl = "https://wa.me/{$waPhone}?text=Halo%20Admin,%20saya%20ingin%20tanya%20tentang%20resto%20" . urlencode($title);
    @endphp

    <section class="bg-white rounded-2xl p-6 md:p-7 shadow-sm space-y-5" data-purpose="resort-overview">
        <!-- Top Title and Action Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $title }}</h1>
                <div class="flex items-center gap-2.5 mt-2">
                    <span style="background-color: #e0f2fe; color: #0284c7;" class="px-2.5 py-0.5 rounded text-xs font-bold">Resto</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                        <!-- Golden Shield with Star / Checkmark -->
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="#fbbf24" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L4 5v6.5C4 16.5 7.5 21.2 12 22.5c4.5-1.3 8-6 8-11V5l-8-3z"/>
                            <path d="M9 12l2 2 4-4" stroke="#92400e" stroke-width="2"/>
                        </svg>
                        Preferred Partner <span class="font-bold" style="color: #f59e0b;">Plus</span>
                    </span>
                    @if(!empty($package->label))
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                        {{ $package->label }}
                    </span>
                    @endif
                </div>
            </div>

            <button id="btnHeaderReservasi" 
                    style="background-color: #0088f8 !important; color: #ffffff !important; border: none !important;" 
                    class="px-8 py-2.5 hover:opacity-90 active:scale-95 text-white font-bold text-sm rounded-full shadow-md transition cursor-pointer" 
                    type="button">
                Reservasi
            </button>
        </div>

        <!-- Info Alert Banner (Jangan Lewatkan...) -->
        <div style="background-color: #def0fc; border-radius: 1rem; padding: 0.75rem 1.25rem; display: flex; align-items: center; gap: 18px;" class="flex items-center">
            <!-- Navy Circle with Gold Stopwatch Icon -->
            <div style="width: 38px; height: 38px; background-color: #0353a4; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.12);">
                <svg class="w-5 h-5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="#facc15" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="13" r="8"/>
                    <path d="M12 9v4l2.5 1.5"/>
                    <path d="M12 5V2"/>
                    <path d="M10 2h4"/>
                    <path d="M18.5 7.5l1.5-1.5"/>
                    <path d="M5.5 7.5L4 6"/>
                </svg>
            </div>
            <span class="text-xs md:text-sm font-semibold text-slate-800 tracking-tight" style="margin-left: 6px;">Jangan Lewatkan Menu Resto Untuk Harga Paling Murah</span>
        </div>

        <!-- Overview Cards Grid (3 Columns like Design) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-stretch">
            <!-- 1. Left Column: Rating Card (Real Database Review Data) -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between relative overflow-hidden h-full min-h-[260px]">
                <!-- Decorative Top-Right Wave Graphic -->
                <div class="absolute top-0 right-0 w-48 h-32 pointer-events-none opacity-40">
                    <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <path d="M0 0C70 45 140 20 200 80V0H0Z" fill="#bae6fd" fill-opacity="0.6"/>
                        <path d="M40 0C90 30 150 15 200 55V0H40Z" fill="#38bdf8" fill-opacity="0.4"/>
                    </svg>
                </div>

                <div class="z-10">
                    @if($hasRealReviews)
                    <div class="flex items-center gap-3">
                        <div class="flex items-baseline">
                            <span class="text-3xl md:text-4xl font-extrabold tracking-tight" style="color: #0088f8;">{{ $ratingScore }}</span>
                            <span class="text-base md:text-lg font-bold" style="color: #0088f8;">/10</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 text-sm md:text-base leading-snug">{{ $ratingLabel }}</span>
                            <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1 mt-0.5" style="color: #0088f8;" href="#customer-reviews">
                                {{ $ratingCount }} ulasan 
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center gap-3">
                        <div class="flex items-baseline">
                            <span class="text-3xl md:text-4xl font-extrabold tracking-tight text-slate-400">-</span>
                            <span class="text-base md:text-lg font-bold text-slate-400">/5</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 text-sm md:text-base leading-snug">Belum Ada Ulasan</span>
                            <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1 mt-0.5" style="color: #0088f8;" href="#customer-reviews">
                                Tulis ulasan pertama
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Spacer to maintain generous height matching adjacent columns -->
                <div class="hidden lg:block h-28"></div>
            </div>

            <!-- 2. Middle Column: Area Akomodasi & Keunggulan Resto -->
            <div class="flex flex-col gap-4 h-full">
                <!-- Area Akomodasi Card with Organic Top-Right Map Contour Watermark -->
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] relative overflow-hidden flex flex-col justify-between flex-1">
                    <!-- Top-Right Organic Map Contour Watermark matching reference -->
                    <div class="absolute top-0 right-0 w-44 sm:w-56 h-full pointer-events-none overflow-hidden opacity-90" aria-hidden="true">
                        <svg class="w-full h-full object-cover" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Faint Organic Blue River/Canal Curve -->
                            <path d="M100 0 C 120 40, 110 80, 135 120 S 185 170, 220 190" stroke="#bae6fd" stroke-width="2.2" stroke-linecap="round" fill="none" opacity="0.7"/>
                            
                            <!-- Organic Rounded Land Blocks / Terrain Parcels -->
                            <path d="M135 10 C 160 5, 195 15, 210 35 C 215 50, 200 70, 180 75 C 155 80, 130 55, 130 35 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.2"/>
                            <path d="M155 20 C 170 18, 190 25, 200 38 C 205 48, 190 60, 178 65 C 162 67, 148 52, 152 35 Z" fill="#ffffff" stroke="#f1f5f9" stroke-width="1"/>
                            
                            <path d="M145 90 C 175 85, 210 100, 215 130 C 210 155, 180 165, 155 160 C 130 155, 125 125, 135 100 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.2"/>
                            <path d="M160 105 C 180 100, 200 110, 205 130 C 200 145, 180 150, 165 145 C 145 140, 145 120, 155 110 Z" fill="#ffffff" stroke="#f1f5f9" stroke-width="1"/>

                            <path d="M170 175 C 195 170, 215 185, 220 215 L 155 215 C 155 195, 160 180, 170 175 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.2"/>

                            <!-- Organic Contour Roads / Paths -->
                            <path d="M115 0 C 135 45, 120 85, 145 125 S 195 175, 220 195" stroke="#e2e8f0" stroke-width="1.4" stroke-linecap="round"/>
                            <path d="M128 65 C 155 70, 180 75, 220 70" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                            <path d="M145 150 C 170 155, 190 155, 220 145" stroke="#cbd5e1" stroke-width="1.2" stroke-linecap="round"/>
                            <path d="M85 20 C 105 15, 120 35, 130 55" stroke="#f1f5f9" stroke-width="1.2"/>
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm md:text-[15px]">Area Akomodasi</h3>
                            <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1" style="color: #0088f8;" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">
                                Lihat Peta 
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                        <div class="mt-2.5 flex items-start gap-2 text-xs text-slate-700">
                            <!-- Location Pin Icon -->
                            <svg class="w-3.5 h-3.5 text-slate-500 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <p class="leading-relaxed line-clamp-2 text-slate-700 font-normal">
                                {{ $addressText }}
                            </p>
                        </div>

                        <!-- Pill Badge (Dekat tempat rekreasi) -->
                        <div class="mt-2.5">
                            <span style="background-color: #eaf4fd; color: #0088f8;" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded font-semibold text-[11px]">
                                <!-- Ticket / Recreation Pass Icon -->
                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22 10V6c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2v4c1.1 0 1.99.9 1.99 2s-.89 2-2 2v4c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-4c-1.1 0-2-.9-2-2s.9-2 2-2zm-9 7.5h-2v-2h2v2zm0-4.5h-2v-2h2v2zm0-4.5h-2v-2h2v2z"/>
                                </svg>
                                Dekat tempat rekreasi
                            </span>
                        </div>

                        <!-- Nearby Places List -->
                        <div class="mt-3 space-y-2">
                            @foreach(array_slice($nearbyList, 0, 3) as $place)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2 min-w-0 pr-2">
                                    <svg class="w-3.5 h-3.5 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                    <span class="font-medium text-slate-800 truncate">{{ $place['name'] ?? '' }}</span>
                                </div>
                                <span class="text-slate-400 font-normal flex-shrink-0">{{ $place['distance'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Keunggulan Resto Card -->
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] relative overflow-hidden flex flex-col justify-between flex-1">
                    <!-- Bottom-right wave -->
                    <div class="absolute -bottom-1 -right-1 w-32 h-16 pointer-events-none opacity-30 overflow-hidden">
                        <svg viewBox="0 0 160 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            <path d="M0 80C50 60 100 75 160 30V80H0Z" fill="#bae6fd" fill-opacity="0.7"/>
                            <path d="M40 80C80 50 120 65 160 15V80H40Z" fill="#38bdf8" fill-opacity="0.5"/>
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm md:text-[15px]">Keunggulan <span class="font-normal text-slate-600 text-xs md:text-sm">Resto</span></h3>
                            <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer" style="color: #0088f8;" href="javascript:void(0)" onclick="openKeunggulanModal()">
                                Selengkapnya 
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                        <div class="bg-[#f0f4f8] rounded-lg p-2.5 text-xs text-slate-600 leading-relaxed mt-2.5">
                            Sebagai Preferred Partner Bintang Wisata selalu menjaga performa terbaik yang secara rutin diverifikasi Bintang Wisata.
                        </div>
                        <div class="mt-3 space-y-2 text-xs">
                            @foreach(array_slice($keunggulanList, 0, 2) as $point)
                            <div class="flex items-center gap-2 font-medium text-slate-700">
                                <!-- Golden Shield Icon -->
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="#fbbf24" stroke="#d97706" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L4 5v6.5C4 16.5 7.5 21.2 12 22.5c4.5-1.3 8-6 8-11V5l-8-3z"/>
                                    <path d="M9 12l2 2 4-4" stroke="#92400e" stroke-width="2"/>
                                </svg>
                                <span>{{ $point }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Right Column: Fasilitas Utama & NOTE -->
            <div class="flex flex-col gap-4 h-full">
                <!-- Fasilitas Utama Card -->
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] relative overflow-hidden flex flex-col justify-between flex-1">
                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm md:text-[15px]">Fasilitas Utama</h3>
                            <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer" style="color: #0088f8;" href="javascript:void(0)" onclick="openFasilitasModal()">
                                Selengkapnya 
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                        <div class="mt-6 space-y-5 text-xs md:text-sm text-slate-700">
                            @php
                                $displayedFacilities = array_slice($facilityList, 0, 3);
                            @endphp
                            @foreach($displayedFacilities as $facility)
                                @php
                                    $fLower = strtolower(trim($facility));
                                @endphp
                                <div class="flex items-center gap-3.5 py-0.5">
                                    @if(str_contains($fLower, 'parkir') || str_contains($fLower, 'parking'))
                                        <div style="width: 20px; height: 20px; border-radius: 4px; border: 1.8px solid #0088f8; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: #0088f8; font-weight: 800; font-size: 11px; line-height: 1;">
                                            P
                                        </div>
                                    @elseif(str_contains($fLower, 'resto') || str_contains($fLower, 'makan') || str_contains($fLower, 'kuliner'))
                                        <!-- Cutlery Fork & Knife SVG in #0088f8 -->
                                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#0088f8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 2v6a3 3 0 0 1-3 3 3 3 0 0 1-3-3V2"/>
                                            <path d="M15 2v19"/>
                                            <path d="M5 2v7a3 3 0 0 0 3 3 3 3 0 0 0 3-3V2"/>
                                            <path d="M8 12v9"/>
                                        </svg>
                                    @elseif(str_contains($fLower, 'wifi') || str_contains($fLower, 'internet'))
                                        <!-- WiFi Signal Waves SVG in #0088f8 -->
                                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#0088f8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                                            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                                            <circle cx="12" cy="20" r="1.2" fill="#0088f8"/>
                                        </svg>
                                    @elseif(str_contains($fLower, 'musholla') || str_contains($fLower, 'masjid') || str_contains($fLower, 'ibadah'))
                                        <svg class="w-5 h-5 flex-shrink-0 text-[#0088f8]" viewBox="0 0 24 24" fill="none" stroke="#0088f8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L12 22l7.03-4.39C20.26 16.07 21 14.12 21 12c0-4.97-4.03-9-9-9z"/>
                                            <path d="M12 7v5l3 3"/>
                                        </svg>
                                    @else
                                        <!-- Clean Checkmark Icon -->
                                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="#0088f8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M9 12l2 2 4-4"/>
                                        </svg>
                                    @endif
                                    <span class="font-medium text-slate-800">{{ $facility }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- NOTE Card -->
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col justify-between flex-1 min-h-[140px]">
                    <div>
                        <span class="text-xs font-bold text-slate-800 tracking-wider">NOTE:</span>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            {{ $noteText }}
                        </p>
                    </div>
                    <div class="mt-4">
                        <a class="text-xs font-semibold hover:underline inline-flex items-center gap-1" style="color: #0088f8;" href="{{ $csWhatsappUrl }}" target="_blank">
                            Selengkapnya 
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: HeaderAndDetails -->

    <!-- BEGIN: SubHeadingSection -->
    <div class="pt-6 pb-2" data-purpose="sub-heading">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $title }}</h2>
        <h3 class="text-xl font-bold text-slate-800 mt-0.5">Detail: Menu Resto</h3>
    </div>
    <!-- END: SubHeadingSection -->

    <!-- BEGIN: FiturPaketTable (Real Menus Only) -->
    <section id="daftar-menu-resto" class="bg-white rounded-2xl p-6 shadow-sm space-y-4" data-purpose="fitur-paket">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h3 class="font-bold text-slate-800 text-base">Fitur Paket</h3>
            <span class="text-xs text-slate-500 font-medium">Tarif dasar per pax: <strong class="text-sky-600">Rp{{ number_format($package->price_per_pax, 0, ',', '.') }}</strong></span>
        </div>

        @if($package->menus && $package->menus->count() > 0)
        <!-- Table Column Headers -->
        <div class="hidden md:grid grid-cols-12 text-xs font-semibold text-slate-400 px-4 py-2 border-b border-slate-100">
            <div class="col-span-3">Foto Menu</div>
            <div class="col-span-4">Menu &amp; Deskripsi</div>
            <div class="col-span-2">Kategori</div>
            <div class="col-span-1 text-center">Status</div>
            <div class="col-span-2 text-right">Aksi</div>
        </div>

        <div class="space-y-3">
            @foreach($package->menus as $menu)
            @php
                $menuPhotos = $menu->all_photos;
                $menuPhotosFormatted = array_map(function($path) use ($menu) {
                    return [
                        'src' => asset('storage/' . $path),
                        'alt' => $menu->name
                    ];
                }, $menuPhotos);
            @endphp
            <div class="border border-slate-100 rounded-xl p-3 md:p-4 hover:shadow-sm transition bg-white flex flex-col md:grid md:grid-cols-12 items-center gap-4">
                {{-- Foto / Auto Slider --}}
                <div class="col-span-3 w-full md:w-auto">
                    @if(count($menuPhotosFormatted) > 0)
                        <div x-data="{
                            active: 0,
                            photos: {{ json_encode($menuPhotosFormatted) }},
                            timer: null,
                            startAuto() {
                                if (this.photos.length > 1) {
                                    this.timer = setInterval(() => {
                                        this.active = (this.active + 1) % this.photos.length;
                                    }, 3500);
                                }
                            },
                            stopAuto() {
                                if (this.timer) clearInterval(this.timer);
                            }
                        }"
                        x-init="startAuto()"
                        @mouseenter="stopAuto()"
                        @mouseleave="startAuto()"
                        class="relative w-full md:w-36 h-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 group shadow-xs">
                            {{-- Slides --}}
                            <template x-for="(photo, pIdx) in photos" :key="pIdx">
                                <img :src="photo.src" 
                                     :alt="photo.alt" 
                                     x-show="active === pIdx"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="w-full h-full object-cover cursor-pointer"
                                     @click="openLightbox(active, photos)">
                            </template>

                            {{-- Click to preview hover overlay --}}
                            <button type="button" 
                                    @click="openLightbox(active, photos)"
                                    title="Klik untuk preview foto"
                                    class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white cursor-pointer">
                                <span class="bg-black/60 backdrop-blur-xs text-white text-[10px] px-2.5 py-1 rounded-full flex items-center gap-1 font-semibold shadow">
                                    <i class="fa-solid fa-magnifying-glass-plus text-[10px]"></i> Preview
                                </span>
                            </button>

                            @if(count($menuPhotosFormatted) > 1)
                                {{-- Arrow Controls --}}
                                <button type="button" 
                                        @click.stop="active = (active === 0 ? photos.length - 1 : active - 1)" 
                                        class="absolute left-1 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[9px] cursor-pointer">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button type="button" 
                                        @click.stop="active = (active + 1) % photos.length" 
                                        class="absolute right-1 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[9px] cursor-pointer">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>

                                {{-- Photo Count Pill --}}
                                <div class="absolute bottom-1 right-1 bg-black/60 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md flex items-center gap-1 pointer-events-none">
                                    <i class="fa-solid fa-camera text-[8px]"></i>
                                    <span x-text="(active + 1) + '/' + photos.length"></span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full md:w-36 h-24 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-utensils text-slate-300 text-lg"></i>
                        </div>
                    @endif
                </div>

                {{-- Judul, Harga & Deskripsi --}}
                <div class="col-span-4 w-full">
                    <h4 class="font-bold text-slate-800 text-sm md:text-base">{{ $menu->name }}</h4>
                    <p class="text-xs text-sky-600 font-bold mt-0.5">Rp. {{ number_format($menu->price, 0, ',', '.') }} <span class="text-slate-400 font-normal">/ porsi</span></p>
                    @if(!empty($menu->description))
                        <p class="text-[11px] text-slate-600 mt-1.5 leading-relaxed bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $menu->description }}</p>
                    @endif
                </div>

                <div class="col-span-2 text-slate-500 text-xs hidden md:block">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-[11px] font-medium">
                        <i class="fa-solid fa-tag text-[10px] text-slate-400"></i> {{ $menu->category ?: 'Umum' }}
                    </span>
                </div>

                <div class="col-span-1 flex justify-center w-full md:w-auto">
                    @if($menu->is_ready)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Habis
                    </span>
                    @endif
                </div>

                <div class="col-span-2 flex justify-end w-full md:w-auto">
                    @if($menu->is_ready)
                    <button onclick="addRestoMenu({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ (float)$menu->price }}, '{{ count($menuPhotos) > 0 ? asset('storage/' . $menuPhotos[0]) : '' }}')" class="w-full md:w-auto px-5 py-2 border border-sky-500 bg-sky-50 hover:bg-sky-600 text-sky-700 hover:text-white rounded-xl text-xs font-semibold transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer" type="button">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambahkan
                    </button>
                    @else
                    <button disabled class="w-full md:w-auto px-5 py-2 border border-slate-200 text-slate-400 bg-slate-50 rounded-xl text-xs font-medium cursor-not-allowed" type="button">
                        Habis
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-8 text-center text-slate-400 text-xs">
            Belum ada menu restoran yang ditambahkan untuk paket ini.
        </div>
        @endif
    </section>
    <!-- END: FiturPaketTable -->

    <!-- BEGIN: DeskripsiContainer -->
    @if(!empty($descHtml) && trim(strip_tags($descHtml)) !== '')
    <section class="bg-white rounded-2xl p-6 shadow-sm min-h-[90px]" data-purpose="deskripsi-section">
        <h3 class="font-bold text-slate-800 text-base mb-3">Deskripsi</h3>
        <div class="prose max-w-none text-slate-600 text-sm leading-relaxed">
            {!! html_entity_decode((string)$descHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
        </div>
    </section>
    @endif
    <!-- END: DeskripsiContainer -->

    <!-- BEGIN: UlasanPembeliSection (Real Reviews Only) -->
    <section id="customer-reviews" class="bg-white rounded-2xl p-6 shadow-sm space-y-6" data-purpose="customer-reviews">
        <div>
            <h3 class="font-bold text-slate-800 text-base">Ulasan Pembeli</h3>
            <p class="text-xs text-slate-400 mt-1">
                @if($reviewCount > 0)
                    Rata-rata {{ number_format($avg, 1, ',', '.') }}/5 dari {{ $reviewCount }} ulasan
                @else
                    Belum ada ulasan yang ditampilkan
                @endif
            </p>
        </div>

        @if($reviewCount === 0)
        <!-- Empty Notice Card -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-5 text-xs text-slate-500 leading-relaxed">
            <p>Jadi yang pertama memberikan ulasan.</p>
            <p>Setelah submit, ulasan akan dimoderasi admin.</p>
        </div>
        @else
        <!-- Reviews List -->
        <div class="space-y-3">
            @foreach($approvedReviews as $review)
            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-slate-800 text-sm">{{ $review->name }}</div>
                    <div class="flex gap-1 text-amber-400 text-xs">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                </div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $review->created_at->format('d M Y') }}</div>
                <p class="text-xs text-slate-600 mt-2">{{ $review->comment }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Review Form -->
        <div class="pt-4 border-t border-slate-100 space-y-4">
            <h4 class="font-bold text-slate-800 text-sm">Tulis Ulasan</h4>

            @if(session('success'))
            <div class="rounded-xl bg-green-50 text-green-800 px-4 py-3 text-xs border border-green-200">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('review.store') }}" class="space-y-4" x-data="{ rating: 5, hover: 0 }">
                @csrf
                <input type="hidden" name="reviewable_type" value="restoran">
                <input type="hidden" name="reviewable_id" value="{{ $package->id }}">
                <input type="hidden" name="rating" :value="rating">
                <input type="text" name="website" class="hidden">
                <input type="hidden" name="form_started_at" value="{{ time() }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input class="w-full rounded-xl border-slate-200 text-sm px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500 placeholder:text-slate-400"
                        placeholder="Nama Lengkap" name="name" type="text" required>
                    <input class="w-full rounded-xl border-slate-200 text-sm px-4 py-2.5 focus:border-sky-500 focus:ring-sky-500 placeholder:text-slate-400"
                        placeholder="Email" name="email" type="email" required>
                </div>

                <div class="space-y-1">
                    <div class="text-xs font-semibold text-slate-600">Rating <span x-text="rating + '/5'"></span></div>
                    <div class="flex justify-center items-center gap-1.5 py-2 text-amber-400 text-2xl">
                        <template x-for="n in 5" :key="n">
                            <i class="fa-solid fa-star cursor-pointer hover:scale-110 transition"
                                :class="(hover || rating) >= n ? 'text-amber-400' : 'text-slate-200'"
                                @mouseenter="hover = n"
                                @mouseleave="hover = 0"
                                @click="rating = n"></i>
                        </template>
                    </div>
                </div>

                <div>
                    <textarea class="w-full rounded-xl border-slate-200 text-sm p-4 focus:border-sky-500 focus:ring-sky-500 placeholder:text-slate-400 resize-none"
                        placeholder="Pengalaman Anda..." name="comment" rows="4" required></textarea>
                </div>

                <button class="w-full py-3 bg-[#0088f8] hover:bg-sky-600 active:scale-[0.99] text-white font-medium text-sm rounded-xl shadow transition" type="submit">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    </section>
    <!-- END: UlasanPembeliSection -->

</main>
<!-- END: MainContentContainer -->

{{-- POPUP BOOKING / RESERVASI --}}
@include('front.restoran.partials.booking-popup', ['package' => $package])

{{-- LIGHTBOX MODAL --}}
<div id="restoLightbox" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
    <div id="restoLightboxBackdrop" class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeLightbox()"></div>

    <div class="absolute top-0 left-0 right-0 z-10">
        <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 border border-white/15 flex items-center justify-center">
                    <i class="fa-regular fa-images text-white"></i>
                </div>
                <div class="text-white/90 text-sm">
                    <span id="restoLightboxCounter" class="font-semibold"></span>
                    <span id="restoLightboxCaption" class="ml-2 text-white/70"></span>
                </div>
            </div>

            <button type="button" onclick="closeLightbox()"
                class="w-10 h-10 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 flex items-center justify-center transition cursor-pointer">
                <span class="text-2xl leading-none">&times;</span>
            </button>
        </div>
    </div>

    <div class="absolute inset-0 flex items-center justify-center px-4">
        <div class="relative w-full max-w-5xl">
            <img id="restoLightboxMain"
                class="w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl select-none mx-auto"
                alt="Preview">

            <button id="restoLightboxPrev" type="button" onclick="prevLightbox()"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-angle-left"></i>
            </button>

            <button id="restoLightboxNext" type="button" onclick="nextLightbox()"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-angle-right"></i>
            </button>

            <div id="restoLightboxThumbs" class="mt-4 flex gap-2 overflow-x-auto justify-center"></div>
        </div>
    </div>
</div>

{{-- MODAL FASILITAS LENGKAP --}}
<div id="modalFasilitas" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeFasilitasModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 z-10 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-list-check text-[#0088f8]"></i> Fasilitas Utama
            </h3>
            <button type="button" onclick="closeFasilitasModal()" class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xl leading-none transition cursor-pointer">
                &times;
            </button>
        </div>
        <div class="space-y-3.5 py-2 max-h-[60vh] overflow-y-auto pr-1">
            @if(isset($facilityList) && count($facilityList) > 0)
                @foreach($facilityList as $facility)
                    @php $fLower = strtolower(trim($facility)); @endphp
                    <div class="flex items-center gap-3 text-sm text-slate-700">
                        @if(str_contains($fLower, 'parkir') || str_contains($fLower, 'parking'))
                            <div class="w-5 h-5 rounded border-[1.5px] border-[#0088f8] text-[#0088f8] font-bold text-xs flex items-center justify-center leading-none flex-shrink-0">P</div>
                        @elseif(str_contains($fLower, 'resto') || str_contains($fLower, 'makan') || str_contains($fLower, 'kuliner'))
                            <div class="w-5 flex justify-center text-[#0088f8] text-sm flex-shrink-0"><i class="fa-solid fa-utensils"></i></div>
                        @elseif(str_contains($fLower, 'wifi') || str_contains($fLower, 'internet'))
                            <div class="w-5 flex justify-center text-[#0088f8] text-sm flex-shrink-0"><i class="fa-solid fa-wifi"></i></div>
                        @elseif(str_contains($fLower, 'musholla') || str_contains($fLower, 'masjid') || str_contains($fLower, 'ibadah'))
                            <div class="w-5 flex justify-center text-[#0088f8] text-sm flex-shrink-0"><i class="fa-solid fa-mosque"></i></div>
                        @else
                            <div class="w-5 flex justify-center text-[#0088f8] text-sm flex-shrink-0"><i class="fa-solid fa-circle-check"></i></div>
                        @endif
                        <span class="font-medium text-slate-800">{{ $facility }}</span>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" onclick="closeFasilitasModal()" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
            Tutup
        </button>
    </div>
</div>

{{-- MODAL KEUNGGULAN LENGKAP --}}
<div id="modalKeunggulan" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeKeunggulanModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 z-10 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-[#f59e0b]"></i> Keunggulan Resto
            </h3>
            <button type="button" onclick="closeKeunggulanModal()" class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center text-xl leading-none transition cursor-pointer">
                &times;
            </button>
        </div>
        <div class="bg-[#f0f4f8] rounded-xl p-3 text-xs text-slate-600 leading-relaxed">
            Sebagai Preferred Partner Bintang Wisata selalu menjaga performa terbaik yang secara rutin diverifikasi Bintang Wisata.
        </div>
        <div class="space-y-3 py-1 max-h-[50vh] overflow-y-auto pr-1">
            @if(isset($keunggulanList) && count($keunggulanList) > 0)
                @foreach($keunggulanList as $point)
                <div class="flex items-start gap-2.5 text-xs text-slate-700 font-medium">
                    <i class="fa-solid fa-shield-halved text-[#f59e0b] text-xs mt-0.5 flex-shrink-0"></i>
                    <span>{{ $point }}</span>
                </div>
                @endforeach
            @endif
        </div>
        <button type="button" onclick="closeKeunggulanModal()" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer">
            Tutup
        </button>
    </div>
</div>

<script>
    function openFasilitasModal() {
        const m = document.getElementById('modalFasilitas');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
    }
    function closeFasilitasModal() {
        const m = document.getElementById('modalFasilitas');
        if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    }
    function openKeunggulanModal() {
        const m = document.getElementById('modalKeunggulan');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
    }
    function closeKeunggulanModal() {
        const m = document.getElementById('modalKeunggulan');
        if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    }

    const defaultLightboxImages = @json($realPhotos);
    let lightboxImages = defaultLightboxImages;
    let currentLightboxIdx = 0;

    function openLightbox(idx, customImages = null) {
        if (customImages && customImages.length > 0) {
            lightboxImages = customImages;
        } else {
            lightboxImages = defaultLightboxImages;
        }
        if (!lightboxImages || lightboxImages.length === 0) return;
        currentLightboxIdx = Math.max(0, Math.min(idx, lightboxImages.length - 1));
        const modal = document.getElementById('restoLightbox');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        renderLightboxImage();
        renderLightboxThumbs();
    }

    function closeLightbox() {
        const modal = document.getElementById('restoLightbox');
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        lightboxImages = defaultLightboxImages;
    }

    function renderLightboxImage() {
        const main = document.getElementById('restoLightboxMain');
        const counter = document.getElementById('restoLightboxCounter');
        const caption = document.getElementById('restoLightboxCaption');

        if (!main || !lightboxImages[currentLightboxIdx]) return;
        main.src = lightboxImages[currentLightboxIdx].src;
        if (counter) counter.textContent = `${currentLightboxIdx + 1} / ${lightboxImages.length}`;
        if (caption) caption.textContent = lightboxImages[currentLightboxIdx].alt || '';

        const prevBtn = document.getElementById('restoLightboxPrev');
        const nextBtn = document.getElementById('restoLightboxNext');
        if (prevBtn && nextBtn) {
            if (lightboxImages.length <= 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            }
        }
    }

    function renderLightboxThumbs() {
        const container = document.getElementById('restoLightboxThumbs');
        if (!container) return;
        container.innerHTML = '';
        if (lightboxImages.length <= 1) {
            container.classList.add('hidden');
            return;
        }
        container.classList.remove('hidden');
        lightboxImages.forEach((item, idx) => {
            const thumb = document.createElement('img');
            thumb.src = item.src;
            thumb.className = `h-14 w-20 object-cover rounded-lg cursor-pointer border-2 transition ${idx === currentLightboxIdx ? 'border-sky-400 opacity-100' : 'border-transparent opacity-60 hover:opacity-100'}`;
            thumb.onclick = () => {
                currentLightboxIdx = idx;
                renderLightboxImage();
                renderLightboxThumbs();
            };
            container.appendChild(thumb);
        });
    }

    function nextLightbox() {
        if (lightboxImages.length <= 1) return;
        if (currentLightboxIdx < lightboxImages.length - 1) {
            currentLightboxIdx++;
        } else {
            currentLightboxIdx = 0;
        }
        renderLightboxImage();
        renderLightboxThumbs();
    }

    function prevLightbox() {
        if (lightboxImages.length <= 1) return;
        if (currentLightboxIdx > 0) {
            currentLightboxIdx--;
        } else {
            currentLightboxIdx = lightboxImages.length - 1;
        }
        renderLightboxImage();
        renderLightboxThumbs();
    }

    window.addEventListener('keydown', (e) => {
        const modal = document.getElementById('restoLightbox');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
        }
    });

    function addRestoMenu(id, name, price, thumbnail = '') {
        window.dispatchEvent(
            new CustomEvent('resto-add-menu', {
                detail: {
                    id: Number(id),
                    name: name,
                    price: Number(price),
                    thumbnail: thumbnail
                }
            })
        );
    }

    function triggerReservationPopup() {
        const today = new Date().toISOString().split('T')[0];
        window.dispatchEvent(
            new CustomEvent('open-restoran-booking', {
                detail: {
                    departure_date: today,
                    pickup_time: '12:00',
                    participants: 1
                }
            })
        );
    }

    function selectMenu(name, price) {
        triggerReservationPopup();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnHeader = document.getElementById('btnHeaderReservasi');
        if (btnHeader) {
            btnHeader.addEventListener('click', function() {
                triggerReservationPopup();
            });
        }
    });
</script>
@endsection