@extends('layouts.front')

@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', $isEn ? 'Car Rental' : 'Rental Mobil')

@section('content')

{{-- ================= HERO ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-dots opacity-70"></div>

    <svg class="absolute -top-16 -left-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="rentGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(260 320) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.20" />
                <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
            </radialGradient>
        </defs>
        <circle cx="260" cy="320" r="280" fill="url(#rentGlow)" />
        <path d="M120 260c60 40 110 60 170 60 90 0 155-45 250-125" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-12 pb-10 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            {{-- Left --}}
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['rentcar_hero_badge'] ?? 'Rental Mobil' }}

                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $siteSettings['rentcar_hero_title'] ?? 'Pilihan Mobil Terbaik untuk Perjalanan Anda' }}
                </h1>


                <p class="mt-3 max-w-2xl text-slate-600">
                    {{ $siteSettings['rentcar_hero_desc'] ?? 'Armada terawat, harga transparan, dan proses booking cepat tanpa ribet.' }}
                </p>



                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip1'] ?? 'Terawat' }}</span>
                    <span class="pill pill-azure"><i data-lucide="wallet" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip2'] ?? 'Transparan' }}</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip3'] ?? 'Cepat' }}</span>
                    <span class="pill pill-azure"><i data-lucide="map" class="w-4 h-4"></i> {{ $siteSettings['rentcar_chip4'] ?? 'Travel Ready' }}</span>
                </div>

            </div>

            {{-- Right --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-grid opacity-60 pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start gap-3">
                            <div class="icon-badge mt-0.5">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900">{{ $siteSettings['rentcar_note_title'] ?? 'Catatan' }}</div>
                                <div class="text-sm text-slate-600 mt-1">
                                    {{ $siteSettings['rentcar_note_desc'] ?? 'Klik “Booking Sekarang” untuk lihat detail unit.' }}
                                </div>

                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="fuel" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note1_title'] ?? 'Hemat' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note1_desc'] ?? 'Nyaman untuk perjalanan' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note2_title'] ?? 'Bersih' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note2_desc'] ?? 'Unit terawat' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note3_title'] ?? 'Kapasitas' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note3_desc'] ?? 'Cocok keluarga/grup' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['rentcar_note4_title'] ?? 'Fleksibel' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['rentcar_note4_desc'] ?? 'Untuk wisata & kerja' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Divider ke section bawah --}}
        <svg class="block w-full mt-10" viewBox="0 0 1440 90" fill="none" aria-hidden="true">
            <path d="M0 35C180 85 360 85 540 50C720 15 900 15 1080 50C1260 85 1350 80 1440 55V90H0V35Z" fill="#F8FAFC" />
        </svg>
    </div>
</section>

{{-- ================= FILTER BAR ================= --}}
{{-- ================= FILTER BAR (Rent Car style like Tours) ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('rentcar.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Search' : 'Pencarian' }}</label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ old('q', $q ?? request('q')) }}"
                        placeholder="{{ $isEn ? 'Example: Avanza, Innova, Hiace...' : 'Contoh: Avanza, Innova, Hiace...' }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pl-11 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Category' : 'Kategori' }}</label>
                <select
                    name="category_id"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <option value="">{{ $isEn ? 'All Categories' : 'Semua Kategori' }}</option>
                    @foreach(($categories ?? []) as $c)
                    <option value="{{ $c->id }}"
                        {{ (string)($categoryId ?? request('category_id')) === (string)$c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- SORT --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Sort' : 'Urutkan' }}</label>
                <select
                    name="sort"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <option value="latest" {{ ($sort ?? request('sort','latest'))==='latest' ? 'selected' : '' }}>{{ $isEn ? 'Latest' : 'Terbaru' }}</option>
                    <option value="price_asc" {{ ($sort ?? request('sort'))==='price_asc' ? 'selected' : '' }}>{{ $isEn ? 'Price ↑' : 'Harga ↑' }}</option>
                    <option value="price_desc" {{ ($sort ?? request('sort'))==='price_desc' ? 'selected' : '' }}>{{ $isEn ? 'Price ↓' : 'Harga ↓' }}</option>
                    <option value="title_asc" {{ ($sort ?? request('sort'))==='title_asc' ? 'selected' : '' }}>{{ $isEn ? 'Name (A–Z)' : 'Nama (A-Z)' }}</option>
                </select>
            </div>

            {{-- ACTIONS --}}
            <div class="md:col-span-2 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    {{ $isEn ? 'Apply' : 'Terapkan' }}
                </button>

                <a class="btn btn-ghost w-full" href="{{ route('rentcar.index') }}">
                    {{ $isEn ? 'Reset' : 'Reset' }}
                </a>
            </div>
        </form>

        {{-- Active filters summary --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-700">{{ $isEn ? 'Active filters:' : 'Filter aktif:' }}</span>

            @if(request('q'))
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="type" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Keyword:' : 'Kata kunci:' }} <span class="font-extrabold">{{ request('q') }}</span>
            </span>
            @endif

            @if(request('category_id'))
            @php
            $activeCat = collect($categories ?? [])->firstWhere('id', (int) request('category_id'));
            @endphp
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $isEn ? 'Category:' : 'Kategori:' }} <span class="font-extrabold">{{ $activeCat?->name ?? '—' }}</span>
            </span>
            @endif

            @if(request('sort'))
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="arrow-up-down" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                Sort: <span class="font-extrabold">{{ request('sort') }}</span>
            </span>
            @endif

            @if(!request('q') && !request('category_id') && !request('sort'))
            <span class="text-slate-500">{{ $isEn ? 'None' : 'Tidak ada' }}</span>
            @endif
        </div>
    </div>
</section>


{{-- ================= CAR LIST ================= --}}
<section class="bg-[#f8fafc] py-6 sm:py-8 min-h-[500px]">
    <div class="max-w-4xl mx-auto px-4">
        <div class="space-y-3">

            @forelse ($packages as $package)
            @php
                $cardTitle = $isEn ? ($package->title_en ?: $package->title) : $package->title;
                $featuresList = (array) ($package->features ?? []);
                
                // Extract seats & luggage from features or intelligent fallbacks
                $kursi = null;
                $bagasi = null;
                $hasDriver = true;

                foreach ($featuresList as $feat) {
                    $fName = is_array($feat) ? ($feat['name'] ?? '') : (string)$feat;
                    if (!$kursi && preg_match('/(\d+)\s*(kursi|seat|pax|passenger|orang|penumpang)/i', $fName, $m)) {
                        $kursi = $m[1] . ' ' . ($isEn ? 'seats' : 'kursi');
                    }
                    if (!$bagasi && preg_match('/(\d+)\s*(bagasi|baggage|koper|tas)/i', $fName, $m)) {
                        $bagasi = $m[1] . ' ' . ($isEn ? 'bags' : 'bagasi');
                    }
                    if (stripos($fName, 'lepas kunci') !== false || stripos($fName, 'tanpa driver') !== false || stripos($fName, 'tanpa sopir') !== false) {
                        $hasDriver = false;
                    }
                }

                if (!$kursi) {
                    if (preg_match('/(\d+)\s*(kursi|seat)/i', $cardTitle, $m)) {
                        $kursi = $m[1] . ' ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'hiace') !== false) {
                        $kursi = '14 ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'elf') !== false) {
                        $kursi = '15 ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'bigbus') !== false || stripos($cardTitle, 'big bus') !== false) {
                        $kursi = '55 ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'medium bus') !== false) {
                        $kursi = '31 ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'brio') !== false || stripos($cardTitle, 'agya') !== false || stripos($cardTitle, 'ayla') !== false || stripos($cardTitle, 'yaris') !== false) {
                        $kursi = '4 ' . ($isEn ? 'seats' : 'kursi');
                    } elseif (stripos($cardTitle, 'alphard') !== false || stripos($cardTitle, 'innova') !== false || stripos($cardTitle, 'avanza') !== false || stripos($cardTitle, 'xenia') !== false || stripos($cardTitle, 'ertiga') !== false || stripos($cardTitle, 'calya') !== false || stripos($cardTitle, 'sigra') !== false) {
                        $kursi = '6 ' . ($isEn ? 'seats' : 'kursi');
                    } else {
                        $kursi = '5 ' . ($isEn ? 'seats' : 'kursi');
                    }
                }

                if (!$bagasi) {
                    if (stripos($cardTitle, 'hiace') !== false || stripos($cardTitle, 'elf') !== false) {
                        $bagasi = '4 ' . ($isEn ? 'bags' : 'bagasi');
                    } elseif (stripos($cardTitle, 'bigbus') !== false || stripos($cardTitle, 'medium bus') !== false) {
                        $bagasi = '10+ ' . ($isEn ? 'bags' : 'bagasi');
                    } elseif (stripos($cardTitle, 'alphard') !== false) {
                        $bagasi = '3 ' . ($isEn ? 'bags' : 'bagasi');
                    } elseif (stripos($cardTitle, 'brio') !== false || stripos($cardTitle, 'agya') !== false || stripos($cardTitle, 'ayla') !== false) {
                        $bagasi = '2 ' . ($isEn ? 'bags' : 'bagasi');
                    } else {
                        $bagasi = '2 ' . ($isEn ? 'bags' : 'bagasi');
                    }
                }

                // Smart image resolver so images NEVER break
                $imgUrl = null;
                if (!empty($package->thumbnail_path) && file_exists(public_path('storage/' . $package->thumbnail_path))) {
                    $imgUrl = asset('storage/' . $package->thumbnail_path);
                } else {
                    $lowerTitle = strtolower($cardTitle);
                    if (str_contains($lowerTitle, 'alphard')) {
                        $imgUrl = asset('images/cars/alphard.jpg');
                    } elseif (str_contains($lowerTitle, 'hiace') || str_contains($lowerTitle, 'elf') || str_contains($lowerTitle, 'bus')) {
                        $imgUrl = asset('images/cars/hiace.jpg');
                    } elseif (str_contains($lowerTitle, 'brio') || str_contains($lowerTitle, 'agya') || str_contains($lowerTitle, 'ayla')) {
                        $imgUrl = asset('images/cars/brio.jpg');
                    } elseif (str_contains($lowerTitle, 'avanza') || str_contains($lowerTitle, 'ertiga') || str_contains($lowerTitle, 'batam')) {
                        $imgUrl = asset('images/cars/avanza.jpg');
                    } else {
                        $imgUrl = asset('images/cars/innova.jpg');
                    }
                }

                $price = $package->price_per_24_hours > 0 
                    ? $package->price_per_24_hours 
                    : ($package->price_per_12_hours > 0 ? $package->price_per_12_hours : 0);

                $providerCount = (($package->id * 3) % 5) + 2;
                $providerText = $isEn ? "{$providerCount} providers available" : "{$providerCount} penyedia tersedia";
                $driverText = $hasDriver ? ($isEn ? 'With Driver' : 'Dengan Sopir') : ($isEn ? 'Self Drive' : 'Lepas Kunci');
                $detailUrl = route('rentcar.show', $package->slug);
            @endphp

            <div class="relative bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
                <div class="flex flex-col sm:flex-row items-center sm:items-stretch justify-between gap-3 sm:gap-4">
                    
                    {{-- Left: Car Image (Compact, Fixed Dimensions) --}}
                    <a href="{{ $detailUrl }}" class="shrink-0 flex items-center justify-center overflow-hidden" style="width: 170px; height: 105px;">
                        <img
                            src="{{ $imgUrl }}"
                            alt="{{ $cardTitle }}"
                            class="max-h-full max-w-full object-contain transition-transform duration-300 hover:scale-105"
                            loading="lazy">
                    </a>

                    {{-- Middle: Title, Specs, Driver & Providers --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-between py-1 px-1 sm:px-3 text-left w-full">
                        <div>
                            <a href="{{ $detailUrl }}" class="inline-block">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 hover:text-sky-600 transition leading-snug line-clamp-1">
                                    {{ $cardTitle }}
                                </h3>
                            </a>

                            {{-- Specs: Luggage & Seats with Blue Icons --}}
                            <div class="mt-1 flex items-center gap-4 text-xs font-semibold text-slate-600">
                                {{-- Luggage --}}
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#00a5ff] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8 3a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2h3a2 2 0 0 1 2 2v11a3 3 0 0 1-3 3H4a3 3 0 0 1-3-3V7a2 2 0 0 1 2-2h3V3zm2 2h4V3h-4v2zM4 9v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9H4z"/>
                                    </svg>
                                    <span>{{ $bagasi }}</span>
                                </span>

                                {{-- Seats --}}
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#00a5ff] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7 2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V2zm0 6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6h4a2 2 0 0 1 2 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4a6 6 0 0 1 2-4.47V8z"/>
                                    </svg>
                                    <span>{{ $kursi }}</span>
                                </span>
                            </div>
                        </div>

                        {{-- Driver & Providers Info --}}
                        <div class="mt-2.5 flex items-center gap-3 text-xs">
                            <span class="font-medium text-slate-700">{{ $driverText }}</span>
                            <span class="text-[#0088cc] font-medium hover:underline cursor-pointer">{{ $providerText }}</span>
                        </div>
                    </div>

                    {{-- Right: Badge, Price & CTA Button --}}
                    <div class="shrink-0 flex flex-row sm:flex-col justify-between sm:justify-between items-end gap-1.5 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100" style="min-width: 155px;">
                        {{-- Top Right Promo Badge --}}
                        <div class="flex items-center gap-1 justify-end w-full" style="min-height: 18px;">
                            @if(!empty($package->label))
                            <span class="inline-flex items-center rounded-sm bg-[#d81b60] px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-white shadow-xs">
                                {{ $package->label }}
                            </span>
                            <span class="inline-flex items-center rounded-sm bg-slate-100 border border-slate-200 px-1 py-0.5 text-[9px] font-bold text-slate-600">
                                +1
                            </span>
                            @endif
                        </div>

                        {{-- Price Box --}}
                        <div class="text-left sm:text-right">
                            <div class="text-[11px] text-slate-400 font-normal leading-none mb-0.5">{{ $isEn ? 'From' : 'Dari' }}</div>
                            <div class="text-lg sm:text-xl font-extrabold text-[#ff5722] tracking-tight leading-tight">
                                Rp {{ number_format((int)$price, 0, ',', '.') }}
                                <span class="text-xs font-normal text-slate-500">/{{ $isEn ? 'day' : 'hari' }}</span>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ $detailUrl }}"
                           class="inline-flex items-center justify-center rounded-lg bg-[#ff5722] hover:bg-[#f4511e] active:scale-95 text-white font-bold text-xs sm:text-sm px-6 py-2 shadow-xs hover:shadow transition-all w-auto sm:w-36 text-center">
                            {{ $isEn ? 'Continue' : 'Lanjutkan' }}
                        </a>
                    </div>

                </div>
            </div>

            @empty
            <div class="card p-10 text-center">
                <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="car" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
                <h3 class="mt-4 text-lg font-extrabold text-slate-900">{{ $isEn ? 'No rental packages yet' : 'Belum ada paket rental' }}</h3>
                <p class="mt-2 text-slate-600">{{ $isEn ? 'Please check back later, or contact us for recommendations.' : 'Silakan cek kembali nanti, atau konsultasi untuk rekomendasi unit.' }}</p>
            </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        @if(method_exists($packages, 'links'))
        <div class="mt-8">
            {{ $packages->withQueryString()->links() }}
        </div>
        @endif
    </div>
</section>

@endsection