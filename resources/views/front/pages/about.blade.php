@extends('layouts.front')
@section('title','About')

@section('content')
<section class="relative overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-b from-brand-50 via-white to-white"></div>
  <div class="relative max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-14">
    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
      <div data-aos="fade-up">
        <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-100 px-4 py-2 text-brand-700 text-xs font-semibold">
          <span class="h-2 w-2 rounded-full bg-brand-500"></span>
          Tentang Kami
        </div>

        <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
          Travel yang fokus ke pengalaman, bukan janji manis
        </h1>

        <p class="mt-4 text-slate-600">
          Bintang Wisata bantu kamu pilih itinerary yang realistis, budget yang transparan, dan layanan yang rapi.
          Kalau lu cari yang “murah banget tapi minta semuanya”, itu biasanya red flag — dan kita nggak jual mimpi.
        </p>

        <div class="mt-7 flex flex-col sm:flex-row gap-3">
          <a href="{{ route('tours.index') }}" class="btn btn-primary">Lihat Paket Tour</a>
          <a href="{{ route('docs') }}" class="btn btn-ghost">Lihat Dokumentasi</a>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2" data-aos="fade-up" data-aos-delay="120">
          <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900">Transparan</div>
            <p class="mt-2 text-sm text-slate-600">Harga & include/exclude jelas. Minim kejutan pas hari H.</p>
          </div>
          <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900">Rapi</div>
            <p class="mt-2 text-sm text-slate-600">Flow booking & komunikasi tertata. Customer nggak kebingungan.</p>
          </div>
          <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900">Fleksibel</div>
            <p class="mt-2 text-sm text-slate-600">Itinerary bisa disesuaikan, tapi tetap masuk akal secara waktu.</p>
          </div>
          <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900">Support</div>
            <p class="mt-2 text-sm text-slate-600">CS cepat respon. Bukan “ghosting” pas dibutuhin.</p>
          </div>
        </div>
      </div>

      <div data-aos="fade-left" class="relative">
        <div class="aspect-[4/3] rounded-3xl overflow-hidden border border-slate-200 bg-white shadow-soft">
          {{-- ganti gambar sesuai branding --}}
          <div class="h-full w-full bg-gradient-to-tr from-brand-100 via-white to-white"></div>
        </div>

        <div class="absolute -bottom-6 -left-6 card p-5 w-[85%] sm:w-[70%]" data-aos="fade-up" data-aos-delay="200">
          <div class="text-sm font-extrabold text-slate-900">Kenapa orang pilih kami?</div>
          <p class="mt-2 text-sm text-slate-600">
            Karena prosesnya jelas: pilih paket → tanya detail → booking → jalan.
            Bukan “DM dulu nanti kita pikirin”.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 pb-14 lg:pb-20">
  <div class="card p-6 lg:p-10" data-aos="fade-up">
    <div class="grid gap-8 lg:grid-cols-3">
      <div>
        <div class="text-xs font-semibold text-brand-700">OUR MISSION</div>
        <div class="mt-2 text-2xl font-extrabold text-slate-900">Bikin travel jadi simple</div>
        <p class="mt-3 text-slate-600 text-sm">
          Fokus ke pengalaman dan eksekusi. Website, layanan, dan itinerary harus clean—kayak UI yang lu minta.
        </p>
      </div>
      <div class="lg:col-span-2 grid gap-4 sm:grid-cols-2">
        <div class="card p-5">
          <div class="text-sm font-extrabold text-slate-900">1) Konsultasi cepat</div>
          <p class="mt-2 text-sm text-slate-600">Tanya kebutuhan lu, kita jawab to-the-point.</p>
        </div>
        <div class="card p-5">
          <div class="text-sm font-extrabold text-slate-900">2) Rekomendasi paket</div>
          <p class="mt-2 text-sm text-slate-600">Kita saranin opsi realistis sesuai waktu & budget.</p>
        </div>
        <div class="card p-5">
          <div class="text-sm font-extrabold text-slate-900">3) Booking</div>
          <p class="mt-2 text-sm text-slate-600">Data jelas, pembayaran jelas, dokumen jelas.</p>
        </div>
        <div class="card p-5">
          <div class="text-sm font-extrabold text-slate-900">4) Eksekusi trip</div>
          <p class="mt-2 text-sm text-slate-600">Jalan sesuai plan + support kalau ada perubahan.</p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
