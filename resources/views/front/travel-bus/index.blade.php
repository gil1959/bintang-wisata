@extends('layouts.front')

@section('title', 'Cari Tiket Bus & Travel')

@push('styles')
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

    {{-- SEARCH HERO BANNER --}}
    <div class="rounded-3xl p-8 text-white shadow-xl relative overflow-hidden"
         style="background: #1b68e4;">
        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Cari Tiket Bus & Travel</h1>
        <p class="mt-1 text-sm md:text-base text-blue-100 font-medium">Pesan tiket perjalanan antarkota cepat, transparan, dan terpercaya.</p>

        <form action="{{ route('travel-bus.index') }}" method="GET" class="mt-6 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <input type="text" name="q" value="{{ $q }}"
                       placeholder="Cari Asal, Tujuan, Nama Travel, atau jenis armada..."
                       class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white text-slate-800 placeholder-slate-400 text-sm font-semibold border-0 focus:ring-2 focus:ring-orange-500 shadow-inner">
            </div>
            <button type="submit"
                    class="px-8 py-3.5 rounded-2xl font-black text-white shadow-md hover:brightness-105 transition flex items-center justify-center gap-2"
                    style="background: #f97316;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Cari</span>
            </button>
        </form>
    </div>

    {{-- LIST TIKET TRAVEL / BUS --}}
    <div class="space-y-4">
        @forelse($packages as $p)
            @php
                $approvedReviews = $p->reviews ?? collect();
                $reviewCount = $approvedReviews->count();
                $avgRating = $reviewCount > 0 ? round((float)$approvedReviews->avg('rating'), 1) : null;
                $routesCount = (is_array($p->route_details) && count($p->route_details) > 0) ? (count($p->route_details) + 2) : 2;
                $photoCount = $p->photos->count() + ($p->thumbnail_path ? 1 : 0);
            @endphp
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                    {{-- Bagian Kiri: Info Utama --}}
                    <div class="flex-1 space-y-4">
                        {{-- Baris Atas: Nama Travel, Kelas, Rating, Tipe Armada --}}
                        <div>
                            <div class="flex items-center gap-3 flex-wrap">
                                <a href="{{ route('travel-bus.show', $p->slug) }}" class="text-xl font-black text-slate-900 tracking-tight hover:text-blue-600 transition">
                                    {{ $p->armada_name }}
                                </a>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-200">
                                    {{ $p->travel_type }}
                                </span>

                                {{-- Real Rating Badge --}}
                                @if($reviewCount > 0)
                                    <a href="{{ route('travel-bus.show', $p->slug) }}#reviews" class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold bg-blue-50 text-blue-600 border border-blue-200 flex items-center gap-1 hover:bg-blue-100 transition">
                                        <i class="fa-solid fa-star text-amber-500 text-[10px]"></i>
                                        <span>{{ number_format($avgRating, 1) }} ({{ $reviewCount }})</span>
                                        <i class="fa-solid fa-chevron-right text-[9px] text-blue-400"></i>
                                    </a>
                                @else
                                    <a href="{{ route('travel-bus.show', $p->slug) }}#reviews" class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200 flex items-center gap-1 hover:bg-slate-100 transition">
                                        <i class="fa-regular fa-star text-slate-400 text-[10px]"></i>
                                        <span>Belum ada ulasan</span>
                                    </a>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500 font-bold">
                                <i class="fa-solid fa-van-shuttle text-blue-500"></i>
                                <span>{{ $p->armada_type }}</span>
                            </div>
                        </div>

                        {{-- Baris Tengah: Jam & Durasi, Fasilitas --}}
                        <div class="flex flex-wrap items-center gap-6 sm:gap-10">
                            {{-- Jam Berangkat --}}
                            <div>
                                <div class="text-2xl font-black text-slate-900">{{ $p->formatted_departure_time }}</div>
                                <div class="text-xs font-bold text-slate-500 max-w-[140px] truncate" title="{{ $p->departure_location }}">
                                    {{ $p->departure_location }}
                                </div>
                            </div>

                            {{-- Durasi & Garis Arah --}}
                            <div class="text-center">
                                <div class="text-xs font-extrabold text-slate-500">{{ $p->formatted_duration }}</div>
                                <div class="flex items-center gap-1 text-blue-500 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                                    <div class="w-12 h-0.5 bg-blue-400"></div>
                                    <i class="fa-solid fa-chevron-right text-[10px] text-orange-500"></i>
                                </div>
                            </div>

                            {{-- Jam Tiba --}}
                            <div>
                                <div class="text-2xl font-black text-slate-900 flex items-center gap-1">
                                    <span>{{ date('H:i', strtotime($p->arrival_time)) }}</span>
                                    @if($p->arrival_days_offset > 0)
                                        <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">
                                            +{{ $p->arrival_days_offset }}h
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs font-bold text-slate-500 max-w-[140px] truncate" title="{{ $p->arrival_location }}">
                                    {{ $p->arrival_location }}
                                </div>
                            </div>

                            {{-- Icon Fasilitas & Foto Badge --}}
                            <div class="flex items-center gap-2 pt-1 sm:pt-0">
                                @if(in_array('tv', (array)$p->facilities))
                                    <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600" title="TV / Audio">
                                        <i class="fa-solid fa-tv text-xs"></i>
                                    </div>
                                @endif
                                @if(in_array('reclining_seat', (array)$p->facilities))
                                    <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600" title="Reclining Seat">
                                        <i class="fa-solid fa-couch text-xs"></i>
                                    </div>
                                @endif
                                @if(in_array('wifi', (array)$p->facilities))
                                    <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-600" title="Free WiFi">
                                        <i class="fa-solid fa-wifi text-xs"></i>
                                    </div>
                                @endif

                                @if($photoCount > 0)
                                    <a href="{{ route('travel-bus.show', $p->slug) }}#gallery" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 text-xs font-bold hover:bg-blue-100 transition">
                                        <i class="fa-solid fa-images text-[11px]"></i>
                                        <span>{{ $photoCount }} Foto</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Baris Bawah: Links --}}
                        <div class="flex items-center gap-4 text-xs font-bold text-blue-600 flex-wrap pt-1">
                            <a href="{{ route('travel-bus.show', $p->slug) }}#gallery" class="hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-images text-blue-500"></i>
                                <span>Fitur & Foto Armada</span>
                            </a>
                            <a href="{{ route('travel-bus.show', $p->slug) }}#routes" class="hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-map-location-dot text-blue-500"></i>
                                <span>Rute Detail ({{ $routesCount }})</span>
                            </a>
                            <a href="{{ route('travel-bus.show', $p->slug) }}#terms" class="hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-blue-500"></i>
                                <span>Ketentuan Tiket</span>
                            </a>
                            <a href="{{ route('travel-bus.show', $p->slug) }}#reviews" class="hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <span>Rating & Review ({{ $reviewCount }})</span>
                            </a>
                        </div>
                    </div>

                    {{-- Bagian Kanan: Harga & Tombol Pesan Langsung Menuju Detail Paket --}}
                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                        <div class="text-left lg:text-right">
                            <div class="text-[11px] font-bold text-slate-400">Mulai dari</div>
                            <div class="text-2xl lg:text-3xl font-black text-[#ea580c] tracking-tight">
                                Rp {{ number_format($p->price, 0, ',', '.') }}<span class="text-xs font-bold text-slate-400">/kursi</span>
                            </div>
                        </div>

                        <a href="{{ route('travel-bus.show', $p->slug) }}"
                           class="mt-0 lg:mt-6 px-6 py-3 rounded-2xl font-black text-white shadow-md hover:brightness-105 transition flex items-center gap-1.5"
                           style="background: #f97316;">
                            <span>Pesan Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-4">
                    <i class="fa-solid fa-bus text-2xl text-slate-400"></i>
                </div>
                <h4 class="text-lg font-extrabold text-slate-800">Tidak ada jadwal ditemukan</h4>
                <p class="text-sm text-slate-500 mt-1">Coba ubah kata kunci pencarian rute atau armada Anda.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $packages->links() }}
        </div>
    </div>

</div>
@endsection
