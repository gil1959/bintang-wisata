@extends('layouts.front')

@section('meta')
    @php
        $pkgOrArticle = isset($article) ? $article : ($package ?? null);
        $mTitle = $pkgOrArticle->seo_title ?? $pkgOrArticle->title ?? 'Bintang Wisata Holiday';
        $mDesc = $pkgOrArticle->seo_description ?? $pkgOrArticle->short_description ?? 'Akomodasi pilihan terbaik dengan pelayanan prima di Bintang Wisata.';
        $mKey = $pkgOrArticle->seo_keywords ?? 'hotel, vila, cottage, resort, penginapan, bintang wisata';
        $mImage = !empty($pkgOrArticle->seo_image_path) ? asset('storage/' . $pkgOrArticle->seo_image_path) : (!empty($package->thumbnail_path) ? asset('storage/' . $package->thumbnail_path) : asset('logo-atau-banner.jpg'));
        $sTitle = $pkgOrArticle->social_title ?? $mTitle;
        $sDesc = $pkgOrArticle->social_description ?? $mDesc;
    @endphp
    <title>{{ $mTitle }} | Bintang Wisata</title>
    <meta name="description" content="{{ $mDesc }}">
    <meta name="keywords" content="{{ $mKey }}">
    <meta name="author" content="Bintang Wisata">
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

@php
    $isEn = app()->getLocale() === 'en';
    $title = $isEn ? ($package->title_en ?: $package->title) : $package->title;
    $address = $isEn ? ($package->address_en ?: $package->address) : $package->address;
    $note = $isEn ? ($package->note_en ?: $package->note) : $package->note;
    $descHtml = $isEn ? ($package->long_description_en ?: $package->long_description) : $package->long_description;

    // Photos Collection
    $allPhotos = [];
    if (!empty($package->thumbnail_path)) {
        $allPhotos[] = [
            'src' => asset('storage/' . $package->thumbnail_path),
            'alt' => $title . ' - Cover Utama',
        ];
    }
    if ($package->photos && $package->photos->count() > 0) {
        foreach ($package->photos as $idx => $photo) {
            $allPhotos[] = [
                'src' => asset('storage/' . $photo->file_path),
                'alt' => $title . ' - Galeri ' . ($idx + 1),
            ];
        }
    }
    // Fallback if no photo uploaded
    if (empty($allPhotos)) {
        $allPhotos[] = [
            'src' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            'alt' => $title,
        ];
    }

    // Nearby places
    $defaultNearby = [
        ['name' => 'Taman Wisata & Rekreasi', 'distance' => '1.2 km'],
        ['name' => 'Pusat Kuliner & Belanja', 'distance' => '850 m'],
        ['name' => 'Spot Panorama Alam', 'distance' => '2.5 km'],
    ];
    $nearbyList = !empty($package->nearby_places) && is_array($package->nearby_places) && count($package->nearby_places) > 0 
        ? $package->nearby_places 
        : $defaultNearby;

    // Facilities
    $defaultFacilities = ['WiFi Gratis', 'Parkir Luas', 'Kolam Renang', 'Resepsionis 24 Jam'];
    $facilityList = !empty($package->facilities) && is_array($package->facilities) && count($package->facilities) > 0 
        ? $package->facilities 
        : $defaultFacilities;

    // Keunggulan
    $defaultKeunggulan = [
        'Harga terbaik dan transparan di kelasnya',
        'Kualitas layanan dan kebersihan terjamin',
        'Lokasi strategis dekat berbagai tempat wisata'
    ];
    $keunggulanList = !empty($package->keunggulan) && is_array($package->keunggulan) && count($package->keunggulan) > 0 
        ? $package->keunggulan 
        : $defaultKeunggulan;

    $addressText = !empty($address) ? $address : 'Alamat akomodasi dapat dilihat pada rincian peta.';
    $mapsUrl = !empty($package->maps_url) ? $package->maps_url : ('https://maps.google.com/?q=' . urlencode($title . ' ' . $addressText));
    $noteText = !empty($note) ? $note : 'Untuk Informasi ketersediaan anda bisa menghubungi kontak Bintang Wisata.';

    // Real Customer Reviews
    $approvedReviews = $package->reviews ? $package->reviews->where('status', 'approved') : collect();
    $reviewCount = $approvedReviews->count();
    $avgRating = $reviewCount > 0 ? round((float)$approvedReviews->avg('rating'), 1) : null;
    $ratingScore10 = $avgRating ? number_format($avgRating > 5 ? $avgRating : ($avgRating * 2), 1, ',', '.') : '-';
    
    $ratingLabel = 'Mengesankan';
    if ($avgRating) {
        $r10 = $avgRating > 5 ? $avgRating : ($avgRating * 2);
        if ($r10 >= 9.0) $ratingLabel = 'Luar Biasa';
        elseif ($r10 >= 8.0) $ratingLabel = 'Mengesankan';
        elseif ($r10 >= 7.0) $ratingLabel = 'Sangat Bagus';
        else $ratingLabel = 'Bagus';
    }

    $csPhone = preg_replace('/[^0-9]/', '', $package->cs_contact ?: '628123456789');
    $csWaUrl = "https://wa.me/{$csPhone}?text=" . urlencode("Halo CS Bintang Wisata, saya ingin informasi reservasi paket: {$title}");

    $rooms = $package->rooms ?? collect();
    $totalRoomsAvailable = $rooms->sum('available_rooms');
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto px-4 py-6 space-y-6 font-sans">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-sky-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('hotel.index') }}" class="hover:text-sky-600 transition">Hotel & Vila</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate max-w-[280px]">{{ $title }}</span>
    </nav>

    {{-- BEGIN: HERO PHOTO GALLERY (5 SLOTS OR ADAPTIVE) --}}
    <section class="bg-white rounded-2xl p-2.5 sm:p-4 shadow-sm border border-slate-100 overflow-hidden">
        @if(count($allPhotos) == 1)
            {{-- Single Photo --}}
            <div class="w-full h-[320px] md:h-[400px] rounded-xl overflow-hidden cursor-pointer group" onclick="openLightbox(0)">
                <img alt="{{ $allPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[0]['src'] }}">
            </div>
        @elseif(count($allPhotos) <= 3)
            {{-- 2 or 3 Photos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 h-[320px] md:h-[380px] rounded-xl overflow-hidden">
                <div class="md:col-span-2 h-full cursor-pointer overflow-hidden group" onclick="openLightbox(0)">
                    <img alt="{{ $allPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[0]['src'] }}">
                </div>
                <div class="grid {{ count($allPhotos) == 3 ? 'grid-rows-2' : 'grid-rows-1' }} gap-2 h-full">
                    <div class="overflow-hidden cursor-pointer group h-full" onclick="openLightbox(1)">
                        <img alt="{{ $allPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[1]['src'] }}">
                    </div>
                    @if(isset($allPhotos[2]))
                    <div class="overflow-hidden cursor-pointer group h-full" onclick="openLightbox(2)">
                        <img alt="{{ $allPhotos[2]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[2]['src'] }}">
                    </div>
                    @endif
                </div>
            </div>
        @else
            {{-- 4 or 5+ Photos: Full Stitch Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 h-[320px] md:h-[390px] rounded-xl overflow-hidden">
                {{-- Big Left Image --}}
                <div class="md:col-span-2 h-full relative group cursor-pointer overflow-hidden" onclick="openLightbox(0)">
                    <img alt="{{ $allPhotos[0]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[0]['src'] }}">
                </div>

                {{-- Middle Column: 2 Stacked Images --}}
                <div class="grid grid-rows-2 gap-2 h-full">
                    <div class="overflow-hidden cursor-pointer group" onclick="openLightbox(1)">
                        <img alt="{{ $allPhotos[1]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[1]['src'] }}">
                    </div>
                    <div class="overflow-hidden cursor-pointer group" onclick="openLightbox(2)">
                        <img alt="{{ $allPhotos[2]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[2]['src'] }}">
                    </div>
                </div>

                {{-- Right Column: 2 Stacked Images with Overlay on the bottom one --}}
                <div class="grid grid-rows-2 gap-2 h-full">
                    <div class="overflow-hidden cursor-pointer group" onclick="openLightbox(3)">
                        <img alt="{{ $allPhotos[3]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" src="{{ $allPhotos[3]['src'] }}">
                    </div>
                    @php $lastIdx = isset($allPhotos[4]) ? 4 : 3; @endphp
                    <div class="relative overflow-hidden cursor-pointer group" onclick="openLightbox({{ $lastIdx }})">
                        <img alt="{{ $allPhotos[$lastIdx]['alt'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 filter brightness-90" src="{{ $allPhotos[$lastIdx]['src'] }}">
                        <button type="button" class="absolute inset-0 m-auto w-max h-max px-3.5 py-2 bg-black/60 hover:bg-black/75 text-white text-xs font-semibold rounded-lg backdrop-blur-sm flex items-center gap-1.5 transition pointer-events-none">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>Lihat Semua Foto ({{ count($allPhotos) }})</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- HOTEL TITLE & PRICE HEADER --}}
        <div class="mt-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight leading-tight">{{ $title }}</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="bg-sky-50 text-sky-600 text-xs font-extrabold px-2.5 py-0.5 rounded border border-sky-200">
                        {{ $package->property_type ?? 'Hotel' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 bg-amber-50/80 px-2.5 py-0.5 rounded border border-amber-200">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="#fbbf24" stroke="#d97706" stroke-width="1.5">
                            <path d="M12 2L4 5v6.5C4 16.5 7.5 21.2 12 22.5c4.5-1.3 8-6 8-11V5l-8-3z"/>
                            <path d="M9 12l2 2 4-4" stroke="#92400e" stroke-width="2"/>
                        </svg>
                        Preferred Partner <span class="font-bold text-amber-600">Plus</span>
                    </span>
                    @if(!empty($package->label))
                    <span class="px-2.5 py-0.5 rounded text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $package->label }}
                    </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4 self-end md:self-auto">
                <div class="text-right">
                    <p class="text-xs text-slate-500 font-medium">Harga/kamar/malam mulai dari</p>
                    <p class="text-xl md:text-2xl font-black text-orange-600">
                        Rp {{ number_format($package->price_per_night, 0, ',', '.') }}
                    </p>
                </div>
                <a href="#room-selection"
                   class="text-white font-extrabold text-sm px-6 py-2.5 rounded-xl shadow-sm transition flex items-center justify-center cursor-pointer hover:opacity-95"
                   style="background:#0088f8;">
                    Lihat Kamar
                </a>
            </div>
        </div>

        {{-- SCARCITY ALERT BANNER --}}
        <div class="mt-4 rounded-xl p-3 flex items-center gap-3 text-xs md:text-sm text-slate-800"
             style="background-color: #def0fc; border: 1px solid #bae6fd;">
            <div style="width: 36px; height: 36px; background-color: #0353a4; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <svg class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="#facc15" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="13" r="8"/>
                    <path d="M12 9v4l2.5 1.5"/>
                    <path d="M12 5V2"/>
                    <path d="M10 2h4"/>
                </svg>
            </div>
            <div>
                Jangan lewatkan! <span class="font-extrabold" style="color: #0088f8;">Sisa {{ $totalRoomsAvailable > 0 ? $totalRoomsAvailable : 1 }} kamar</span> untuk harga paling murah.
            </div>
        </div>

        {{-- 3-CARD HIGHLIGHTS GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-5">

            {{-- 1. Rating & Keunggulan Properti --}}
            <div class="md:col-span-4 bg-gradient-to-br from-sky-50/70 via-white to-white border border-slate-200/80 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="text-3xl font-black text-sky-600">
                            {{ $ratingScore10 }}<span class="text-sm font-normal text-slate-400">/10</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 leading-tight">{{ $ratingLabel }}</p>
                            @if($reviewCount > 0)
                            <a class="text-xs text-sky-600 hover:underline font-semibold" href="#customer-reviews">
                                {{ $reviewCount }} ulasan &gt;
                            </a>
                            @else
                            <a class="text-xs text-sky-600 hover:underline font-semibold" href="#customer-reviews">
                                Belum ada ulasan &gt;
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">Keunggulan Properti</span>
                            <span class="text-[11px] text-sky-600 font-semibold">Terverifikasi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-2 leading-relaxed">
                            Sebagai Preferred Partner Bintang Wisata selalu menjaga performa terbaik yang secara rutin diverifikasi Bintang Wisata.
                        </p>
                        <ul class="space-y-1.5 text-xs text-slate-700">
                            @foreach($keunggulanList as $kItem)
                            <li class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="#fbbf24" stroke="#d97706" stroke-width="1.5">
                                    <path d="M12 2L4 5v6.5C4 16.5 7.5 21.2 12 22.5c4.5-1.3 8-6 8-11V5l-8-3z"/>
                                </svg>
                                <span>{{ is_array($kItem) ? ($kItem['text'] ?? '') : $kItem }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 2. Area Akomodasi Card with Subtle Top-Right Organic Watermark --}}
            <div class="md:col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 flex flex-col justify-between relative overflow-hidden">
                {{-- Subtle Map Watermark --}}
                <div class="absolute top-0 right-0 w-48 h-full pointer-events-none overflow-hidden opacity-80" aria-hidden="true">
                    <svg class="w-full h-full object-cover" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 0 C 120 40, 110 80, 135 120 S 185 170, 220 190" stroke="#bae6fd" stroke-width="2" stroke-linecap="round" fill="none" opacity="0.6"/>
                        <path d="M135 10 C 160 5, 195 15, 210 35 C 215 50, 200 70, 180 75 C 155 80, 130 55, 130 35 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.2"/>
                        <path d="M145 90 C 175 85, 210 100, 215 130 C 210 155, 180 165, 155 160 C 130 155, 125 125, 135 100 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1.2"/>
                        <path d="M115 0 C 135 45, 120 85, 145 125 S 195 175, 220 195" stroke="#e2e8f0" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xs font-bold text-slate-900">Area Akomodasi</h3>
                        <a class="text-[11px] text-sky-600 font-semibold hover:underline inline-flex items-center gap-1" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">
                            Lihat Peta &gt;
                        </a>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-slate-600 mb-3">
                        <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <p class="line-clamp-2 leading-relaxed">{{ $addressText }}</p>
                    </div>

                    <div class="inline-block bg-sky-50 text-sky-600 text-[11px] font-semibold px-2 py-0.5 rounded-md mb-3 border border-sky-200">
                        Dekat tempat rekreasi
                    </div>

                    <div class="space-y-2 text-xs text-slate-700">
                        @foreach(array_slice($nearbyList, 0, 3) as $np)
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-1.5 truncate">
                                <svg class="w-3 h-3 text-slate-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                </svg>
                                <span class="truncate">{{ $np['name'] ?? '-' }}</span>
                            </span>
                            <span class="text-slate-400 text-[11px] shrink-0 ml-2">{{ $np['distance'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Fasilitas Utama & NOTE Card --}}
            <div class="md:col-span-4 bg-white border border-slate-200/80 rounded-2xl p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold text-slate-900">Fasilitas Utama</h3>
                        <span class="text-[11px] text-sky-600 font-semibold">{{ count($facilityList) }} Fasilitas</span>
                    </div>

                    <div class="grid grid-cols-2 gap-y-2.5 gap-x-2 text-xs text-slate-700">
                        @foreach(array_slice($facilityList, 0, 6) as $fItem)
                        @php $fName = is_array($fItem) ? ($fItem['name'] ?? '') : $fItem; @endphp
                        <div class="flex items-center gap-2 truncate">
                            <svg class="w-3.5 h-3.5 text-sky-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                            <span class="truncate">{{ $fName }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-3">
                    <span class="text-[11px] font-extrabold text-slate-800 block">NOTE:</span>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">{{ $noteText }}</p>
                    <a href="{{ $csWaUrl }}" target="_blank" rel="noopener noreferrer"
                       class="text-[11px] font-bold hover:underline block mt-2"
                       style="color:#0088f8;">
                        Hubungi Kontak CS &gt;
                    </a>
                </div>
            </div>

        </div>
    </section>
    {{-- END: HERO GALLERY SECTION --}}

    {{-- BEGIN: PROMO BANNER --}}
    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-3.5 flex items-center gap-3 text-xs md:text-sm text-slate-800">
        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                <line x1="2" y1="10" x2="22" y2="10"></line>
            </svg>
        </div>
        <div>
            Suka penginapan ini? Dapatkan penawaran terbaik dan potongan harga dengan kode promo saat reservasi!
        </div>
    </div>
    {{-- END: PROMO BANNER --}}

    {{-- BEGIN: ROOM SELECTION SECTION --}}
    <section class="space-y-4" data-purpose="room-options-catalog" id="room-selection">
        
        {{-- Section Header --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <h2 class="text-base md:text-lg font-black text-slate-900">
                Tipe Kamar yang Tersedia di {{ $title }}
            </h2>
            <p class="text-xs text-slate-500">
                Pilih kamar yang sesuai dengan rencana liburan dan kebutuhan menginap Anda.
            </p>
        </div>

        {{-- ROOM CARDS REPEATER --}}
        @forelse($rooms as $room)
        <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:border-slate-300 transition">
            {{-- Room Card Title --}}
            <div class="bg-slate-50/80 px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm md:text-base">{{ $room->name }}</h3>
                @if($room->is_ready)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Tersedia
                </span>
                @else
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                    Habis Terjual
                </span>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Left: Room Photo & Key Specs --}}
                <div class="lg:col-span-4 p-5 border-b lg:border-b-0 lg:border-r border-slate-200 flex flex-col justify-between">
                    <div>
                        @php
                            $roomPhotos = $room->all_photos;
                            $roomPhotosFormatted = array_map(function($path) use ($room) {
                                return [
                                    'src' => asset('storage/' . $path),
                                    'alt' => $room->name
                                ];
                            }, $roomPhotos);
                            if (empty($roomPhotosFormatted) && !empty($package->thumbnail_path)) {
                                $roomPhotosFormatted[] = [
                                    'src' => asset('storage/' . $package->thumbnail_path),
                                    'alt' => $room->name
                                ];
                            }
                        @endphp
                        <div class="rounded-xl overflow-hidden h-48 w-full relative mb-3 bg-slate-100 border border-slate-200 group">
                            @if(count($roomPhotosFormatted) > 0)
                            <div x-data="{
                                active: 0,
                                photos: {{ json_encode($roomPhotosFormatted) }},
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
                            class="relative w-full h-full">
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
                                        title="Klik untuk melihat foto lebih besar"
                                        class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white cursor-pointer">
                                    <span class="bg-black/60 backdrop-blur-xs text-white text-[10px] px-3 py-1 rounded-full flex items-center gap-1.5 font-semibold shadow">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg> Preview Foto
                                    </span>
                                </button>

                                @if(count($roomPhotosFormatted) > 1)
                                    {{-- Arrow Controls --}}
                                    <button type="button" 
                                            @click.stop="active = (active === 0 ? photos.length - 1 : active - 1)" 
                                            class="absolute left-1.5 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[10px] cursor-pointer">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" 
                                            @click.stop="active = (active + 1) % photos.length" 
                                            class="absolute right-1.5 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-[10px] cursor-pointer">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>

                                    {{-- Photo Count Pill --}}
                                    <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1.5 pointer-events-none">
                                        <svg class="w-3 h-3 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                        <span x-text="(active + 1) + '/' + photos.length"></span>
                                    </div>
                                @endif
                            </div>
                            @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 7v11a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"/>
                                    <path d="M3 11h18"/>
                                </svg>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-2 text-xs text-slate-700">
                            @if(!empty($room->room_size))
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 3H3v18h18V3z"/>
                                    <path d="M9 3v18"/>
                                    <path d="M15 3v18"/>
                                    <path d="M3 9h18"/>
                                    <path d="M3 15h18"/>
                                </svg>
                                <span class="font-semibold">{{ $room->room_size }}</span>
                            </p>
                            @endif

                            @if(!empty($room->bed_type))
                            <p class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 4v16"/>
                                    <path d="M2 8h18a2 2 0 0 1 2 2v10"/>
                                    <path d="M2 17h20"/>
                                    <path d="M6 8v9"/>
                                </svg>
                                <span>{{ $room->bed_type }}</span>
                            </p>
                            @endif

                            <div class="flex items-center gap-4 pt-1 text-slate-600">
                                @if($room->has_shower)
                                <span class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16v4H4z"/>
                                        <path d="M10 8v12"/>
                                    </svg>
                                    Shower
                                </span>
                                @endif

                                @if($room->has_wifi)
                                <span class="flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                                        <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                                        <line x1="12" y1="20" x2="12.01" y2="20"/>
                                    </svg>
                                    Free WiFi
                                </span>
                                @endif
                            </div>

                            @if(!empty($room->description))
                            <div class="pt-2.5 border-t border-slate-100 mt-2">
                                <span class="font-bold text-slate-700 block text-[10px] uppercase tracking-wider text-slate-400 mb-1">Deskripsi Kamar:</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $room->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Room Plan Options Table --}}
                <div class="lg:col-span-8 overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse min-w-[500px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                <th class="py-3 px-4">Pilihan Paket</th>
                                <th class="py-3 px-3 text-center w-16">Tamu</th>
                                <th class="py-3 px-4 text-right">Harga / Malam</th>
                                <th class="py-3 px-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            {{-- Option 1: Tanpa Sarapan --}}
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 align-top">
                                    <p class="font-extrabold text-slate-900 text-xs">Tanpa Sarapan</p>
                                    @if(!empty($room->bed_type))
                                    <p class="text-slate-500 mt-1 text-[11px]">{{ $room->bed_type }}</p>
                                    @endif
                                    <p class="text-slate-400 mt-1 text-[10px] flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Bisa dijadwalkan ulang
                                    </p>
                                </td>

                                <td class="py-3.5 px-3 align-middle text-center text-slate-700">
                                    <span class="inline-flex items-center gap-1 font-bold">
                                        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $room->max_guests }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 align-middle text-right">
                                    <span class="inline-block bg-orange-50 text-orange-600 text-[10px] font-extrabold px-2 py-0.5 rounded mb-1">
                                        Special for you!
                                    </span>
                                    @if(!empty($room->original_price) && $room->original_price > $room->price)
                                    <p class="line-through text-slate-400 text-[11px]">
                                        Rp {{ number_format($room->original_price, 0, ',', '.') }}
                                    </p>
                                    @endif
                                    <p class="text-base font-black text-orange-600">
                                        Rp {{ number_format($room->price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">Di luar pajak &amp; biaya</p>
                                    @if(!empty($room->description))
                                    <div class="mt-2 text-left bg-slate-50/90 p-2 rounded-lg border border-slate-100 text-[11px] text-slate-600 font-normal leading-relaxed">
                                        <span class="font-bold text-slate-700 block text-[10px] uppercase tracking-wider text-slate-400 mb-0.5">Deskripsi:</span>
                                        {{ $room->description }}
                                    </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 align-middle text-center">
                                    @if($room->is_ready)
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('open-hotel-booking', { detail: { room_id: {{ $room->id }}, room_name: '{{ addslashes($room->name) }}', with_breakfast: false, price: {{ (float)$room->price }} } }))"
                                            class="w-full text-white font-extrabold py-2 px-3 rounded-xl text-xs transition shadow-sm hover:opacity-95 cursor-pointer"
                                            style="background:#0088f8;">
                                        Pilih
                                    </button>
                                    <p class="text-[10px] text-red-500 font-bold mt-1">Sisa {{ $room->available_rooms }} kamar!</p>
                                    @else
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold py-2 px-3 rounded-xl text-xs cursor-not-allowed">
                                        Habis
                                    </button>
                                    @endif
                                </td>
                            </tr>

                            {{-- Option 2: Sarapan untuk 2 / Pax --}}
                            @if($room->has_breakfast || !empty($room->price_with_breakfast))
                            @php
                                $breakfastRate = $room->price_with_breakfast ?: ($room->price + 85000);
                                $origBreakfastRate = !empty($room->original_price) ? ($room->original_price + 90000) : ($breakfastRate * 1.15);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 align-top">
                                    <div class="inline-flex items-center gap-1 bg-emerald-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full mb-1">
                                        Kamar dengan sarapan
                                    </div>
                                    <p class="font-extrabold text-slate-900 text-xs">Sarapan untuk {{ $room->max_guests }} Orang</p>
                                    @if(!empty($room->bed_type))
                                    <p class="text-slate-500 mt-1 text-[11px]">{{ $room->bed_type }}</p>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 align-middle text-center text-slate-700">
                                    <span class="inline-flex items-center gap-1 font-bold">
                                        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        {{ $room->max_guests }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 align-middle text-right">
                                    <span class="inline-block bg-orange-50 text-orange-600 text-[10px] font-extrabold px-2 py-0.5 rounded mb-1">
                                        Special for you!
                                    </span>
                                    <p class="line-through text-slate-400 text-[11px]">
                                        Rp {{ number_format($origBreakfastRate, 0, ',', '.') }}
                                    </p>
                                    <p class="text-base font-black text-orange-600">
                                        Rp {{ number_format($breakfastRate, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">Di luar pajak &amp; biaya</p>
                                    @if(!empty($room->description))
                                    <div class="mt-2 text-left bg-slate-50/90 p-2 rounded-lg border border-slate-100 text-[11px] text-slate-600 font-normal leading-relaxed">
                                        <span class="font-bold text-slate-700 block text-[10px] uppercase tracking-wider text-slate-400 mb-0.5">Deskripsi:</span>
                                        {{ $room->description }}
                                    </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-3 align-middle text-center">
                                    @if($room->is_ready)
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('open-hotel-booking', { detail: { room_id: {{ $room->id }}, room_name: '{{ addslashes($room->name) }}', with_breakfast: true, price: {{ (float)$breakfastRate }} } }))"
                                            class="w-full text-white font-extrabold py-2 px-3 rounded-xl text-xs transition shadow-sm hover:opacity-95 cursor-pointer"
                                            style="background:#0088f8;">
                                        Pilih
                                    </button>
                                    <p class="text-[10px] text-red-500 font-bold mt-1">Sisa {{ $room->available_rooms }} kamar!</p>
                                    @else
                                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-bold py-2 px-3 rounded-xl text-xs cursor-not-allowed">
                                        Habis
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
        @empty
        {{-- Fallback if no specific rooms added yet --}}
        <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm p-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-base font-extrabold text-slate-900">Standar / Pilihan Properti</h3>
                    <p class="text-xs text-slate-500">Reservasi langsung kamar utama penginapan ini.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-xs text-slate-400 block">Harga per malam</span>
                        <span class="text-lg font-black text-orange-600">Rp {{ number_format($package->price_per_night, 0, ',', '.') }}</span>
                    </div>
                    <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-hotel-booking', { detail: { room_id: null, room_name: 'Standar / Pilihan Properti', with_breakfast: false, price: {{ (float)$package->price_per_night }} } }))"
                            class="px-6 py-2.5 text-white font-extrabold rounded-xl text-xs transition shadow"
                            style="background:#0088f8;">
                        Pilih &amp; Reservasi
                    </button>
                </div>
            </div>
        </article>
        @endforelse

    </section>
    {{-- END: ROOM SELECTION SECTION --}}

    {{-- BEGIN: DESKRIPSI LENGKAP --}}
    @if(!empty($descHtml))
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-sky-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            Deskripsi Properti
        </h3>
        <div class="prose max-w-none text-slate-700 text-xs md:text-sm leading-relaxed overflow-hidden">
            {!! html_entity_decode((string)$descHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
        </div>
    </section>
    @endif
    {{-- END: DESKRIPSI LENGKAP --}}

    {{-- BEGIN: CUSTOMER REVIEWS --}}
    <div id="customer-reviews">
        @include('front.partials.reviews', ['item' => $package, 'type' => 'hotel'])
    </div>
    {{-- END: CUSTOMER REVIEWS --}}

</div>

{{-- MODAL POPUP RESERVATION --}}
@include('front.hotel.partials.booking-popup', ['package' => $package])

{{-- LIGHTBOX MODAL --}}
<div id="hotelLightbox" class="fixed inset-0 z-[99999] hidden" aria-modal="true" role="dialog">
    <div id="hotelLbBackdrop" class="absolute inset-0 bg-black/90 backdrop-blur-sm" onclick="closeLightbox()"></div>

    <div class="absolute top-0 left-0 right-0 z-10 px-4 py-3 flex items-center justify-between bg-black/40">
        <div class="text-white text-xs sm:text-sm font-bold truncate max-w-[70%]">
            <span id="lbCounter" class="mr-2 text-sky-400"></span>
            <span id="lbTitle" class="text-white/80"></span>
        </div>
        <button type="button" onclick="closeLightbox()" class="text-white/80 hover:text-white p-2 text-xl font-black transition">
            ✕
        </button>
    </div>

    <div class="absolute inset-0 flex items-center justify-center p-4 z-0">
        <img id="lbMainImage" src="" alt="Preview" class="max-h-[85vh] max-w-[92vw] object-contain rounded-xl shadow-2xl transition duration-300">
    </div>

    {{-- Lightbox Arrows --}}
    <button id="lbPrevBtn" type="button" onclick="prevLightbox()" class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition cursor-pointer">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button id="lbNextBtn" type="button" onclick="nextLightbox()" class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition cursor-pointer">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
</div>

<script>
    const defaultHotelPhotos = @json($allPhotos);
    let activeHotelPhotos = defaultHotelPhotos;
    let curLbIdx = 0;

    function openLightbox(idx, customPhotos = null) {
        if (customPhotos && customPhotos.length > 0) {
            activeHotelPhotos = customPhotos;
        } else {
            activeHotelPhotos = defaultHotelPhotos;
        }
        if (!activeHotelPhotos || activeHotelPhotos.length === 0) return;
        curLbIdx = Math.max(0, Math.min(idx, activeHotelPhotos.length - 1));
        renderLightbox();
        document.getElementById('hotelLightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('hotelLightbox').classList.add('hidden');
        document.body.style.overflow = '';
        activeHotelPhotos = defaultHotelPhotos;
    }

    function renderLightbox() {
        const photo = activeHotelPhotos[curLbIdx];
        if (!photo) return;
        document.getElementById('lbMainImage').src = photo.src;
        document.getElementById('lbTitle').textContent = photo.alt || 'Foto Penginapan';
        document.getElementById('lbCounter').textContent = `${curLbIdx + 1} / ${activeHotelPhotos.length}`;

        const prevBtn = document.getElementById('lbPrevBtn');
        const nextBtn = document.getElementById('lbNextBtn');
        if (prevBtn && nextBtn) {
            if (activeHotelPhotos.length <= 1) {
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
                nextBtn.classList.remove('hidden');
            }
        }
    }

    function prevLightbox() {
        if (activeHotelPhotos.length <= 1) return;
        if (curLbIdx > 0) curLbIdx--;
        else curLbIdx = activeHotelPhotos.length - 1;
        renderLightbox();
    }

    function nextLightbox() {
        if (activeHotelPhotos.length <= 1) return;
        if (curLbIdx < activeHotelPhotos.length - 1) curLbIdx++;
        else curLbIdx = 0;
        renderLightbox();
    }

    document.addEventListener('keydown', function(e) {
        const lb = document.getElementById('hotelLightbox');
        if (lb && !lb.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
        }
    });
</script>
@endsection