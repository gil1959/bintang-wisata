@extends('layouts.front')
@section('title', 'Home - Bintang Wisata')

@section('content')
  {{-- HERO --}}
  <section class="relative overflow-hidden">
    <div class="absolute inset-0">
      <img src="{{ $siteSettings['hero_image'] }}" class="h-full w-full object-cover" alt="Hero">
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950/70 via-slate-950/35 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 pt-14 pb-16 lg:pt-20 lg:pb-24">
      <div class="max-w-2xl" data-aos="fade-up">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-4 py-2 text-white text-xs">
          <span class="h-2 w-2 rounded-full bg-brand-500"></span>
          Travel • Tour • Rental
        </div>

        <h1 class="mt-4 text-4xl lg:text-5xl font-extrabold tracking-tight text-white">
          {{ $siteSettings['hero_title'] }}
        </h1>

        <p class="mt-4 text-white/90 text-base lg:text-lg">
          {{ $siteSettings['hero_subtitle'] }}
        </p>

        <div class="mt-8 flex flex-col sm:flex-row gap-3">
          <a class="btn btn-primary" href="{{ route('tours.index') }}">Pilih Paket Tour</a>
          <a class="btn btn-ghost" href="{{ route('rentcar.index') }}">Lihat Rental</a>
        </div>
      </div>

      {{-- quick benefits (mirip pola referensi) --}}
      <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="150">
        @php
          $benefits = [
            ['title'=>'Harga Ekonomis', 'desc'=>'Value tinggi, transparan, no drama.'],
            ['title'=>'Legal & Terpercaya', 'desc'=>'Proses jelas, data aman.'],
            ['title'=>'Booking Cepat', 'desc'=>'Mobile-first, form singkat.'],
            ['title'=>'CS Ramah', 'desc'=>'Respons cepat, solusi jelas.'],
          ];
        @endphp

        @foreach($benefits as $b)
          <div class="rounded-2xl bg-white/10 border border-white/15 p-5 text-white hover:bg-white/15 transition">
            <div class="text-sm font-bold">{{ $b['title'] }}</div>
            <div class="mt-1 text-sm text-white/80">{{ $b['desc'] }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- SECTION: Paket populer --}}
  <section class="max-w-7xl mx-auto px-4 py-12 lg:py-16">
    <div class="flex items-end justify-between gap-4" data-aos="fade-up">
      <div>
        <h2 class="text-2xl lg:text-3xl font-extrabold text-slate-900">Paket Tour Terbaik</h2>
        <p class="mt-2 text-slate-600">Pilih yang paling cocok, lalu checkout cepat.</p>
      </div>
      <a class="hidden sm:inline-flex btn btn-ghost" href="{{ route('tours.index') }}">Lihat Semua</a>
    </div>

    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
      @forelse($packages as $package)
        <a href="{{ route('tour.show', $package) }}" class="card p-5 group">
          <div class="text-xs text-slate-500 mb-2">
            {{ $package->category === 'domestic' ? 'Domestik' : 'Internasional' }}
            @if($package->destination) • {{ $package->destination }} @endif
          </div>

          <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand-600 transition">
              {{ $package->title }}
            </h3>
            <div class="shrink-0 h-10 w-10 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center">
              <span class="text-brand-600 font-extrabold">→</span>
            </div>
          </div>

          @if($package->duration_text)
            <div class="mt-2 text-sm text-slate-600">Durasi: {{ $package->duration_text }}</div>
          @endif

          @if($package->short_description)
            <p class="mt-3 text-sm text-slate-600 line-clamp-3">
              {{ $package->short_description }}
            </p>
          @endif

          <div class="mt-4 text-sm font-semibold text-brand-600">
            Lihat Detail Paket
          </div>
        </a>
      @empty
        <div class="text-slate-500">Belum ada paket wisata.</div>
      @endforelse
    </div>

    <div class="mt-8">
      {{ $packages->withQueryString()->links() }}
    </div>
  </section>
@endsection
