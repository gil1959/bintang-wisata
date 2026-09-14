@extends('layouts.admin')

@section('title', 'Detail Tiket Pesawat')
@section('page-title', 'Detail Tiket Pesawat')

@php
$search = is_array($search ?? null) ? $search : [];
$journey = is_array($journey ?? null) ? $journey : [];
$pricingData = is_array($pricingData ?? null) ? $pricingData : [];
$cities = is_array($cities ?? null) ? $cities : [];

$cityNameMap = [];
foreach ($cities as $c) {
$cid = strtoupper((string) data_get($c, 'cityID', ''));
$cname = trim((string) data_get($c, 'cityName', ''));
if ($cid !== '') {
$cityNameMap[$cid] = $cname !== '' ? $cname : $cid;
}
}

$formatCityLabel = function ($code) use ($cityNameMap) {
$code = strtoupper(trim((string) $code));
if ($code === '') return '-';
$name = trim((string) ($cityNameMap[$code] ?? ''));
return $name !== '' ? ($name . ' (' . $code . ')') : $code;
};

$origin = (string) data_get($journey, 'jiOrigin', $search['origin'] ?? '');
$destination = (string) data_get($journey, 'jiDestination', $search['destination'] ?? '');
$airlineId = (string) data_get($journey, 'airlineID', '-');
$flightNumber = (string) data_get($journey, 'segment.0.flightDetail.0.flightNumber', '');
@endphp

@section('content')
<div class="max-w-6xl space-y-5">
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Detail Tiket Pesawat</h1>
            <p class="mt-1 text-sm text-slate-600">Lihat harga default supplier dan set harga manual untuk tampilan user.</p>
        </div>

        <a href="{{ route('admin.flights.pricing.index', $search) }}"
            class="inline-flex items-center rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="text-xs font-extrabold uppercase text-slate-600">Airline</div>
                <div class="mt-1 text-lg font-extrabold text-slate-900">{{ trim($airlineId . ' ' . $flightNumber) }}</div>
                <div class="mt-4 text-xs font-extrabold uppercase text-slate-600">Route</div>
                <div class="mt-1 text-lg font-extrabold text-[#0194F3]">
                    {{ $formatCityLabel($origin) }} → {{ $formatCityLabel($destination) }}
                </div>
                <div class="mt-2 text-sm text-slate-600">
                    Depart: {{ data_get($journey, 'jiDepartTime', '-') }}<br>
                    Arrive: {{ data_get($journey, 'jiArrivalTime', '-') }}
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-extrabold uppercase text-slate-600">Harga Default</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        Rp {{ number_format((float) ($pricingData['supplier_price'] ?? 0), 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Harga supplier/display reference.</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-extrabold uppercase text-slate-600">Harga Tampil Sekarang</div>
                    <div class="mt-2 text-2xl font-extrabold text-rose-600">
                        Rp {{ number_format((float) ($pricingData['final_price'] ?? 0), 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ !empty($pricingData['is_manual']) ? 'Override manual aktif.' : 'Masih pakai harga default.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-extrabold text-slate-900">Set Harga Manual</h2>
        <p class="mt-1 text-sm text-slate-600">Kalau diisi dan aktif, harga ini yang tampil ke user dan masuk ke order lokal. Flow booking supplier tetap tidak diubah.</p>

        <form method="POST" action="{{ route('admin.flights.pricing.upsert', $quoteKey) }}" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold uppercase text-slate-600">Harga Manual</label>
                <input type="number"
                    step="0.01"
                    min="0"
                    name="manual_price"
                    required
                    value="{{ old('manual_price', (float) ($pricingData['manual_price'] ?? $pricingData['supplier_price'] ?? 0)) }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase text-slate-600">Status</label>
                <select name="is_active" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
                    <option value="1" @selected(old('is_active', !empty($pricingData['override']) ? (int) $pricingData['override']->is_active : 1) == 1)>Aktif</option>
                    <option value="0" @selected(old('is_active', !empty($pricingData['override']) ? (int) $pricingData['override']->is_active : 1) == 0)>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-end">
                <button class="w-full rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white" style="background:#0194F3;">
                    Simpan Harga
                </button>
            </div>
        </form>

        @if(!empty($pricingData['override']))
        <div class="mt-4 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">
            Override aktif ditemukan untuk tiket ini. ID override: #{{ $pricingData['override']->id }}
        </div>
        @endif
    </div>
</div>
@endsection