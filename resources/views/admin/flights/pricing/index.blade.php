@extends('layouts.admin')

@section('title', 'Tiket Pesawat')
@section('page-title', 'Tiket Pesawat')

@php
$search = is_array($search ?? null) ? $search : [];
$results = is_array($results ?? null) ? $results : [];
$cityLabelMap = is_array($cityLabelMap ?? null) ? $cityLabelMap : [];
@endphp

@section('content')
<div class="space-y-5">
    @if(session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Tiket Pesawat</h1>
            <p class="mt-1 text-sm text-slate-600">Cari tiket supplier, lihat harga default, lalu set harga manual tanpa mengubah flow booking.</p>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="mb-4">
            <div class="text-base font-extrabold text-slate-900">Pencarian Tiket</div>
            <div class="mt-1 text-sm text-slate-600">Pakai route, tanggal, dan airline/flight result untuk set harga manual.</div>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Trip Type</label>
                <select name="tripType" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
                    <option value="OneWay" @selected(($search['tripType'] ?? 'OneWay' )==='OneWay' )>One Way</option>
                    <option value="RoundTrip" @selected(($search['tripType'] ?? '' )==='RoundTrip' )>Round Trip</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Asal</label>
                <input
                    type="text"
                    name="origin"
                    list="adminFlightCities"
                    value="{{ $search['origin'] ?? '' }}"
                    placeholder="Jakarta, Soekarno Hatta (CGK) / CGK"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Tujuan</label>
                <input
                    type="text"
                    name="destination"
                    list="adminFlightCities"
                    value="{{ $search['destination'] ?? '' }}"
                    placeholder="Denpasar, Bali (DPS) / DPS"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Tanggal Berangkat</label>
                <input
                    type="date"
                    name="departDate"
                    value="{{ $search['departDate'] ?? '' }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Tanggal Pulang</label>
                <input
                    type="date"
                    name="returnDate"
                    value="{{ $search['returnDate'] ?? '' }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Adult</label>
                <input type="number" min="1" max="9" name="paxAdult" value="{{ $search['paxAdult'] ?? 1 }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Child</label>
                <input type="number" min="0" max="9" name="paxChild" value="{{ $search['paxChild'] ?? 0 }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-extrabold text-slate-600 uppercase">Infant</label>
                <input type="number" min="0" max="9" name="paxInfant" value="{{ $search['paxInfant'] ?? 0 }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div class="md:col-span-6 flex items-end gap-2">
                <button class="inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-extrabold text-white"
                    style="background:#0194F3;">
                    Cari Tiket
                </button>

                <a href="{{ route('admin.flights.pricing.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        <datalist id="adminFlightCities">
            @foreach($cityLabelMap as $label)
            <option value="{{ $label }}"></option>
            @endforeach
        </datalist>
    </div>

    @if(!empty($searchError))
    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
        {{ $searchError }}
    </div>
    @endif

    @if(count($results) > 0)
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <div class="text-sm font-extrabold text-slate-900">Hasil Pencarian Supplier</div>
            <div class="mt-1 text-xs font-semibold text-slate-500">Klik detail untuk lihat harga default dan set harga manual.</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Airline</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Route</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Jadwal</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Harga Tampil</th>
                        <th class="px-4 py-3 text-right text-xs font-extrabold uppercase text-slate-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($results as $item)
                    @php
                    $journey = (array) ($item['journey'] ?? []);
                    $pricing = (array) ($item['pricing'] ?? []);
                    $airline = (string) data_get($journey, 'airlineID', '-');
                    $flightNumber = (string) data_get($journey, 'segment.0.flightDetail.0.flightNumber', '');
                    $origin = (string) data_get($journey, 'jiOrigin', $search['origin'] ?? '-');
                    $destination = (string) data_get($journey, 'jiDestination', $search['destination'] ?? '-');
                    $depart = (string) data_get($journey, 'jiDepartTime', '-');
                    $arrive = (string) data_get($journey, 'jiArrivalTime', '-');
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ trim($airline . ' ' . $flightNumber) }}</div>
                            <div class="text-xs text-slate-500">{{ data_get($journey, 'journeyReference', '-') }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $origin }} → {{ $destination }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            <div>{{ $depart }}</div>
                            <div class="text-xs text-slate-500">{{ $arrive }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-rose-600">
                                Rp {{ number_format((float) ($pricing['final_price'] ?? 0), 0, ',', '.') }}
                            </div>
                            @if(!empty($pricing['is_manual']))
                            <div class="text-xs font-semibold text-sky-700">Harga manual aktif</div>
                            @else
                            <div class="text-xs text-slate-500">Harga default supplier/schedule</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.flights.pricing.show', $item['key']) }}"
                                class="inline-flex items-center rounded-2xl border border-slate-200 px-3 py-2 text-xs font-extrabold text-slate-700 hover:bg-slate-50">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Override Harga Tersimpan</div>
                <div class="mt-1 text-xs font-semibold text-slate-500">List rule harga manual yang sudah pernah disimpan.</div>
            </div>

            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}"
                    placeholder="Cari airline/flight/route..."
                    class="w-64 max-w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800">
                <button class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Airline</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Route</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Manual</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase text-slate-600">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($savedOverrides as $ov)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $ov->airline_name ?: $ov->airline_id ?: '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $ov->flight_number ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $ov->origin }} → {{ $ov->destination }}</td>
                        <td class="px-4 py-3 text-slate-700">Rp {{ number_format((float) $ov->supplier_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-bold text-rose-600">Rp {{ number_format((float) $ov->manual_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $ov->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-600' }}">
                                {{ $ov->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ optional($ov->updated_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">Belum ada override harga tiket pesawat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $savedOverrides->links() }}
        </div>
    </div>
</div>
@endsection