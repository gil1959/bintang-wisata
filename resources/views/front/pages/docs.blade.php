@extends('layouts.front')
@section('title','Dokumentasi')

@section('content')
<section class="relative overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-b from-brand-50 via-white to-white"></div>
  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-14">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
      <div data-aos="fade-up" class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-100 px-4 py-2 text-brand-700 text-xs font-semibold">
          <span class="h-2 w-2 rounded-full bg-brand-500"></span>
          Dokumentasi
        </div>
        <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
          Galeri Perjalanan & Dokumentasi
        </h1>
        <p class="mt-3 text-slate-600">
          Kumpulan foto & cerita singkat dari trip yang pernah kami handle. Tujuannya: bikin calon customer yakin, bukan janji kosong.
        </p>
      </div>

      <div data-aos="fade-up" data-aos-delay="120" class="flex gap-3">
        <a href="{{ route('tours.index') }}" class="btn btn-primary">Lihat Paket Tour</a>
        <a href="{{ route('rentcar.index') }}" class="btn btn-ghost">Lihat Rental</a>
      </div>
    </div>

    {{-- FILTER/SEARCH (UI/UX siap, backend nanti) --}}
    <div data-aos="fade-up" data-aos-delay="180" class="mt-8 card p-5">
      <div class="grid gap-4 md:grid-cols-3">
        <div>
          <label class="block text-sm font-semibold text-slate-800 mb-2">Cari</label>
          <input class="w-full rounded-xl border-slate-200" placeholder="Contoh: Bali, Lombok, Jepang..." />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-800 mb-2">Kategori</label>
          <select class="w-full rounded-xl border-slate-200">
            <option>Semua</option>
            <option>Tour</option>
            <option>Rental</option>
            <option>Dokumentasi Trip</option>
          </select>
        </div>
        <div class="md:pt-7 flex gap-3">
          <button class="btn btn-primary w-full">Terapkan</button>
          <button class="btn btn-ghost w-full">Reset</button>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 pb-14 lg:pb-20">
  <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up">
    @php
      // placeholder UI (ganti jadi data DB nanti)
      $items = [
        ['title'=>'Trip Bali 3D2N', 'loc'=>'Bali', 'date'=>'2025', 'tag'=>'Tour'],
        ['title'=>'Honeymoon Ubud', 'loc'=>'Bali', 'date'=>'2025', 'tag'=>'Tour'],
        ['title'=>'Rental Avanza Harian', 'loc'=>'Denpasar', 'date'=>'2025', 'tag'=>'Rental'],
        ['title'=>'Trip Lombok Santai', 'loc'=>'Lombok', 'date'=>'2025', 'tag'=>'Tour'],
        ['title'=>'Dokumentasi Group', 'loc'=>'Bali', 'date'=>'2025', 'tag'=>'Dokumentasi'],
        ['title'=>'City Tour', 'loc'=>'Denpasar', 'date'=>'2025', 'tag'=>'Tour'],
      ];
    @endphp

    @foreach($items as $i)
      <article class="card overflow-hidden group">
        <div class="aspect-[16/10] bg-slate-100 relative">
          {{-- ganti jadi img real nanti --}}
          <div class="absolute inset-0 bg-gradient-to-tr from-brand-100/60 via-white to-white"></div>
          <div class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/80 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
            {{ $i['tag'] }}
          </div>
        </div>

        <div class="p-5">
          <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand-600 transition">
              {{ $i['title'] }}
            </h3>
            <div class="shrink-0 h-10 w-10 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center">
              <span class="text-brand-600 font-extrabold">↗</span>
            </div>
          </div>

          <p class="mt-2 text-sm text-slate-600">
            Lokasi: <span class="font-semibold text-slate-800">{{ $i['loc'] }}</span> • {{ $i['date'] }}
          </p>

          <p class="mt-3 text-sm text-slate-600 line-clamp-2">
            Dokumentasi singkat untuk memperlihatkan pengalaman nyata—bukan cuma stok foto.
          </p>

          <a href="#" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition">
            Lihat Detail
            <span class="translate-x-0 group-hover:translate-x-0.5 transition">→</span>
          </a>
        </div>
      </article>
    @endforeach
  </div>

  <div class="mt-10 card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4" data-aos="fade-up" data-aos-delay="120">
    <div>
      <div class="text-lg font-extrabold text-slate-900">Mau dokumentasi trip kamu masuk sini?</div>
      <div class="mt-1 text-slate-600 text-sm">Hubungi admin, nanti kita kurasi & publish yang relevan.</div>
    </div>
    <a class="btn btn-primary" href="#">Kontak Admin</a>
  </div>
</section>
@endsection
