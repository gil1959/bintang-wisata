@extends('user.layouts.app')
@php $isEn = app()->getLocale() === 'en'; @endphp

@section('content')
<div class="space-y-6">
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Create Affiliate Link' : 'Buat Link Affiliate' }}</h1>
      <p class="mt-1 text-sm text-slate-600">
        {{ $isEn ? 'Select a product, add campaign parameters, and (optional) use a coupon.' : 'Pilih produk, tambahkan parameter campaign, dan (opsional) pakai coupon.' }}
      </p>
    </div>
    <a href="{{ route('user.affiliate.links') }}"
      class="px-4 py-2 rounded-2xl border border-slate-200 text-sm font-bold text-slate-700 bg-white hover:bg-slate-50">
      {{ $isEn ? 'Back' : 'Kembali' }}
    </a>
  </div>

  {{-- FILTER --}}
  <div class="bg-white border border-slate-200 rounded-2xl p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Product Type' : 'Tipe Produk' }}</label>
        <select name="type" id="filter_type"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold">
          <option value="" {{ $type===''?'selected':'' }}>{{ $isEn ? 'All' : 'Semua' }}</option>
          <option value="tour" {{ $type==='tour'?'selected':'' }}>Tour</option>
          <option value="umrah" {{ $type==='umrah'?'selected':'' }}>Umrah</option>
          <option value="rent_car" {{ $type==='rent_car'?'selected':'' }}>Rent Car</option>
          <option value="ship" {{ $type==='ship'?'selected':'' }}>{{ $isEn ? 'Rent Ship' : 'Sewa Kapal' }}</option>
          <option value="mice" {{ $type==='mice'?'selected':'' }}>MICE</option>
          <option value="flight" {{ $type==='flight'?'selected':'' }}>Flight</option>
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Search Product' : 'Cari Produk' }}</label>
        <input name="q" value="{{ $q }}"
          placeholder="{{ $isEn ? 'Type product name / slug...' : 'Ketik nama produk / slug...' }}"
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
      </div>

      @if($type === 'flight')
      <div class="md:col-span-3 border border-sky-200 bg-sky-50 rounded-2xl p-4">
        <div class="mb-3">
          <div class="text-sm font-extrabold text-slate-900">
            {{ $isEn ? 'Flight Search Parameters' : 'Parameter Pencarian Tiket Pesawat' }}
          </div>
          <div class="text-xs text-slate-600 mt-1">
            {{ $isEn ? 'Search a specific flight first, then select the result below.' : 'Cari tiket pesawat tertentu dulu, lalu pilih hasilnya di bawah.' }}
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Trip Type</label>
            <select name="tripType" id="filter_trip_type"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold">
              <option value="OneWay" {{ ($flightSearch['tripType'] ?? 'OneWay') === 'OneWay' ? 'selected' : '' }}>OneWay</option>
              <option value="RoundTrip" {{ ($flightSearch['tripType'] ?? '') === 'RoundTrip' ? 'selected' : '' }}>RoundTrip</option>
            </select>
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Origin</label>
            <input name="origin" value="{{ $flightSearch['origin'] ?? '' }}" placeholder="CGK"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Destination</label>
            <input name="destination" value="{{ $flightSearch['destination'] ?? '' }}" placeholder="DPS"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Departure Date' : 'Tanggal Berangkat' }}</label>
            <input type="date" name="departDate" value="{{ $flightSearch['departDate'] ?? '' }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
          <div id="filter_return_date_wrap" class="{{ ($flightSearch['tripType'] ?? 'OneWay') === 'RoundTrip' ? '' : 'hidden' }}">
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Return Date' : 'Tanggal Pulang' }}</label>
            <input type="date" name="returnDate" value="{{ $flightSearch['returnDate'] ?? '' }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Adult</label>
            <input type="number" min="1" max="9" name="paxAdult" value="{{ $flightSearch['paxAdult'] ?? 1 }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Child</label>
            <input type="number" min="0" max="9" name="paxChild" value="{{ $flightSearch['paxChild'] ?? 0 }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">Infant</label>
            <input type="number" min="0" max="9" name="paxInfant" value="{{ $flightSearch['paxInfant'] ?? 0 }}"
              class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
          </div>
        </div>
      </div>
      @endif

      <div class="md:col-span-3">
        <button type="submit" class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
          {{ $type === 'flight' ? ($isEn ? 'Search Flight' : 'Cari Tiket Pesawat') : ($isEn ? 'Apply Filter' : 'Terapkan Filter') }}
        </button>
      </div>
    </form>
  </div>

  {{-- CREATE FORM --}}
  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <form method="POST" action="{{ route('user.affiliate.links.store') }}" class="space-y-5">
      @csrf

      @if ($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <div class="font-bold mb-1">{{ $isEn ? 'Please fix the following errors:' : 'Perbaiki error berikut:' }}</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Link Name' : 'Nama Link' }}</label>
        <input name="name" value="{{ old('name') }}" required
          class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800"
          placeholder="{{ $isEn ? 'Example: IG Reels - Bali Tour - Jan' : 'Contoh: IG Reels - Tour Bali - Jan' }}">
      </div>

      <div>
        <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Select Product' : 'Pilih Produk' }}</label>
        <div class="mt-2 grid grid-cols-1 gap-2 max-h-72 overflow-auto border border-slate-200 rounded-2xl p-3 bg-slate-50">
          @forelse($products as $p)
          <label class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-200 cursor-pointer hover:bg-slate-50">
            <input type="radio" name="product_pick" value="{{ $p['type'] }}:{{ $p['id'] }}" required {{ old('product_type') === $p['type'] && (string) old('product_id') === (string) $p['id'] ? 'checked' : '' }}>
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900 truncate">{{ $p['name'] }}</div>
              <div class="text-xs text-slate-600">
                {{ $isEn ? 'Type' : 'Tipe' }}:
                <span class="font-bold">{{ $p['type'] }}</span>
              </div>
              <div class="text-xs text-slate-600">
                {{ $isEn ? 'Package Name' : 'Nama Paket' }}:
                <span class="font-mono">{{ $p['slug'] }}</span>
              </div>
            </div>
          </label>
          @empty
          <div class="text-sm text-slate-600 p-3">
            {{ $isEn ? 'No products found for this filter.' : 'Tidak ada produk ditemukan untuk filter ini.' }}
          </div>
          @endforelse
        </div>

        <input type="hidden" name="product_type" id="product_type" value="{{ old('product_type') }}">
        <input type="hidden" name="product_id" id="product_id" value="{{ old('product_id') }}">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Platform (optional)' : 'Platform (opsional)' }}</label>
          <input name="platform" value="{{ old('platform') }}" placeholder="tiktok / instagram / youtube / whatsapp"
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        </div>
        <div>
          <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Platform ID (optional)' : 'Platform ID (opsional)' }}</label>
          <input name="platform_id" value="{{ old('platform_id') }}" placeholder="ID campaign/adset/shortlink internal"
            class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
        </div>
      </div>

      <div class="border border-slate-200 rounded-2xl overflow-hidden">
        <button type="button"
          class="w-full flex items-center justify-between px-4 py-3 bg-slate-50 hover:bg-slate-100"
          onclick="document.getElementById('utmBox').classList.toggle('hidden')">
          <div class="text-left">
            <div class="text-sm font-extrabold text-slate-900">Campaign Tracking (Optional)</div>
            <div class="text-xs text-slate-600 mt-0.5">Advanced</div>
          </div>
          <div class="text-xs font-extrabold text-slate-700">{{ $isEn ? 'Show / Hide' : 'Tampilkan / Sembunyikan' }}</div>
        </button>

        <div id="utmBox" class="hidden bg-white px-4 py-4 space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Source</label>
              <input name="utm_source" value="{{ old('utm_source') }}"
                placeholder="{{ $isEn ? 'example: instagram / tiktok / whatsapp' : 'contoh: instagram / tiktok / whatsapp' }}"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
              <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Traffic source.' : 'Sumber traffic.' }}</div>
            </div>

            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Medium</label>
              <input name="utm_medium" value="{{ old('utm_medium') }}"
                placeholder="contoh: reels / story / ads / bio"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
              <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Content/placement type.' : 'Jenis konten/penempatan.' }}</div>
            </div>

            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Campaign</label>
              <input name="utm_campaign" value="{{ old('utm_campaign') }}"
                placeholder="contoh: promo_januari / lebaran_2026"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
              <div class="mt-1 text-[11px] text-slate-500">{{ $isEn ? 'Campaign name.' : 'Nama campaign.' }}</div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Content (opsional)</label>
              <input name="utm_content" value="{{ old('utm_content') }}"
                placeholder="contoh: video1 / influencerA"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>

            <div>
              <label class="text-xs font-extrabold text-slate-600 uppercase">UTM Term (opsional)</label>
              <input name="utm_term" value="{{ old('utm_term') }}"
                placeholder="contoh: keyword / segment"
                class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
            </div>
          </div>

          <div>
            <label class="text-xs font-extrabold text-slate-600 uppercase">{{ $isEn ? 'Coupon (optional)' : 'Coupon (opsional)' }}</label>
            <select name="user_coupon_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm font-semibold">
              <option value="">{{ $isEn ? 'No coupon' : 'Tanpa coupon' }}</option>
              @foreach($userCoupons as $c)
              <option value="{{ $c->id }}" {{ (string) old('user_coupon_id') === (string) $c->id ? 'selected' : '' }}>
                {{ $c->alias_name }} ({{ $c->promo?->code }})
              </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="pt-2">
        <button type="submit" class="px-4 py-2.5 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
          {{ $isEn ? 'Create Link' : 'Buat Link' }}
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function syncAffiliateProductState() {
    const pick = document.querySelector('input[name="product_pick"]:checked');
    const hiddenType = document.getElementById('product_type');
    const hiddenId = document.getElementById('product_id');

    if (!pick) {
      hiddenType.value = '';
      hiddenId.value = '';
      return;
    }

    const idx = pick.value.indexOf(':');
    hiddenType.value = idx === -1 ? '' : pick.value.substring(0, idx);
    hiddenId.value = idx === -1 ? '' : pick.value.substring(idx + 1);
  }

  function syncFlightFilterState() {
    const filterType = document.getElementById('filter_type');
    const tripType = document.getElementById('filter_trip_type');
    const returnWrap = document.getElementById('filter_return_date_wrap');

    if (!filterType || filterType.value !== 'flight' || !tripType || !returnWrap) return;

    if (tripType.value === 'RoundTrip') {
      returnWrap.classList.remove('hidden');
    } else {
      returnWrap.classList.add('hidden');
      const input = returnWrap.querySelector('input[name="returnDate"]');
      if (input) input.value = '';
    }
  }

  document.querySelectorAll('input[name="product_pick"]').forEach(function(el) {
    el.addEventListener('change', syncAffiliateProductState);
  });

  const filterTripType = document.getElementById('filter_trip_type');
  if (filterTripType) {
    filterTripType.addEventListener('change', syncFlightFilterState);
  }

  document.addEventListener('submit', function() {
    syncAffiliateProductState();
  }, true);

  syncAffiliateProductState();
  syncFlightFilterState();
</script>
@endsection