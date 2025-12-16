@extends('layouts.front')

@section('title', $package->title)

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10">

  <div class="grid gap-10 lg:grid-cols-3">

    {{-- LEFT CONTENT --}}
    <div class="lg:col-span-2 space-y-8">

      {{-- IMAGE --}}
      <div class="rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
        <img
          src="{{ $package->thumbnail_path ? asset('storage/' . $package->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Rent+Car' }}"
          alt="{{ $package->title }}"
          class="w-full h-[360px] object-cover"
        >
      </div>

      {{-- TITLE + PRICE --}}
      <div>
        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900">
          {{ $package->title }}
        </h1>

        <div class="mt-3 flex items-end gap-2">
          <div class="text-3xl font-extrabold text-brand-600">
            Rp{{ number_format($package->price_per_day, 0, ',', '.') }}
          </div>
          <span class="text-slate-500 text-sm mb-1">/ hari</span>
        </div>
      </div>

      {{-- FEATURES --}}
      <section class="bg-white border border-slate-200 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Fitur Paket</h2>

        @if(!empty($package->features))
          <ul class="grid sm:grid-cols-2 gap-3 text-sm">
            @foreach ($package->features as $feat)
              <li class="flex items-center gap-2">
                @if(!empty($feat['available']))
                  <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                @else
                  <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
                @endif
                <span class="text-slate-700">{{ $feat['name'] ?? '-' }}</span>
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-sm text-slate-500">Belum ada fitur yang ditambahkan.</div>
        @endif
      </section>

    </div>

    {{-- RIGHT SIDEBAR — BOOKING --}}
    <aside class="lg:col-span-1">
      <div class="sticky top-24 bg-white border border-slate-200 rounded-2xl shadow-soft p-6">

        <h3 class="text-lg font-extrabold text-slate-900 mb-4">
          Booking Mobil
        </h3>

        <form id="bookingForm" onsubmit="return false;" class="space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Pickup Date</label>
            <input type="date" id="pickup"
              class="w-full rounded-xl border-slate-200 focus:ring-brand-500 focus:border-brand-500">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Return Date</label>
            <input type="date" id="return"
              class="w-full rounded-xl border-slate-200 focus:ring-brand-500 focus:border-brand-500">
          </div>

          {{-- SUMMARY --}}
          <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-sm">
            <div class="flex justify-between">
              <span class="text-slate-600">Total Hari</span>
              <strong id="days">0</strong>
            </div>
            <div class="flex justify-between mt-2">
              <span class="text-slate-600">Total Harga</span>
              <strong id="total">Rp0</strong>
            </div>
          </div>

          <button type="button"
            id="btnBook"
            disabled
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-brand-500 py-3 text-sm font-bold text-white hover:bg-brand-600 disabled:opacity-60 disabled:cursor-not-allowed transition">
            <i data-lucide="calendar-check" class="w-4 h-4"></i>
            Booking Sekarang
          </button>
        </form>

      </div>
    </aside>

  </div>

  {{-- REVIEWS --}}
  <div class="mt-14">
    @include('front.partials.reviews', ['item' => $package, 'type' => 'rent'])
  </div>

</section>

{{-- Popup booking modern --}}
@include('front.rentcar.partials.booking-popup', ['package' => $package])

<script>
document.addEventListener('DOMContentLoaded', function () {

  const pickup  = document.getElementById('pickup');
  const ret     = document.getElementById('return');
  const daysEl  = document.getElementById('days');
  const totalEl = document.getElementById('total');
  const btnBook = document.getElementById('btnBook');

  const pricePerDay = {{ (int) $package->price_per_day }};

  function recalc() {
    if (!pickup.value || !ret.value) {
      daysEl.textContent  = '0';
      totalEl.textContent = 'Rp0';
      btnBook.disabled    = true;
      return;
    }

    const start = new Date(pickup.value);
    const end   = new Date(ret.value);

    if (end < start) {
      daysEl.textContent  = '0';
      totalEl.textContent = 'Rp0';
      btnBook.disabled    = true;
      return;
    }

    const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    daysEl.textContent = diffDays;

    const total = diffDays * pricePerDay;
    totalEl.textContent = 'Rp' + total.toLocaleString('id-ID');

    btnBook.disabled = false;
  }

  pickup.addEventListener('change', recalc);
  ret.addEventListener('change', recalc);

  btnBook.addEventListener('click', function () {
    window.dispatchEvent(new CustomEvent('open-rentcar-booking', {
      detail: { pickup: pickup.value, ret: ret.value }
    }));
  });

});
</script>
@endsection
