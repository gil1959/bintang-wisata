@extends('layouts.front')
@section('title', __('front.home.page_title'))


@section('content')

{{-- ================= HERO ================= --}}
<section class="relative min-h-[88vh] flex items-center justify-center text-center overflow-hidden">
    {{-- background --}}
    <div class="absolute inset-0">
        <img
            src="{{ $siteSettings['hero_image'] ?? '' }}"
            alt="Bintang Wisata"
            class="h-full w-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/45 to-slate-950/15"></div>

        {{-- decorative travel SVG overlay --}}
        <div class="absolute inset-0 opacity-60">
            <svg class="absolute -top-24 -right-24 w-[520px] h-[520px]" viewBox="0 0 600 600" fill="none" aria-hidden="true">
                <defs>
                    <radialGradient id="rg1" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(300 300) rotate(90) scale(280)">
                        <stop stop-color="#0194F3" stop-opacity="0.35"/>
                        <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
                    </radialGradient>
                </defs>
                <circle cx="300" cy="300" r="280" fill="url(#rg1)"/>
                <path d="M120 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#FFFFFF" stroke-opacity="0.30" stroke-width="2" stroke-linecap="round"/>
                <path d="M155 380c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round"/>
            </svg>

            <svg class="absolute -bottom-14 -left-10 w-[520px] h-[520px]" viewBox="0 0 600 600" fill="none" aria-hidden="true">
                <defs>
                    <radialGradient id="rg2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(280 320) rotate(90) scale(280)">
                        <stop stop-color="#0194F3" stop-opacity="0.28"/>
                        <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
                    </radialGradient>
                </defs>
                <circle cx="280" cy="320" r="280" fill="url(#rg2)"/>
                <path d="M140 260c60 40 110 60 170 60 90 0 155-45 250-125" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    {{-- content --}}
    <div class="relative max-w-4xl mx-auto px-4" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-5 py-2 text-white text-xs tracking-wide">
            <span class="h-2 w-2 rounded-full bg-white/80"></span>
           {{ __('front.home.badge') }}

        </div>

        <h1 class="mt-6 text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight text-white">
            {{ $siteSettings['hero_title'] ?? 'Perjalanan Nyaman & Terpercaya' }}
        </h1>

        <p class="mt-5 text-base md:text-lg text-white/90 max-w-2xl mx-auto">
            {{ $siteSettings['hero_subtitle'] ?? 'Kami membantu Anda merencanakan perjalanan dengan layanan profesional dan harga transparan.' }}
        </p>

       

        <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('tours.index') }}" class="btn btn-primary px-8 py-3">
                <i data-lucide="map" class="w-5 h-5"></i>
             {{ __('front.home.cta_view_tours') }}

            </a>
            <a href="{{ route('rentcar.index') }}" class="btn btn-ghost px-8 py-3">
                <i data-lucide="car" class="w-5 h-5"></i>
                {{ __('front.home.cta_rental') }}

            </a>
        </div>

       
    </div>
</section>

{{-- ================= STATS / HIGHLIGHTS ================= --}}
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-12 lg:py-16">
        <div class="grid gap-6 lg:grid-cols-12 items-center" data-aos="fade-up">
            <div class="lg:col-span-5">
                <div class="pill pill-azure">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    {{ $siteSettings['home_highlight_label'] ?? 'Kenapa layanan kami beda' }}
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $siteSettings['home_highlight_title'] ?? 'Detail, rapi, dan fokus ke pengalaman perjalanan.' }}
                </h2>

                <p class="mt-3 text-slate-600">
                    {{ $siteSettings['home_highlight_desc'] ?? 'Kami bikin trip terasa “beres” dari awal: informasi jelas, itinerary enak diikuti, dan tim responsif.' }}
                </p>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="badge-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left1_title'] ?? 'Harga Transparan' }}
                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left1_desc'] ?? 'Tanpa biaya tersembunyi' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="route" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left2_title'] ?? 'Itinerary Jelas' }}
                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left2_desc'] ?? 'Rute & waktu terstruktur' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left3_title'] ?? 'Booking Cepat' }}
                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left3_desc'] ?? 'Form ringkas & jelas' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-5">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="messages-square" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-slate-900 font-extrabold">
                                    {{ $siteSettings['home_highlight_left4_title'] ?? 'Support Aktif' }}
                                </div>
                                <div class="text-slate-600 text-xs mt-0.5">
                                    {{ $siteSettings['home_highlight_left4_desc'] ?? 'Bisa konsultasi trip' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- illustration panel --}}
            <div class="lg:col-span-7">
                <div class="relative rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 travel-dots p-6 lg:p-8 shadow-soft">
                    <div class="absolute inset-0 pointer-events-none">
                        <svg class="absolute -top-10 -right-8 w-64 h-64 opacity-70" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                            <path d="M40 170c30-70 95-110 170-110 20 0 40 3 60 9" stroke="#0194F3" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round"/>
                            <path d="M60 200c45-55 90-80 145-80 35 0 65 10 95 26" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round"/>
                            <path d="M100 240l18-22 18 22" stroke="#0194F3" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2 relative">
                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right1_title'] ?? 'Destinasi Favorit' }}
                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right1_desc'] ?? 'Bali, Lombok, Jogja, Bandung, sampai destinasi luar negeri (tergantung paket).' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right2_title'] ?? 'Cocok untuk Grup' }}
                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right2_desc'] ?? 'Trip keluarga, kantor, komunitas — tinggal sesuaikan kebutuhan.' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right3_title'] ?? 'Budget Friendly' }}
                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right3_desc'] ?? 'Paket fleksibel dengan informasi harga jelas sejak awal.' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-6">
                            <div class="flex items-start gap-4">
                                <div class="icon-badge">
                                    <i data-lucide="camera" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900">
                                        {{ $siteSettings['home_highlight_right4_title'] ?? 'Spot Wisata Terbaik' }}
                                    </div>
                                    <div class="text-sm text-slate-600 mt-1">
                                        {{ $siteSettings['home_highlight_right4_desc'] ?? 'Fokus pengalaman: view bagus, tempat ikonik, dan alur perjalanan nyaman.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('tours.index') }}" class="btn btn-primary">
                            <i data-lucide="compass" class="w-5 h-5"></i>
                            {{ $siteSettings['home_highlight_cta_primary_text'] ?? 'Mulai Jelajah Paket' }}
                        </a>
                        <a href="{{ route('rentcar.index') }}" class="btn btn-ghost">
                            <i data-lucide="car-front" class="w-5 h-5"></i>
                            {{ $siteSettings['home_highlight_cta_secondary_text'] ?? 'Cek Armada Rental' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



{{-- ================= WHY US ================= --}}
<section class="bg-slate-50">
   @include('front.partials.home-promo-tours')

        {{-- ================= INSPIRASI DESTINASI ================= --}}
<div class="mt-10 rounded-3xl border border-slate-200 bg-white p-6 lg:p-8 travel-grid shadow-soft" data-aos="fade-up" data-aos-delay="140">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <div class="pill pill-azure">
                <i data-lucide="map" class="w-4 h-4"></i>
                Inspirasi destinasi
            </div>
            <div class="mt-3 text-xl lg:text-2xl font-extrabold text-slate-900">
                Nuansa wisata yang siap kamu jelajahi
            </div>
            <p class="mt-2 text-slate-600">
                Pilih destinasi favorit, lalu lihat daftar paket tour sesuai kategori.
            </p>
        </div>

        <div class="hidden lg:block text-sm text-slate-500">
            Total: <span class="font-extrabold text-slate-900">{{ $inspirations->count() }}</span>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($inspirations as $it)
            @php
                $href = $it->tour_category_id
                    ? route('tours.index', ['category' => $it->tour_category_id])
                    : route('tours.index');

                $img = $it->image_path ? asset('storage/'.$it->image_path) : null;
            @endphp

            <a href="{{ $href }}" class="group block rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition bg-white">
                <div class="relative aspect-[4/5]">
                    @if($img)
                        <img
                            src="{{ $img }}"
                            alt="{{ $it->title }}"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.03] transition duration-500"
                        >
                    @else
                        <div class="absolute inset-0 bg-slate-200"></div>
                        <div class="absolute inset-0 grid place-items-center text-slate-500 text-xs">
                            No Image
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                    <div class="absolute left-3 bottom-10 text-[10px] font-extrabold tracking-wider text-white/90">
                        PAKET WISATA
                    </div>

                    <div class="absolute left-3 bottom-3 right-3 text-white">
                        <div class="text-lg font-black leading-tight uppercase">
                            {{ $it->title }}
                        </div>

                        <div class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold bg-orange-500 text-white">
                            Detail
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-2 md:col-span-4 rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-slate-600">
                Belum ada inspirasi destinasi. Tambahkan lewat Admin Panel.
            </div>
        @endforelse
    </div>
</div>


    </div>
</section>

{{-- ================= FEATURED TOURS ================= --}}
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
        <div class="flex items-end justify-between gap-4" data-aos="fade-up">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                    Rekomendasi
                </div>
                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    Paket Tour Pilihan
                </h2>
                <p class="mt-2 text-slate-600">
                    Rekomendasi perjalanan yang paling diminati oleh pelanggan kami.
                </p>
            </div>
            <a href="{{ route('tours.index') }}" class="hidden sm:inline-flex btn btn-ghost">
                Lihat Semua
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
            @forelse($packages as $package)
                <a href="{{ route('tour.show', $package) }}"
                   class="group card overflow-hidden">

                    {{-- Thumbnail --}}
                    <div class="relative h-52 overflow-hidden bg-slate-100">
                        @if($package->thumbnail_path)
                            <img
                                src="{{ asset('storage/'.$package->thumbnail_path) }}"
                                alt="{{ $package->title }}"
                                class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                            >
                        @else
                            <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                            <div class="absolute inset-0 grid place-items-center text-slate-500 text-sm">
                                <div class="text-center">
                                    <i data-lucide="image" class="w-8 h-8 mx-auto mb-2" style="color:#0194F3;"></i>
                                    No Image
                                </div>
                            </div>
                        @endif
                        

                        {{-- Badge destination --}}
                        @if($package->destination)
                            <div class="absolute top-3 left-3">
                                <div class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                                    {{ $package->destination }}
                                </div>
                            </div>
                        @endif

                        {{-- subtle svg route line --}}
                        <svg class="absolute bottom-2 right-2 w-24 h-24 opacity-70" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                            <path d="M15 85c18-26 36-38 54-38 14 0 26 5 36 14" stroke="#0194F3" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round"/>
                            <path d="M86 26l8 8-8 8" stroke="#0194F3" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-azure transition">
                            {{ $package->title }}
                        </h3>

                        @if($package->duration_text)
                            <div class="mt-1 text-sm text-slate-600 flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                                Durasi: {{ $package->duration_text }}
                            </div>
                        @endif
<div class="mt-3 flex items-center gap-2 text-sm text-slate-700">
    {{-- 1 bintang full kuning --}}
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
        {{ $package->rating_value ?? 5 }} Rating
    </span>
</div>

                        <div class="mt-4 inline-flex items-center gap-2 text-sm font-extrabold" style="color:#0194F3;">
                            Lihat Detail
                            <span class="transition group-hover:translate-x-1">→</span>
                        </div>
                    </div>

                </a>
            @empty
                <div class="text-slate-500">
                    Belum ada paket wisata.
                </div>
            @endforelse
        </div>

        <div class="mt-10 sm:hidden">
            <a href="{{ route('tours.index') }}" class="btn btn-ghost w-full">
                Lihat Semua Paket
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
     <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
    <div class="text-center max-w-2xl mx-auto" data-aos="fade-up">
        <div class="mx-auto w-fit pill pill-azure">
            <i data-lucide="stars" class="w-4 h-4"></i>
            {{ $siteSettings['home_why_label'] ?? 'Layanan unggulan' }}
        </div>

        <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
            {{ $siteSettings['home_why_title'] ?? 'Mengapa Memilih Bintang Wisata' }}
        </h2>

        <p class="mt-3 text-slate-600">
            {{ $siteSettings['home_why_desc'] ?? 'Kami berkomitmen memberikan layanan perjalanan yang profesional, transparan, dan berorientasi pada kenyamanan pelanggan.' }}
        </p>
    </div>

    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="100">
        @php
            $reasons = [
                ['icon'=>'badge-dollar-sign','title'=>($siteSettings['home_why1_title'] ?? 'Harga Transparan'), 'desc'=>($siteSettings['home_why1_desc'] ?? 'Tanpa biaya tersembunyi, semua informasi jelas sejak awal.')],
                ['icon'=>'shield-check','title'=>($siteSettings['home_why2_title'] ?? 'Legal & Terpercaya'), 'desc'=>($siteSettings['home_why2_desc'] ?? 'Dikelola secara profesional dan berpengalaman.')],
                ['icon'=>'zap','title'=>($siteSettings['home_why3_title'] ?? 'Proses Booking Cepat'), 'desc'=>($siteSettings['home_why3_desc'] ?? 'Sistem pemesanan ringkas dan mudah digunakan.')],
                ['icon'=>'headphones','title'=>($siteSettings['home_why4_title'] ?? 'Dukungan Pelanggan'), 'desc'=>($siteSettings['home_why4_desc'] ?? 'Tim siap membantu sebelum dan selama perjalanan.')],
            ];
        @endphp

        @foreach($reasons as $r)
            <div class="card p-6 text-left relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full" style="background: radial-gradient(circle, rgba(1,148,243,0.18), transparent 65%);"></div>

                <div class="flex items-start gap-4 relative">
                    <div class="icon-badge">
                        <i data-lucide="{{ $r['icon'] }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-slate-900">{{ $r['title'] }}</div>
                        <div class="mt-1.5 text-sm text-slate-600">{{ $r['desc'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

</section>


{{-- ================= HOW IT WORKS ================= --}}
<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-20">
        <div class="text-center max-w-2xl mx-auto" data-aos="fade-up">
            <div class="mx-auto w-fit pill pill-azure">
                <i data-lucide="route" class="w-4 h-4"></i>
                {{ $siteSettings['home_flow_label'] ?? 'Alur mudah' }}
            </div>

            <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                {{ $siteSettings['home_flow_title'] ?? 'Cara Booking yang Rapi & Cepat' }}
            </h2>

            <p class="mt-3 text-slate-600">
                {{ $siteSettings['home_flow_desc'] ?? 'Biar gak buang waktu, alurnya dibuat simple tapi tetap jelas.' }}
            </p>
        </div>

        @php
            $steps = [
                ['no'=>'01','icon'=>'search','title'=>($siteSettings['home_flow1_title'] ?? 'Pilih Paket'),'desc'=>($siteSettings['home_flow1_desc'] ?? 'Cari destinasi, cek detail itinerary, dan sesuaikan kebutuhan.')],
                ['no'=>'02','icon'=>'message-circle','title'=>($siteSettings['home_flow2_title'] ?? 'Konsultasi'),'desc'=>($siteSettings['home_flow2_desc'] ?? 'Tanya jadwal, meeting point, atau request khusus untuk grup.')],
                ['no'=>'03','icon'=>'calendar-check','title'=>($siteSettings['home_flow3_title'] ?? 'Konfirmasi'),'desc'=>($siteSettings['home_flow3_desc'] ?? 'Finalisasi tanggal & data peserta, lalu booking dikunci.')],
                ['no'=>'04','icon'=>'plane','title'=>($siteSettings['home_flow4_title'] ?? 'Berangkat'),'desc'=>($siteSettings['home_flow4_desc'] ?? 'Nikmati perjalanan. Tim support siap bantu selama trip.')],
            ];
        @endphp

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="120">
            @foreach($steps as $s)
                <div class="card p-6 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full" style="background: radial-gradient(circle, rgba(1,148,243,0.16), transparent 65%);"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div class="icon-badge">
                                <i data-lucide="{{ $s['icon'] }}" class="w-5 h-5"></i>
                            </div>
                            <div class="text-xs font-extrabold text-slate-500">{{ $s['no'] }}</div>
                        </div>
                        <div class="mt-4 font-extrabold text-slate-900">{{ $s['title'] }}</div>
                        <div class="mt-2 text-sm text-slate-600">{{ $s['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-white">
  <div class="max-w-7xl mx-auto px-4 py-16">

    {{-- Header --}}
    <div class="text-center mb-12">
      <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold mx-auto"
           style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
        <i data-lucide="shield-check" class="w-4 h-4" style="color:#0194F3;"></i>
        Kepercayaan pelanggan
      </div>

      <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">
        Kepercayaan Pelanggan Bintang Wisata
      </h2>

      <p class="mt-2 text-slate-600 max-w-2xl mx-auto">
        Brand dan institusi yang telah mempercayakan perjalanan bersama kami
      </p>
    </div>

    {{-- Logo wall --}}
    <div class="rounded-3xl border border-slate-200 bg-white overflow-hidden">
      <div class="p-6 lg:p-10">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-x-6 gap-y-8">
          @forelse($clientLogos as $logo)
            @php
              $wrapOpen  = $logo->url ? '<a href="'.$logo->url.'" target="_blank" rel="noopener" class="group block">' : '<div class="group">';
              $wrapClose = $logo->url ? '</a>' : '</div>';
            @endphp

            {!! $wrapOpen !!}
              <div class="flex items-center justify-center">
                <div class="w-full max-w-[170px] h-14 rounded-2xl border border-slate-200 bg-slate-50/60
                            flex items-center justify-center px-5
                            transition group-hover:bg-white group-hover:shadow-sm">
                  <img
                    src="{{ asset('storage/'.$logo->image_path) }}"
                    alt="{{ $logo->name }}"
                    class="h-9 sm:h-10 object-contain opacity-80 transition
                           group-hover:opacity-100"
                    loading="lazy"
                  >
                </div>
              </div>

              <div class="mt-3 text-center text-xs font-semibold text-slate-500 truncate px-2">
                {{ $logo->name }}
              </div>
            {!! $wrapClose !!}

          @empty
            <div class="col-span-full">
              <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                <div class="mx-auto h-12 w-12 rounded-2xl border border-slate-200 bg-white flex items-center justify-center">
                  <i data-lucide="image" class="w-5 h-5" style="color:#0194F3;"></i>
                </div>
                <div class="mt-4 font-extrabold text-slate-900">Belum ada logo pelanggan</div>
                <p class="mt-1 text-sm text-slate-600">Tambahkan logo dari halaman admin untuk ditampilkan di sini.</p>
              </div>
            </div>
          @endforelse
        </div>
      </div>

      {{-- subtle footer strip --}}
      <div class="h-1 w-full" style="background: linear-gradient(90deg, rgba(1,148,243,.18), rgba(1,148,243,0));"></div>
    </div>

  </div>
</section>


{{-- ================= CTA ================= --}}
<section class="max-w-7xl mx-auto px-4 py-16 text-center">
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 lg:p-10 shadow-soft">
        <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>
        <svg class="absolute -top-16 -right-12 w-72 h-72 opacity-60 pointer-events-none" viewBox="0 0 300 300" fill="none" aria-hidden="true">
            <circle cx="150" cy="150" r="120" fill="#0194F3" fill-opacity="0.10"/>
            <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#0194F3" stroke-opacity="0.35" stroke-width="3" stroke-linecap="round"/>
            <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#0194F3" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round"/>
        </svg>

        <div class="relative">
            <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900">
                Rencanakan Perjalanan Anda Sekarang
            </h2>
            <p class="mt-3 text-slate-600 max-w-xl mx-auto">
                Hubungi tim kami untuk mendapatkan rekomendasi perjalanan terbaik sesuai kebutuhan Anda.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('tours.index') }}" class="btn btn-primary px-8 py-3">
                    <i data-lucide="map" class="w-5 h-5"></i>
                    Lihat Paket Tour
                </a>
                <a href="#" class="btn btn-ghost px-8 py-3">
                    <i data-lucide="messages-square" class="w-5 h-5"></i>
                    Konsultasi Perjalanan
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
