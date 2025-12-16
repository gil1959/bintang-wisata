@extends('layouts.front')

@section('title', 'Rental Mobil')

@section('content')

{{-- ================= HEADER ================= --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-dots opacity-70"></div>
    <svg class="absolute -top-16 -left-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="rentGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(260 320) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.20"/>
                <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="260" cy="320" r="280" fill="url(#rentGlow)"/>
        <path d="M120 260c60 40 110 60 170 60 90 0 155-45 250-125" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 py-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                     style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    Rental Mobil
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900">
                    Pilihan Mobil Terbaik untuk Perjalanan Anda
                </h1>

                <p class="mt-2 max-w-2xl text-slate-600">
                    Armada terawat, harga transparan, dan proses booking cepat tanpa ribet.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="shield-check" class="w-4 h-4"></i> Terawat</span>
                    <span class="pill pill-azure"><i data-lucide="wallet" class="w-4 h-4"></i> Transparan</span>
                    <span class="pill pill-azure"><i data-lucide="clock" class="w-4 h-4"></i> Cepat</span>
                    <span class="pill pill-azure"><i data-lucide="map" class="w-4 h-4"></i> Travel Ready</span>
                </div>
            </div>

            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-grid opacity-60 pointer-events-none"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3">
                            <div class="icon-badge">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900">Catatan</div>
                                <div class="text-sm text-slate-600 mt-0.5">Klik “Booking Sekarang” untuk lihat detail unit.</div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="fuel" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Hemat
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Nyaman untuk perjalanan</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="sparkles" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Bersih
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Unit terawat</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Kapasitas
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Cocok keluarga/grup</div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Fleksibel
                                </div>
                                <div class="text-xs text-slate-600 mt-1">Untuk wisata & kerja</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- divider --}}
        <svg class="block w-full mt-10" viewBox="0 0 1440 90" fill="none" aria-hidden="true">
            <path d="M0 35C180 85 360 85 540 50C720 15 900 15 1080 50C1260 85 1350 80 1440 55V90H0V35Z" fill="#F8FAFC"/>
        </svg>
    </div>
</section>

{{-- ================= GRID ================= --}}
<section class="max-w-7xl mx-auto px-4 pb-12 -mt-10">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

        @forelse ($packages as $package)
            <article class="group card overflow-hidden">

                {{-- IMAGE --}}
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img
                        src="{{ asset('storage/' . $package->thumbnail_path) }}"
                        alt="{{ $package->title }}"
                        class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                    >

                    {{-- BADGE --}}
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                            <i data-lucide="car" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                            Rent Car
                        </span>
                    </div>

                    {{-- svg accent --}}
                    <svg class="absolute bottom-2 right-2 w-24 h-24 opacity-70" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                        <path d="M18 82c18-18 38-28 60-28 14 0 26 4 40 12" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round"/>
                        <path d="M86 30l8 8-8 8" stroke="#0194F3" stroke-opacity="0.30" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                {{-- CONTENT --}}
                <div class="p-5 flex flex-col h-full">

                    <h3 class="text-lg font-extrabold text-slate-900">
                        {{ $package->title }}
                    </h3>

                    {{-- PRICE --}}
                    <div class="mt-2 flex items-end gap-1">
                        <div class="text-2xl font-extrabold" style="color:#0194F3;">
                            Rp{{ number_format($package->price_per_day, 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-slate-500 mb-0.5">/ hari</div>
                    </div>

                    {{-- FEATURES --}}
                    @if(!empty($package->features))
                        <ul class="mt-4 space-y-2 text-sm text-slate-600">
                            @foreach(array_slice($package->features, 0, 4) as $f)
                                <li class="flex items-center gap-2">
                                    @if($f['available'])
                                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                    @else
                                        <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
                                    @endif
                                    <span>{{ $f['name'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- CTA (no function change, only style) --}}
<div class="mt-6">
    <a href="{{ route('rentcar.show', $package->slug) }}"
       class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-extrabold text-white transition shadow-sm hover:shadow-md"
       style="background:#0194F3;"
       onmouseover="this.style.background='#0186DB'"
       onmouseout="this.style.background='#0194F3'">
        <i data-lucide="calendar-check" class="w-4 h-4"></i>
        Booking Sekarang
    </a>
</div>

                </div>
            </article>

        @empty
            <div class="col-span-full">
                <div class="card p-10 text-center">
                    <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
                         style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                        <i data-lucide="car" class="w-6 h-6" style="color:#0194F3;"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-extrabold text-slate-900">Belum ada paket rental</h3>
                    <p class="mt-2 text-slate-600">Silakan cek kembali nanti, atau konsultasi untuk rekomendasi unit.</p>
                </div>
            </div>
        @endforelse

    </div>
</section>
@endsection
