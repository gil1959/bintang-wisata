@extends('layouts.front')
@section('title','Artikel')

@section('content')
<section class="relative overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-b from-brand-50 via-white to-white"></div>
  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-14">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
      <div data-aos="fade-up" class="max-w-2xl">
        <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-100 px-4 py-2 text-brand-700 text-xs font-semibold">
          <span class="h-2 w-2 rounded-full bg-brand-500"></span>
          Artikel
        </div>
        <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
          Tips Travel, Rekomendasi, dan Insight
        </h1>
        <p class="mt-3 text-slate-600">
          Konten yang bikin orang makin yakin buat booking. Ini bukan blog “asal ada”, jadi jaga kualitasnya.
        </p>
      </div>

      <div data-aos="fade-up" data-aos-delay="120" class="w-full lg:w-auto">
        <div class="card p-3 flex gap-2">
          <input class="w-full lg:w-80 rounded-xl border-slate-200" placeholder="Cari artikel..." />
          <button class="btn btn-primary px-4">Cari</button>
        </div>
      </div>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="180">
      <article class="card overflow-hidden lg:col-span-2 group">
        <div class="aspect-[16/9] bg-slate-100 relative">
          <div class="absolute inset-0 bg-gradient-to-tr from-brand-100/60 via-white to-white"></div>
          <div class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/80 border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
            Featured
          </div>
        </div>
        <div class="p-6">
          <div class="text-xs text-slate-500">Travel • 5 min read</div>
          <h2 class="mt-2 text-2xl font-extrabold text-slate-900 group-hover:text-brand-600 transition">
            7 Cara Pilih Paket Tour yang Nggak Bikin Nyesel
          </h2>
          <p class="mt-3 text-slate-600">
            Biar nggak ketipu “harga murah” tapi itinerary kacau. Ini checklist yang harus lu lihat sebelum booking.
          </p>
          <a href="#" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition">
            Baca Artikel
            <span class="translate-x-0 group-hover:translate-x-0.5 transition">→</span>
          </a>
        </div>
      </article>

      <div class="grid gap-5">
        <article class="card p-5 group">
          <div class="text-xs text-slate-500">Bali • 3 min read</div>
          <h3 class="mt-2 font-extrabold text-slate-900 group-hover:text-brand-600 transition">
            Itinerary Bali 3 Hari yang Realistis
          </h3>
          <p class="mt-2 text-sm text-slate-600 line-clamp-3">
            Fokus spot yang worth it, tanpa maksa “semua harus masuk”.
          </p>
          <a href="#" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-brand-600">
            Baca <span class="group-hover:translate-x-0.5 transition">→</span>
          </a>
        </article>

        <article class="card p-5 group">
          <div class="text-xs text-slate-500">Budget • 4 min read</div>
          <h3 class="mt-2 font-extrabold text-slate-900 group-hover:text-brand-600 transition">
            Cara Hitung Budget Liburan Tanpa Ngawang
          </h3>
          <p class="mt-2 text-sm text-slate-600 line-clamp-3">
            Breakdown biaya yang paling sering bikin overbudget.
          </p>
          <a href="#" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-brand-600">
            Baca <span class="group-hover:translate-x-0.5 transition">→</span>
          </a>
        </article>
      </div>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 pb-14 lg:pb-20">
  <div class="flex items-end justify-between gap-4" data-aos="fade-up">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-900">Artikel Terbaru</h2>
      <p class="mt-2 text-slate-600">Nanti bagian ini tinggal dihubungkan ke database.</p>
    </div>
    <div class="hidden sm:flex gap-2">
      <button class="btn btn-ghost">Semua</button>
      <button class="btn btn-ghost">Bali</button>
      <button class="btn btn-ghost">Tips</button>
    </div>
  </div>

  <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3" data-aos="fade-up" data-aos-delay="120">
    @for($i=1;$i<=6;$i++)
      <article class="card overflow-hidden group">
        <div class="aspect-[16/10] bg-slate-100 relative">
          <div class="absolute inset-0 bg-gradient-to-tr from-brand-100/60 via-white to-white"></div>
        </div>
        <div class="p-5">
          <div class="text-xs text-slate-500">Kategori • 4 min read</div>
          <h3 class="mt-2 text-lg font-extrabold text-slate-900 group-hover:text-brand-600 transition">
            Judul Artikel {{ $i }}
          </h3>
          <p class="mt-2 text-sm text-slate-600 line-clamp-3">
            Preview singkat artikel. Pastikan copywriting singkat tapi tajam.
          </p>
          <a href="#" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700 transition">
            Baca Artikel
            <span class="translate-x-0 group-hover:translate-x-0.5 transition">→</span>
          </a>
        </div>
      </article>
    @endfor
  </div>

  <div class="mt-10 card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4" data-aos="fade-up" data-aos-delay="160">
    <div>
      <div class="text-lg font-extrabold text-slate-900">Butuh rekomendasi itinerary?</div>
      <div class="mt-1 text-slate-600 text-sm">Biar kami yang bantu pilih opsi terbaik sesuai waktu & budget lu.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('tours.index') }}">Lihat Paket Tour</a>
  </div>
</section>
@endsection
