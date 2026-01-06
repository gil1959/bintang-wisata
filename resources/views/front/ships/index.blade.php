@extends('layouts.front')
@section('title', 'Sewa Kapal')

@section('content')

{{-- ================= PAGE HEADER (mirip Tours) ================= --}}
<section class="relative overflow-hidden bg-white">
  <div class="absolute inset-0 travel-grid opacity-70"></div>

  <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
    <defs>
      <radialGradient id="shipsHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
        <stop stop-color="#0194F3" stop-opacity="0.22"/>
        <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
      </radialGradient>
    </defs>
    <circle cx="310" cy="290" r="280" fill="url(#shipsHeroGlow)"/>
    <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round"/>
    <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round"/>
  </svg>

  <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
    <div class="grid gap-8 lg:grid-cols-12 items-center">
      <div class="lg:col-span-7" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
             style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
          <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
          {{ $siteSettings['ship_hero_badge'] ?? 'Sewa Kapal' }}
        </div>

        <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
           {{ $siteSettings['ship_hero_title'] ?? 'Temukan Paket Sewa Kapal yang Sesuai Kebutuhan Anda' }}
        </h1>

        <p class="mt-3 text-slate-600 max-w-2xl">
           {{ $siteSettings['ship_hero_desc'] ?? 'Gunakan pencarian dan filter kategori untuk menyaring paket yang tersedia.' }}
        </p>

        <div class="mt-6 flex flex-wrap gap-2">
          <span class="pill pill-azure"><i data-lucide="search" class="w-4 h-4"></i> Pencarian</span>
          <span class="pill pill-azure"><i data-lucide="tag" class="w-4 h-4"></i> Kategori</span>
          <span class="pill pill-azure"><i data-lucide="arrow-up-down" class="w-4 h-4"></i> Urutkan</span>
          <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> Transparan</span>
        </div>
      </div>

      {{-- right tips card (mirip tours) --}}
      <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
          <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>

          <div class="relative">
            <div class="flex items-start gap-3">
              <div class="icon-badge shrink-0">
                <i data-lucide="anchor" class="w-5 h-5"></i>
              </div>

              <div>
                <div class="font-extrabold text-slate-900"> {{ $siteSettings['ship_tips_title'] ?? 'Tips Cepat' }}</div>
                <div class="text-sm text-slate-600 mt-0.5">
                 {{ $siteSettings['ship_tips_desc'] ?? 'Cek detail paket untuk harga weekday/weekend & fitur yang tersedia.' }}
                </div>
              </div>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                  <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                  {{ $siteSettings['ship_tip1_title'] ?? 'Weekday/Weekend' }}
                </div>
                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                  {{ $siteSettings['ship_tip1_desc'] ?? 'Harga berbeda sesuai hari' }}
                </div>
              </div>

              <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                  <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                  {{ $siteSettings['ship_tip2_title'] ?? 'Untuk Grup' }}
                </div>
                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                  {{ $siteSettings['ship_tip2_desc'] ?? 'Cocok keluarga/rombongan' }}
                </div>
              </div>

              <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                  <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                  {{ $siteSettings['ship_tip3_title'] ?? 'Rekomendasi' }}
                </div>
                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                  {{ $siteSettings['ship_tip3_desc'] ?? 'Paket favorit pelanggan' }}
                </div>
              </div>

              <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                  <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                  {{ $siteSettings['ship_tip4_title'] ?? 'Support' }}
                </div>
                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                  {{ $siteSettings['ship_tip4_desc'] ?? 'Bisa konsultasi sebelum booking' }}
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- wave divider (mirip tours) --}}
  <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
    <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC"/>
  </svg>
</section>

{{-- ================= FILTER BAR (mirip Tours) ================= --}}
<section class="max-w-7xl mx-auto px-4">
  <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
    <form method="GET" action="{{ route('ship.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

      {{-- SEARCH --}}
      <div class="md:col-span-5">
        <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
        <div class="relative">
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Contoh: Kapal, Paket, Promo..."
            class="w-full rounded-xl border-slate-200 pl-11"
          >
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            <i data-lucide="search" class="w-5 h-5"></i>
          </span>
        </div>
      </div>

      {{-- CATEGORY --}}
      <div class="md:col-span-3">
        <label class="block text-sm font-extrabold text-slate-700 mb-2">Kategori</label>
        <select name="category_id" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
          <option value="">Semua Kategori</option>
          @foreach(($categories ?? []) as $c)
            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- SORT --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-extrabold text-slate-700 mb-2">Urutkan</label>
        <select name="sort" class="w-full rounded-xl border-slate-200">
          <option value="title_asc" @selected(request('sort','latest') === 'title_asc')>Nama (A-Z)</option>
          <option value="latest" @selected(request('sort','latest') === 'latest')>Terbaru</option>
          <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
        </select>
      </div>

      {{-- ACTIONS --}}
      <div class="md:col-span-2 flex gap-3">
        <button class="btn btn-primary w-full" type="submit">
          <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
          Terapkan
        </button>

        <a class="btn btn-ghost w-full" href="{{ route('ship.index') }}">
          Reset
        </a>
      </div>
    </form>

    {{-- Active filters summary --}}
    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
      <span class="font-extrabold text-slate-700">Filter aktif:</span>

      @if(request('q'))
        <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
          <i data-lucide="type" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
          Kata kunci: <span class="font-extrabold">{{ request('q') }}</span>
        </span>
      @endif

      @if(request('category_id'))
        @php
          $activeCat = collect($categories ?? [])->firstWhere('id', (int) request('category_id'));
        @endphp
        <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
          <i data-lucide="tag" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
          Kategori: <span class="font-extrabold">{{ $activeCat?->name ?? '—' }}</span>
        </span>
      @endif

      @if(!request('q') && !request('category_id'))
        <span class="text-slate-500">Tidak ada</span>
      @endif
    </div>

  </div>
</section>

{{-- ================= LIST (kasih GAP kayak Tours: py-12) ================= --}}
<section class="max-w-7xl mx-auto px-4 py-12 lg:py-14">
  <div class="flex items-end justify-between gap-4 mb-6" data-aos="fade-up">
    <div>
      <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900">
        Daftar Private Charter
      </h2>
      <p class="mt-1 text-slate-600 text-sm">
        Menampilkan {{ $packages->total() }} paket.
      </p>
    </div>
  </div>

  @if($packages->count())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
      @foreach($packages as $p)
        @php
          // konsisten dengan Tours: rating_value & rating_count
          $ratingValue = (float) ($p->rating_value ?? 5);
          $ratingCount = (int) ($p->rating_count ?? 0);
        @endphp

        <a href="{{ route('ship.show', $p->slug) }}" class="group card overflow-hidden">

          {{-- thumbnail --}}
          <div class="relative h-48 overflow-hidden bg-slate-100">
            <img
              src="{{ $p->thumbnail_path ? asset('storage/'.$p->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Sewa+Kapal' }}"
              alt="{{ $p->title }}"
              class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
            >

            {{-- label (top-right) sama kayak tours --}}
            @if(!empty($p->label))
              <div class="absolute top-3 right-3">
                <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
                  {{ $p->label }}
                </span>
              </div>
            @endif

            {{-- badge kategori (top-left) sama kayak tours --}}
            <div class="absolute top-3 left-3">
              <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                {{ $p->category?->name ?? 'Kapal' }}
              </span>
            </div>

            {{-- dekor svg (biar gak kosong) --}}
            <svg class="absolute bottom-2 right-2 w-24 h-24 opacity-70" viewBox="0 0 120 120" fill="none" aria-hidden="true">
              <path d="M18 80c20-26 40-38 60-38 16 0 28 6 40 16" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round"/>
              <path d="M88 26l8 8-8 8" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>

          {{-- content --}}
          <div class="p-5">
            <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-azure transition line-clamp-2">
              {{ $p->title }}
            </h3>

            <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-slate-600">
              {{-- rating style: PERSIS kayak tours (1 star svg + text) --}}
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
                  {{ number_format($ratingValue, 1) }}/5
                </span>
                <span class="text-slate-500">
                  · {{ $ratingCount }} ulasan
                </span>
              </div>
            </div>

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
      <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
           style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
        <i data-lucide="search-x" class="w-6 h-6" style="color:#0194F3;"></i>
      </div>
      <h3 class="mt-4 text-lg font-extrabold text-slate-900">Paket tidak ditemukan</h3>
      <p class="mt-2 text-slate-600">
        Silakan ubah kata kunci atau pilih kategori lain untuk melihat paket yang tersedia.
      </p>
      <div class="mt-6">
        <a href="{{ route('ship.index') }}" class="btn btn-primary">Reset Pencarian</a>
      </div>
    </div>
  @endif
</section>

@endsection
