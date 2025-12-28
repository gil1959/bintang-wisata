@extends('layouts.front')
@section('title', 'Paket Umrah - Bintang Wisata')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>
    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="umrahHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22"/>
                <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#umrahHeroGlow)"/>
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round"/>
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['umrah_hero_badge'] ?? 'Paket Umrah' }}
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $siteSettings['umrah_hero_title'] ?? 'Temukan Paket Umrah yang Sesuai Kebutuhan Anda' }}
                </h1>

                <p class="mt-3 text-slate-600 max-w-2xl">
                    {{ $siteSettings['umrah_hero_desc'] ?? 'Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.' }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="map-pin" class="w-4 h-4"></i> {{ $siteSettings['umrah_filter_dest_label'] ?? 'Destinasi' }}</span>
                    <span class="pill pill-azure"><i data-lucide="tag" class="w-4 h-4"></i> {{ $siteSettings['umrah_filter_cat_label'] ?? 'Kategori' }}</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> {{ $siteSettings['umrah_filter_dur_label'] ?? 'Durasi' }}</span>
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> {{ $siteSettings['umrah_filter_trans_label'] ?? 'Transparan' }}</span>
                </div>
            </div>

            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start gap-3">
                            <div class="icon-badge shrink-0">
                                <i data-lucide="compass" class="w-5 h-5"></i>
                            </div>

                            <div>
                                <div class="font-extrabold text-slate-900">
                                    {{ $siteSettings['umrah_tips_title'] ?? 'Tips Cepat' }}
                                </div>
                                <div class="text-sm text-slate-600 mt-0.5">
                                    {{ $siteSettings['umrah_tips_desc'] ?? 'Gunakan kata kunci destinasi untuk hasil lebih akurat.' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['umrah_tip1_title'] ?? 'Rekomendasi' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['umrah_tip1_desc'] ?? 'Paket favorit pelanggan' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['umrah_tip2_title'] ?? 'Itinerary' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['umrah_tip2_desc'] ?? 'Alur perjalanan jelas' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['umrah_tip3_title'] ?? 'Grup' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['umrah_tip3_desc'] ?? 'Cocok untuk rombongan' }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['umrah_tip4_title'] ?? 'Support' }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['umrah_tip4_desc'] ?? 'Bisa konsultasi trip' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
        <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC"/>
    </svg>
</section>

{{-- ================= FILTER BAR ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('umrah.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Contoh: Ramadhan, Reguler, Plus..."
                        class="w-full rounded-xl border-slate-200 pl-11"
                    >
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Kategori</label>
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

            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Urutkan</label>
                <select
                    name="sort"
                    class="w-full rounded-xl border-slate-200"
                >
                    <option value="title_asc" @selected(request('sort','title_asc') === 'title_asc')>Nama (A-Z)</option>
                    <option value="newest" @selected(request('sort') === 'newest')>Terbaru</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
                </select>
            </div>

            <div class="md:col-span-2 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    Terapkan
                </button>

                <a class="btn btn-ghost w-full"
                   href="{{ route('umrah.index') }}">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-700">Filter aktif:</span>

            @if(request('q'))
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                    <i data-lucide="type" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                    Kata kunci: <span class="font-extrabold">{{ request('q') }}</span>
                </span>
            @endif

            @if(request('category'))
                @php
                    $activeCat = $categories->firstWhere('id', (int) request('category'));
                @endphp
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                    <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                    Kategori: <span class="font-extrabold">{{ $activeCat?->name ?? '—' }}</span>
                </span>
            @endif

            @if(!request('q') && !request('category'))
                <span class="text-slate-500">Tidak ada</span>
            @endif
        </div>
    </div>
</section>

{{-- ================= LIST ================= --}}
<section class="max-w-7xl mx-auto px-4 py-12 lg:py-14">
    <div class="flex items-end justify-between gap-4 mb-6" data-aos="fade-up">
        <div>
            <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900">
                Daftar Paket Umrah
            </h2>
            <p class="mt-1 text-slate-600 text-sm">
                Menampilkan {{ $packages->total() }} paket.
            </p>
        </div>
    </div>

    @if($packages->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
            @foreach($packages as $package)
                <a href="{{ route('umrah.show', $package) }}"
                   class="group card overflow-hidden hover:shadow-xl transition">
                    <div class="relative">
                        @if($package->thumbnail_path)
                            <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                                 class="w-full h-52 object-cover"
                                 alt="{{ $package->title }}">
                        @else
                            <div class="w-full h-52 bg-slate-100"></div>
                        @endif

                        @if($package->label)
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-extrabold text-white"
                                  style="background:#0194F3;">
                                {{ $package->label }}
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-extrabold text-slate-900 group-hover:text-slate-950 leading-snug">
                            {{ $package->title }}
                        </h3>
@php
  $ratingValue = (float)($package->rating_value ?? 0);
  $ratingCount = (int)($package->rating_count ?? 0);
  $rounded = (int) round($ratingValue);
@endphp

<div class="mt-2 flex items-center gap-2">
  <div class="flex items-center gap-0.5 text-amber-500">
    @for($i=1; $i<=5; $i++)
      <svg class="w-4 h-4" viewBox="0 0 20 20" fill="{{ $i <= $rounded ? 'currentColor' : 'none' }}" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.447a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.539 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.783.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.075 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/>
      </svg>
    @endfor
  </div>

  <div class="text-xs text-slate-600">
    <span class="font-bold">{{ number_format($ratingValue, 1) }}</span>
    <span class="text-slate-400">({{ $ratingCount }})</span>
  </div>
</div>

                        <div class="mt-2 text-sm text-slate-600 flex flex-wrap items-center gap-4">
                            @if($package->duration_text)
                                <span class="flex items-center gap-2">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                                    <span>{{ $package->duration_text }}</span>
                                </span>
                            @endif

                            @if($package->destination)
                                <span class="flex items-center gap-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-500"></i>
                                    <span>{{ $package->destination }}</span>
                                </span>
                            @endif
                        </div>

                        @php
                            $minPrice = $package->tiers?->min('price');
                        @endphp

                        <div class="mt-4 flex items-end justify-between">
                            <div class="text-xs text-slate-500">Mulai dari</div>
                            <div class="text-lg font-extrabold" style="color:#0194F3;">
                                @if($minPrice !== null)
                                    Rp {{ number_format((int)$minPrice, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    @else
        <div class="card p-6 text-center text-slate-600">
            Belum ada paket umrah.
        </div>
    @endif
</section>

@endsection
