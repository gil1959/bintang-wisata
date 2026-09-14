@extends('layouts.front')
@php $isEn = app()->getLocale() === 'en'; @endphp
@section('title', $isEn ? 'Restoran' : 'Restoran')

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
                    {{ $siteSettings['restoran_hero_badge'] ?? 'Restoran' }}

                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
                    {{ $siteSettings['restoran_hero_title'] ?? 'Pilihan Restoran Terbaik untuk Perjalanan Anda' }}
                </h1>


                <p class="mt-3 max-w-2xl text-slate-600">
                    {{ $siteSettings['restoran_hero_desc'] ?? 'Hidangan lezat, tempat nyaman, dan pelayanan ramah.' }}
                </p>



                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> {{ $siteSettings['restoran_chip1'] ?? 'Higienis' }}</span>
                    <span class="pill pill-azure"><i data-lucide="wallet" class="w-4 h-4"></i> {{ $siteSettings['restoran_chip2'] ?? 'Halal' }}</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> {{ $siteSettings['restoran_chip3'] ?? 'Lezat' }}</span>
                    <span class="pill pill-azure"><i data-lucide="map" class="w-4 h-4"></i> {{ $siteSettings['restoran_chip4'] ?? 'Nyaman' }}</span>
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
                                <div class="font-extrabold text-slate-900">{{ $siteSettings['restoran_note_title'] ?? 'Catatan' }}</div>
                                <div class="text-sm text-slate-600 mt-1">
                                    {{ $siteSettings['restoran_note_desc'] ?? 'Klik “Booking Sekarang” untuk lihat menu.' }}
                                </div>

                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="utensils" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['restoran_note1_title'] ?? 'Lezat' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['restoran_note1_desc'] ?? 'Cita rasa khas' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['restoran_note2_title'] ?? 'Bersih' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['restoran_note2_desc'] ?? 'Tempat bersih & rapi' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['restoran_note3_title'] ?? 'Kapasitas' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['restoran_note3_desc'] ?? 'Luas untuk grup' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="clock" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['restoran_note4_title'] ?? 'Cepat' }}

                                </div>
                                <div class="text-xs text-slate-600 mt-1">{{ $siteSettings['restoran_note4_desc'] ?? 'Pelayanan ramah & cepat' }}
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
        <form method="GET" action="{{ route('restoran.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-10">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">{{ $isEn ? 'Search' : 'Pencarian' }}</label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ old('q', $q ?? request('q')) }}"
                        placeholder="{{ $isEn ? 'Example: Seafood, Nasi Goreng...' : 'Contoh: Seafood, Nasi Goreng...' }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pl-11 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
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

                <a class="btn btn-ghost w-full" href="{{ route('restoran.index') }}">
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


{{-- ================= GRID ================= --}}
<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 pb-14">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

            @forelse ($packages as $package)
            @php
            $cardTitle = $isEn ? ($package->title_en ?: $package->title) : $package->title;
            $cardFeatSrc = $isEn ? ($package->features_en ?: $package->features) : $package->features;
            $thumbUrl = \Illuminate\Support\Str::startsWith($package->thumbnail_path, ['http://', 'https://']) 
                ? $package->thumbnail_path 
                : ($package->thumbnail_path ? asset('storage/' . $package->thumbnail_path) : asset('images/default.jpg'));

            $kList = $package->keunggulan;
            $infoText = '';
            if (!empty($package->label) && is_string($package->label)) {
                $infoText = $package->label;
            } elseif (!empty($kList)) {
                if (is_array($kList)) {
                    $firstK = reset($kList);
                    $infoText = is_array($firstK) ? ($firstK['title'] ?? $firstK['name'] ?? reset($firstK)) : (string)$firstK;
                } else {
                    $infoText = (string)$kList;
                }
            } elseif (!empty($cardFeatSrc[0]['name'])) {
                $infoText = (string)$cardFeatSrc[0]['name'];
            } elseif (!empty($package->address)) {
                $infoText = (string)$package->address;
            } else {
                $infoText = $isEn ? 'Best Resto in town' : 'Resto Terbaik di Jogja';
            }
            @endphp
            <a href="{{ route('restoran.show', $package->slug) }}"
                class="group block bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">

                {{-- IMAGE --}}
                <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100">
                    <img
                        src="{{ $thumbUrl }}"
                        alt="{{ $package->title }}"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy">

                    {{-- badge kiri --}}
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-black/40 backdrop-blur-md px-3 py-1 text-xs font-semibold text-white shadow-sm">
                            <i data-lucide="utensils" class="w-3.5 h-3.5 text-[#0194F3]"></i>
                            <span>{{ $isEn ? 'Restaurant' : 'Restoran' }}</span>
                        </span>
                    </div>

                    {{-- label kanan --}}
                    @if(!empty($package->label))
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-2.5 py-0.5 text-[11px] font-bold text-slate-900 shadow-sm">
                            {{ $package->label }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- CONTENT --}}
                <div class="p-4">
                    <div class="text-[17px] font-bold text-[#0194F3] group-hover:text-[#007fd1] line-clamp-1 leading-snug transition-colors">
                        {{ $cardTitle }}
                    </div>

                    <div class="mt-1 text-sm font-medium text-slate-600">
                        {{ $isEn ? 'Start ' : 'Mulai ' }}<span class="font-bold text-rose-500">{{ $isEn ? 'Reservation' : 'Reservasi' }}</span>{{ $isEn ? ' Resto' : ' Resto' }}
                    </div>

                    <div class="border-t border-slate-100 mt-3 pt-3">
                        {{-- Info Line --}}
                        <div class="flex items-center gap-2 text-[13px] font-medium text-slate-600">
                            <i data-lucide="info" class="w-4 h-4 text-[#0194F3] shrink-0"></i>
                            <span class="line-clamp-1">
                                {{ $infoText }}
                            </span>
                        </div>

                        {{-- Action Button --}}
                        <div class="mt-3">
                            <div class="w-full rounded-xl py-2.5 px-4 font-bold text-sm text-white text-center bg-[#0194F3] hover:bg-[#007fd1] shadow-sm hover:shadow transition flex items-center justify-center">
                                {{ $isEn ? 'View Menu & Booking' : 'Lihat Menu & Booking' }}
                            </div>
                        </div>
                    </div>
                </div>
            </a>


            @empty
            <div class="col-span-full">
                <div class="card p-10 text-center">
                    <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
                        style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="utensils" class="w-6 h-6" style="color:#0194F3;"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-extrabold text-slate-900">{{ $isEn ? 'No restoran packages yet' : 'Belum ada paket restoran' }}</h3>
                    <p class="mt-2 text-slate-600">{{ $isEn ? 'Please check back later, or contact us for recommendations.' : 'Silakan cek kembali nanti, atau konsultasi untuk rekomendasi.' }}</p>

                </div>
            </div>
            @endforelse

        </div>

        {{-- Pagination (kalau ada) --}}
        @if(method_exists($packages, 'links'))
        <div class="mt-10">
            {{ $packages->withQueryString()->links() }}
        </div>
        @endif
    </div>
</section>

@endsection