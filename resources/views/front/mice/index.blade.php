@extends('layouts.front')
@section('title', 'Paket MICE - Bintang Wisata')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>

    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="miceHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22"/>
                <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#miceHeroGlow)"/>
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round"/>
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">

            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['mice_hero_badge'] ?? 'Paket MICE' }}
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $siteSettings['mice_hero_title'] ?? 'Solusi Paket MICE untuk Event Perusahaan Anda' }}
                </h1>

                <p class="mt-3 text-slate-600 max-w-2xl">
                    {{ $siteSettings['mice_hero_desc'] ?? 'Meetings, Incentives, Conferences, and Exhibitions. Pilih paket, lihat detail, dan lanjut checkout dengan mudah.' }}
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#mice-list" class="btn btn-primary">
                        <i data-lucide="briefcase" class="w-4 h-4"></i>
                        {{ $siteSettings['mice_cta_button'] ?? 'Lihat Paket' }}
                    </a>

                   
                </div>
            </div>

            {{-- TIPS (4 BOX) --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="calendar-check" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $siteSettings['mice_tip1_title'] ?? 'Event Ready' }}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $siteSettings['mice_tip1_desc'] ?? 'Paket siap untuk meeting, conference, dan exhibition.' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="badge-check" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $siteSettings['mice_tip2_title'] ?? 'Terpercaya' }}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $siteSettings['mice_tip2_desc'] ?? 'Pilihan paket jelas, detail lengkap, mudah dipilih.' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="wallet" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $siteSettings['mice_tip3_title'] ?? 'Harga Fleksibel' }}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $siteSettings['mice_tip3_desc'] ?? 'Tier harga Domestik & WNA bisa multi baris sesuai kebutuhan.' }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                        <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $siteSettings['mice_tip4_title'] ?? 'Support' }}
                        </div>
                        <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                            {{ $siteSettings['mice_tip4_desc'] ?? 'Bisa konsultasi kebutuhan event dan itinerary.' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- wave divider (sama pola index lain) --}}
    <svg class="block w-full" viewBox="0 0 1440 60" fill="none" aria-hidden="true">
        <path d="M0 40C240 10 480 10 720 40C960 70 1200 70 1440 40V60H0V40Z" fill="#F8FAFC"/>
    </svg>
</section>

{{-- ================= FILTER BAR ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('mice.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $siteSettings['mice_filter_search_label'] ?? 'Pencarian' }}
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{ $siteSettings['mice_filter_search_placeholder'] ?? 'Contoh: Meeting, Conference, Exhibition...' }}"
                        class="w-full rounded-xl border-slate-200 pl-11"
                    >
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            {{-- CATEGORY --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $siteSettings['mice_filter_cat_label'] ?? 'Kategori' }}
                </label>
                <select name="category"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SORT --}}
            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Urutkan</label>
                <select name="sort"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <option value="">Default</option>
                    <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ request('sort')=='price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_high" {{ request('sort')=='price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                </select>
            </div>

            <div class="md:col-span-12 flex flex-wrap gap-3 justify-end">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>

                <a href="{{ route('mice.index') }}" class="btn btn-ghost border-slate-200 text-slate-700 hover:bg-slate-50">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>
</section>

{{-- ================= PACKAGES GRID ================= --}}
<section id="mice-list" class="max-w-7xl mx-auto px-4 pt-10 pb-16">
    @if($packages->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
            @foreach($packages as $package)
                @php
                    // min price dari tiers domestic (kalau ada)
                    $minDomestic = ($package->tiers ?? collect())->where('type','domestic')->min('price');
                    $ratingValue = $package->rating_value ?? 0;
                    $ratingCount = $package->rating_count ?? 0;
                @endphp

                <a href="{{ route('mice.show', $package) }}"
   class="group card overflow-hidden">

    {{-- thumbnail (SAMA TOUR) --}}
    <div class="relative h-48 overflow-hidden bg-slate-100">
        @if($package->thumbnail_path)
            <img
                src="{{ asset('storage/' . $package->thumbnail_path) }}"
                alt="{{ $package->title }}"
                class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
            >
        @else
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
        @endif

        {{-- label kanan atas (SAMA TOUR) --}}
        @if(!empty($package->label))
            <div class="absolute top-3 right-3">
                <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
                    {{ $package->label }}
                </span>
            </div>
        @endif

        {{-- badge kategori kiri atas (SAMA TOUR) --}}
        <div class="absolute top-3 left-3">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ optional($package->category)->name ?? 'MICE' }}
            </span>
        </div>

        {{-- svg decoration (SAMA TOUR) --}}
        <svg class="absolute bottom-2 right-2 w-24 h-24 opacity-70" viewBox="0 0 120 120" fill="none" aria-hidden="true">
            <path d="M18 80c20-26 40-38 60-38 16 0 28 6 40 16" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round"/>
            <path d="M88 26l8 8-8 8" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    {{-- content (SAMA TOUR) --}}
    <div class="p-5">
        <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-azure transition line-clamp-2">
            {{ $package->title }}
        </h3>

        <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-slate-600">
            @if($package->destination)
                <span class="inline-flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4" style="color:#0194F3;"></i>
                    {{ $package->destination }}
                </span>
            @endif

            @if($package->duration_text)
                <span class="inline-flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4" style="color:#0194F3;"></i>
                    {{ $package->duration_text }}
                </span>
            @endif

            {{-- rating (SAMA TOUR: 1 star + text) --}}
            <div class="flex items-center gap-2 text-sm text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="#FBBF24"
                     class="w-4 h-4">
                    <path d="M12 17.27L18.18 21l-1.64-7.03
                             L22 9.24l-7.19-.61L12 2
                             9.19 8.63 2 9.24l5.46 4.73
                             L5.82 21z"/>
                </svg>

                <span class="font-semibold">
                    {{ number_format((float)($package->rating_value ?? 5), 1) }}/5
                </span>
                <span class="text-slate-500">
                    · {{ (int)($package->rating_count ?? 0) }} ulasan
                </span>
            </div>
        </div>

        {{-- optional desc (kalau mau sama Tour, biarin kayak ini) --}}
        @if(!empty($package->description))
            <p class="mt-3 text-sm text-slate-600 line-clamp-2">
                {{ \Illuminate\Support\Str::limit(strip_tags($package->description), 120) }}
            </p>
        @endif

        <div class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold" style="color:#0194F3;">
            Lihat Detail
            <span class="translate-x-0 group-hover:translate-x-0.5 transition">→</span>
        </div>
    </div>
</a>

            @endforeach
        </div>

        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    @else
        <div class="card p-10 text-center" data-aos="fade-up">
            <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center"
                 style="background: rgba(1,148,243,0.10);">
                <i data-lucide="search-x" class="w-7 h-7" style="color:#0194F3;"></i>
            </div>
            <h3 class="mt-4 text-lg font-extrabold text-slate-900">Paket tidak ditemukan</h3>
            <p class="mt-2 text-slate-600">
                Silakan ubah kata kunci atau pilih kategori lain untuk melihat paket yang tersedia.
            </p>
            <div class="mt-6">
                <a href="{{ route('mice.index') }}" class="btn btn-primary">Reset Pencarian</a>
            </div>
        </div>
    @endif
</section>

@endsection
