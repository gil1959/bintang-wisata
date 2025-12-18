@extends('layouts.front')
@section('title', 'Paket Tour - Bintang Wisata')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>
    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="toursHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22"/>
                <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#toursHeroGlow)"/>
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round"/>
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    Paket Tour
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    Temukan Paket Tour yang Sesuai Kebutuhan Anda
                </h1>

                <p class="mt-3 text-slate-600 max-w-2xl">
                    Gunakan pencarian dan filter untuk menyaring paket berdasarkan destinasi maupun kategori.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="map-pin" class="w-4 h-4"></i> Destinasi</span>
                    <span class="pill pill-azure"><i data-lucide="tag" class="w-4 h-4"></i> Kategori</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> Durasi</span>
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> Transparan</span>
                </div>
            </div>

            {{-- right illustration --}}
            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="compass" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900">Tips Cepat</div>
                                <div class="text-sm text-slate-600 mt-0.5">Gunakan kata kunci destinasi untuk hasil lebih akurat.</div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Rekomendasi
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Paket favorit pelanggan</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Itinerary
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Alur perjalanan jelas</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Grup
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Cocok untuk rombongan</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="headphones" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Support
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Bisa konsultasi trip</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- wave divider --}}
    <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
        <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC"/>
    </svg>
</section>

{{-- ================= FILTER BAR ================= --}}
<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">
        <form method="GET" action="{{ route('tours.index') }}" class="grid gap-4 md:grid-cols-12 items-end">

            {{-- SEARCH --}}
            <div class="md:col-span-5">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Contoh: Bali, Lombok, Jepang..."
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

            {{-- ACTIONS --}}
            <div class="md:col-span-2 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    Terapkan
                </button>

                <a class="btn btn-ghost w-full"
                   href="{{ route('tours.index') }}">
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
                Daftar Paket Tour
            </h2>
            <p class="mt-1 text-slate-600 text-sm">
                Menampilkan {{ $packages->total() }} paket.
            </p>
        </div>
    </div>

    @if($packages->count())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
            @foreach($packages as $package)
                <a href="{{ route('tour.show', $package) }}"
                   class="group card overflow-hidden">

                    {{-- thumbnail --}}
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
                        @if(!empty($package->label))
  <div class="absolute top-3 right-3">
    <span class="inline-flex items-center rounded-full bg-white/90 backdrop-blur border border-white/60 px-3 py-1 text-xs font-extrabold text-slate-900 shadow">
      {{ $package->label }}
    </span>
  </div>
@endif


                        {{-- badge kategori --}}
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                                <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                                {{ optional($package->category)->name ?? 'Tour' }}
                            </span>
                        </div>

                        {{-- svg route decoration --}}
                        <svg class="absolute bottom-2 right-2 w-24 h-24 opacity-70" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                            <path d="M18 80c20-26 40-38 60-38 16 0 28 6 40 16" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round"/>
                            <path d="M88 26l8 8-8 8" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    {{-- content --}}
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
                        </div>

                        @if(!empty($package->long_description))
                            <p class="mt-3 text-sm text-slate-600 line-clamp-2">
                                {{ \Illuminate\Support\Str::limit(strip_tags($package->long_description), 120) }}
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
        {{-- empty state --}}
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
                <a href="{{ route('tours.index') }}" class="btn btn-primary">Reset Pencarian</a>
            </div>
        </div>
    @endif
</section>

{{-- ================= CTA ================= --}}
<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="rounded-3xl text-white p-8 lg:p-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative overflow-hidden"
         style="background: linear-gradient(90deg, #0194F3 0%, rgba(1,148,243,0.70) 100%);"
         data-aos="fade-up">
        <div class="absolute inset-0 opacity-60 pointer-events-none">
            <svg class="absolute -top-10 -right-10 w-72 h-72" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                <circle cx="150" cy="150" r="120" fill="#FFFFFF" fill-opacity="0.10"/>
                <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round"/>
                <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#FFFFFF" stroke-opacity="0.18" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="max-w-2xl relative">
            <h2 class="text-2xl font-extrabold">Membutuhkan Rekomendasi Paket yang Tepat?</h2>
            <p class="mt-2 text-white/90">
                Tim kami siap membantu memilih itinerary yang sesuai dengan waktu, preferensi, dan anggaran Anda.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 relative">
            <a href="#" class="btn bg-white text-slate-900 hover:bg-white/90">
                <i data-lucide="messages-square" class="w-4 h-4"></i>
                Konsultasi
            </a>
            <a href="{{ route('rentcar.index') }}" class="btn btn-ghost border-white/30 text-white hover:bg-white/10">
                <i data-lucide="car" class="w-4 h-4"></i>
                Lihat Rental
            </a>
        </div>
    </div>
</section>

@endsection
