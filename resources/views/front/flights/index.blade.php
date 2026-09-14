{{-- resources/views/front/flights/index.blade.php --}}
@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';

$i18n = [
'page_title' => $isEn ? 'Flight Tickets' : 'Tiket Pesawat',

'hero_badge' => $isEn ? 'Flights' : 'Tiket Pesawat',
'hero_title' => $isEn ? 'Search Flights Easily & Quickly' : 'Cari Tiket Pesawat dengan Mudah & Cepat',
'hero_desc' => $isEn
? 'Search by origin, destination, date, and passengers. Prices are dynamic and may change during booking.'
: 'Cari berdasarkan asal, tujuan, tanggal, dan jumlah penumpang. Harga bersifat dinamis dan bisa berubah saat booking.',

'pill_search' => $isEn ? 'Search' : 'Pencarian',
'pill_detail' => $isEn ? 'Details' : 'Detail',
'pill_price' => $isEn ? 'Real-time Price' : 'Harga Real-time',
'pill_support'=> $isEn ? 'Support' : 'Support',

'tips_title' => $isEn ? 'Quick Tips' : 'Tips Cepat',
'tips_desc' => $isEn
? 'Use IATA city/airport codes (e.g., CGK, DPS) for the most accurate results.'
: 'Gunakan kode kota/bandara (contoh: CGK, DPS) agar hasil lebih akurat.',

'tip1_title' => $isEn ? 'Use Codes' : 'Pakai Kode',
'tip1_desc' => $isEn ? 'Example: CGK, DPS, SUB' : 'Contoh: CGK, DPS, SUB',

'tip2_title' => $isEn ? 'Flexible Dates' : 'Tanggal Fleksibel',
'tip2_desc' => $isEn ? 'Try different dates for better prices' : 'Coba ganti tanggal untuk harga lebih baik',

'tip3_title' => $isEn ? 'Check Transit' : 'Cek Transit',
'tip3_desc' => $isEn ? 'Direct vs transit affects duration' : 'Langsung vs transit memengaruhi durasi',

'tip4_title' => $isEn ? 'Need Help?' : 'Butuh Bantuan?',
'tip4_desc' => $isEn ? 'Chat admin for guidance' : 'Chat admin untuk dibantu',

'filter_title' => $isEn ? 'Search Flights' : 'Cari Penerbangan',
'filter_sub' => $isEn ? 'Fill the form and press Search.' : 'Isi form lalu klik Cari.',

'trip_type' => $isEn ? 'Trip Type' : 'Tipe Perjalanan',
'one_way' => $isEn ? 'One Way' : 'Sekali Jalan',
'round_trip' => $isEn ? 'Round Trip' : 'Pulang Pergi',

'origin' => $isEn ? 'Origin' : 'Asal',
'destination' => $isEn ? 'Destination' : 'Tujuan',
'depart_date' => $isEn ? 'Departure Date' : 'Tanggal Berangkat',
'return_date' => $isEn ? 'Return Date' : 'Tanggal Pulang',

'pax' => $isEn ? 'Passengers' : 'Penumpang',
'adult' => $isEn ? 'Adult' : 'Dewasa',
'child' => $isEn ? 'Child' : 'Anak',
'infant' => $isEn ? 'Infant' : 'Bayi',

'search_btn' => $isEn ? 'Search' : 'Cari',
'reset_btn' => $isEn ? 'Reset' : 'Reset',
'swap' => $isEn ? 'Swap' : 'Tukar',

'results_title' => $isEn ? 'Search Results' : 'Hasil Pencarian',
'results_sub' => $isEn ? 'Flights found' : 'penerbangan ditemukan',

'view_detail' => $isEn ? 'View Detail' : 'Lihat Detail',

'empty_title_before' => $isEn ? 'Start searching' : 'Mulai cari tiket',
'empty_desc_before' => $isEn
? 'Fill in origin, destination, and departure date to see available flights.'
: 'Isi asal, tujuan, dan tanggal berangkat untuk melihat penerbangan yang tersedia.',

'empty_title_after' => $isEn ? 'No flights found' : 'Tidak ada penerbangan',
'empty_desc_after' => $isEn
? 'Try different dates or adjust origin/destination.'
: 'Coba ganti tanggal atau cek kembali asal/tujuan.',

'price' => $isEn ? 'Total Price' : 'Total Harga',
'duration' => $isEn ? 'Duration' : 'Durasi',
'direct' => $isEn ? 'Direct' : 'Langsung',
'transit' => $isEn ? 'Transit' : 'Transit',

'cta_title' => $isEn ? 'Need help choosing the best flight?' : 'Butuh bantuan pilih penerbangan terbaik?',
'cta_desc' => $isEn
? 'Chat our admin for recommendations based on schedule, duration, and budget.'
: 'Chat admin untuk rekomendasi berdasarkan jadwal, durasi, dan budget.',
'cta_btn' => $isEn ? 'Chat Admin' : 'Chat Admin',
];

$search = is_array($search ?? null) ? $search : [];
$results = is_array($results ?? null) ? $results : [];
$cities = is_array($cities ?? null) ? $cities : [];

$normalizeDateInput = function ($value) {
try {
if (!filled($value)) return '';
return \Carbon\Carbon::parse($value)->format('Y-m-d');
} catch (\Throwable $e) {
return '';
}
};

$tripTypeVal = old('tripType', (string)($search['tripType'] ?? 'OneWay'));
$originVal = old('origin', (string)($search['origin'] ?? ''));
$destinationVal = old('destination', (string)($search['destination'] ?? ''));
$departDateVal = $normalizeDateInput(old('departDate', (string)($search['departDate'] ?? '')));
$returnDateVal = $normalizeDateInput(old('returnDate', (string)($search['returnDate'] ?? '')));

$paxAdultVal = (int) old('paxAdult', (int)($search['paxAdult'] ?? 1));
$paxChildVal = (int) old('paxChild', (int)($search['paxChild'] ?? 0));
$paxInfantVal = (int) old('paxInfant', (int)($search['paxInfant'] ?? 0));

$totalPax = max(1, $paxAdultVal + $paxChildVal + $paxInfantVal);
$hasSearch = filled($originVal) && filled($destinationVal) && filled($departDateVal);

$safeParse = function ($value) {
try {
if (!filled($value)) return null;
return \Carbon\Carbon::parse($value);
} catch (\Throwable $e) {
return null;
}
};

$fmtTime = function ($value) use ($safeParse) {
$c = $safeParse($value);
return $c ? $c->format('H:i') : '-';
};

$fmtDate = function ($value) use ($safeParse) {
$c = $safeParse($value);
return $c ? $c->format('d M Y') : '-';
};

$fmtDuration = function ($start, $end) use ($safeParse) {
$a = $safeParse($start);
$b = $safeParse($end);
if (!$a || !$b) return '-';

$mins = $a->diffInMinutes($b, false);
if (!is_numeric($mins)) return '-';
$mins = abs((int)$mins);

$h = intdiv($mins, 60);
$m = $mins % 60;

return $h . 'h ' . str_pad((string)$m, 2, '0', STR_PAD_LEFT) . 'm';
};

$fmtMoney = function ($amount, $currency = 'IDR') {
if (!is_numeric($amount)) return '-';

$num = (int) round((float) $amount);
$cur = strtoupper((string)($currency ?? ''));

if ($cur === '' || $cur === 'IDR' || $cur === 'RP') {
return 'Rp ' . number_format($num, 0, ',', '.');
}

return $cur . ' ' . number_format($num, 0, ',', '.');
};

$airlineNameMap = [
'GA' => 'Garuda Indonesia',
'QG' => 'Citilink Indonesia',
'JT' => 'Lion Air',
'ID' => 'Batik Air',
'IW' => 'Wings Air',
'IU' => 'Super Air Jet',
'IP' => 'Pelita Air',
'8B' => 'TransNusa',
'KD' => 'Batik Air Malaysia',
'AK' => 'AirAsia',
'QZ' => 'Indonesia AirAsia',
'SJ' => 'Sriwijaya Air',
'IN' => 'NAM Air',
];

$cityNameMap = [];
$cityLabelMap = [];

foreach ($cities as $c) {
$cid = strtoupper((string) data_get($c, 'cityID', ''));
$cname = trim((string) data_get($c, 'cityName', ''));

if ($cid === '') {
continue;
}

$cityNameMap[$cid] = $cname !== '' ? $cname : $cid;
$cityLabelMap[$cid] = $cname !== '' ? ($cname . ' (' . $cid . ')') : $cid;
}

$formatCityLabel = function ($code) use ($cityLabelMap) {
$code = strtoupper(trim((string) $code));

if ($code === '') {
return '-';
}

return (string) ($cityLabelMap[$code] ?? $code);
};
@endphp

@section('title', ($isEn ? 'Flight Tickets - Bintang Wisata' : 'Tiket Pesawat - Bintang Wisata'))

@section('content')

<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>

    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="flightsHeroGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(310 290) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.22" />
                <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
            </radialGradient>
        </defs>
        <circle cx="310" cy="290" r="280" fill="url(#flightsHeroGlow)" />
        <path d="M130 330c70-90 170-150 280-150 40 0 80 7 120 20" stroke="#0194F3" stroke-opacity="0.25" stroke-width="2" stroke-linecap="round" />
        <path d="M165 385c85-70 160-105 245-105 70 0 125 18 170 42" stroke="#0194F3" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 pt-10 pb-10 lg:pt-14 lg:pb-12 relative">
        <div class="grid gap-8 lg:grid-cols-12 items-center">
            <div class="lg:col-span-7" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                    style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                    {{ $siteSettings['flight_hero_badge'] ?? $i18n['hero_badge'] }}
                </div>

                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                    {{ $siteSettings['flight_hero_title'] ?? $i18n['hero_title'] }}
                </h1>

                <p class="mt-3 text-slate-600 max-w-2xl">
                    {{ $siteSettings['flight_hero_desc'] ?? $i18n['hero_desc'] }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="pill pill-azure"><i data-lucide="search" class="w-4 h-4"></i> {{ $i18n['pill_search'] }}</span>
                    <span class="pill pill-azure"><i data-lucide="list" class="w-4 h-4"></i> {{ $i18n['pill_detail'] }}</span>
                    <span class="pill pill-azure"><i data-lucide="badge-dollar-sign" class="w-4 h-4"></i> {{ $i18n['pill_price'] }}</span>
                    <span class="pill pill-azure"><i data-lucide="headphones" class="w-4 h-4"></i> {{ $i18n['pill_support'] }}</span>
                </div>
            </div>

            <div class="lg:col-span-5" data-aos="fade-up" data-aos-delay="80">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft relative overflow-hidden">
                    <div class="absolute inset-0 travel-dots opacity-60 pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-start gap-3">
                            <div class="icon-badge shrink-0">
                                <i data-lucide="plane" class="w-5 h-5"></i>
                            </div>

                            <div>
                                <div class="font-extrabold text-slate-900">
                                    {{ $siteSettings['flight_tips_title'] ?? $i18n['tips_title'] }}
                                </div>
                                <div class="text-sm text-slate-600 mt-0.5">
                                    {{ $siteSettings['flight_tips_desc'] ?? $i18n['tips_desc'] }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="hash" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['flight_tip1_title'] ?? $i18n['tip1_title'] }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['flight_tip1_desc'] ?? $i18n['tip1_desc'] }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="calendar" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['flight_tip2_title'] ?? $i18n['tip2_title'] }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['flight_tip2_desc'] ?? $i18n['tip2_desc'] }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['flight_tip3_title'] ?? $i18n['tip3_title'] }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['flight_tip3_desc'] ?? $i18n['tip3_desc'] }}
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:shadow-md hover:border-slate-300">
                                <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                    <i data-lucide="messages-square" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $siteSettings['flight_tip4_title'] ?? $i18n['tip4_title'] }}
                                </div>
                                <div class="mt-1 text-xs text-slate-600 leading-relaxed">
                                    {{ $siteSettings['flight_tip4_desc'] ?? $i18n['tip4_desc'] }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
        <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC" />
    </svg>
</section>

<section class="max-w-7xl mx-auto px-4">
    <div class="card p-5 -mt-8 relative z-10" data-aos="fade-up" data-aos-delay="100">

        @if(session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <div class="font-extrabold">{{ $isEn ? 'Error' : 'Error' }}</div>
            <div class="mt-1 text-sm">{{ session('error') }}</div>
        </div>
        @endif

        @if(!empty($error))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <div class="font-extrabold">{{ $isEn ? 'Error' : 'Error' }}</div>
            <div class="mt-1 text-sm">{{ $error }}</div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <div class="font-extrabold">{{ $isEn ? 'Please fix the following:' : 'Harap perbaiki hal berikut:' }}</div>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="mb-4">
            <div class="text-base font-extrabold text-slate-900">{{ $i18n['filter_title'] }}</div>
            <div class="text-sm text-slate-600">{{ $i18n['filter_sub'] }}</div>
        </div>

        <form
            method="GET"
            action="{{ route('flights.index') }}"
            class="grid gap-4 md:grid-cols-12 items-end"
            @submit="prepareSubmit()"
            x-data="{
        tripType: '{{ $tripTypeVal }}',
        departDate: '{{ $departDateVal }}',
        cityMap: @js($cityLabelMap),
        originCode: '{{ strtoupper($originVal) }}',
        destinationCode: '{{ strtoupper($destinationVal) }}',
        originDisplay: '{{ $originVal ? $formatCityLabel($originVal) : '' }}',
        destinationDisplay: '{{ $destinationVal ? $formatCityLabel($destinationVal) : '' }}',

        init() {
            this.$watch('tripType', (v) => {
                if (v !== 'RoundTrip' && this.$refs.returnDate) {
                    this.$refs.returnDate.value = '';
                }
            });

            this.syncDisplayFromCode('origin');
            this.syncDisplayFromCode('destination');
        },

        normalizeCode(value) {
            return String(value || '').trim().toUpperCase();
        },

        findCodeFromInput(value) {
            const raw = String(value || '').trim();
            if (!raw) return '';

            const asCode = this.normalizeCode(raw);
            if (this.cityMap[asCode]) {
                return asCode;
            }

            const lowered = raw.toLowerCase();
            for (const [code, label] of Object.entries(this.cityMap)) {
                if (String(label).toLowerCase() === lowered) {
                    return code;
                }
            }

            const match = raw.match(/\(([A-Za-z0-9]{2,10})\)\s*$/);
            if (match && match[1]) {
                const extracted = this.normalizeCode(match[1]);
                if (this.cityMap[extracted]) {
                    return extracted;
                }
            }

            return asCode;
        },

        syncDisplayFromCode(field) {
            if (field === 'origin') {
                this.originDisplay = this.cityMap[this.originCode] || this.originCode;
            }

            if (field === 'destination') {
                this.destinationDisplay = this.cityMap[this.destinationCode] || this.destinationCode;
            }
        },

        syncField(field, value) {
            const code = this.findCodeFromInput(value);

            if (field === 'origin') {
                this.originCode = code;
                this.originDisplay = this.cityMap[code] || String(value || '').trim().toUpperCase();
            }

            if (field === 'destination') {
                this.destinationCode = code;
                this.destinationDisplay = this.cityMap[code] || String(value || '').trim().toUpperCase();
            }
        },

        swapCities() {
            const oldOriginCode = this.originCode;
            const oldOriginDisplay = this.originDisplay;

            this.originCode = this.destinationCode;
            this.originDisplay = this.destinationDisplay;

            this.destinationCode = oldOriginCode;
            this.destinationDisplay = oldOriginDisplay;
        },

        prepareSubmit() {
            this.syncField('origin', this.originDisplay);
            this.syncField('destination', this.destinationDisplay);
        }
    }">
            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['trip_type'] }}
                </label>
                <select
                    name="tripType"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm"
                    x-model="tripType">
                    <option value="OneWay" @selected($tripTypeVal==='OneWay' )>{{ $i18n['one_way'] }}</option>
                    <option value="RoundTrip" @selected($tripTypeVal==='RoundTrip' )>{{ $i18n['round_trip'] }}</option>
                </select>
                @error('tripType')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['origin'] }}
                </label>

                <input type="hidden" name="origin" :value="originCode">

                <div class="relative">
                    <input
                        x-ref="origin"
                        type="text"
                        list="flightCities"
                        x-model="originDisplay"
                        placeholder="Jakarta, Soekarno Hatta (CGK)"
                        class="w-full rounded-xl border border-slate-200 pl-11 pr-4 py-2 text-sm"
                        autocomplete="off"
                        required
                        @change="syncField('origin', originDisplay)"
                        @blur="syncField('origin', originDisplay)">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </span>
                </div>

                @error('origin')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-1 flex justify-center">
                <button
                    type="button"
                    class="btn btn-ghost w-full justify-center"
                    title="{{ $i18n['swap'] }}"
                    @click="swapCities()">
                    <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                    <span class="hidden lg:inline">{{ $i18n['swap'] }}</span>
                </button>
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['destination'] }}
                </label>

                <input type="hidden" name="destination" :value="destinationCode">

                <div class="relative">
                    <input
                        x-ref="destination"
                        type="text"
                        list="flightCities"
                        x-model="destinationDisplay"
                        placeholder="Denpasar, Bali (DPS)"
                        class="w-full rounded-xl border border-slate-200 pl-11 pr-4 py-2 text-sm"
                        autocomplete="off"
                        required
                        @change="syncField('destination', destinationDisplay)"
                        @blur="syncField('destination', destinationDisplay)">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </span>
                </div>

                @error('destination')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['depart_date'] }}
                </label>
                <input
                    type="date"
                    name="departDate"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm"
                    x-model="departDate"
                    value="{{ $departDateVal }}"
                    min="{{ now('Asia/Jakarta')->toDateString() }}"
                    required>
                @error('departDate')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['return_date'] }}
                </label>
                <input
                    x-ref="returnDate"
                    type="date"
                    name="returnDate"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm"
                    value="{{ $returnDateVal }}"
                    :disabled="tripType !== 'RoundTrip'"
                    :required="tripType === 'RoundTrip'"
                    :min="departDate || ''">
                @error('returnDate')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">
                    {{ $i18n['pax'] }}
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <div class="text-[11px] font-extrabold text-slate-600 mb-1">{{ $i18n['adult'] }}</div>
                        <select name="paxAdult" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @for($i = 1; $i <= 9; $i++)
                                <option value="{{ $i }}" @selected($paxAdultVal===$i)>{{ $i }}</option>
                                @endfor
                        </select>
                        @error('paxAdult')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="text-[11px] font-extrabold text-slate-600 mb-1">{{ $i18n['child'] }}</div>
                        <select name="paxChild" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @for($i = 0; $i <= 9; $i++)
                                <option value="{{ $i }}" @selected($paxChildVal===$i)>{{ $i }}</option>
                                @endfor
                        </select>
                        @error('paxChild')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="text-[11px] font-extrabold text-slate-600 mb-1">{{ $i18n['infant'] }}</div>
                        <select name="paxInfant" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @for($i = 0; $i <= 9; $i++)
                                <option value="{{ $i }}" @selected($paxInfantVal===$i)>{{ $i }}</option>
                                @endfor
                        </select>
                        @error('paxInfant')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="md:col-span-4 flex gap-3">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    {{ $i18n['search_btn'] }}
                </button>

                <a class="btn btn-ghost w-full" href="{{ route('flights.index') }}">
                    {{ $i18n['reset_btn'] }}
                </a>
            </div>

            <datalist id="flightCities">
                @foreach($cityLabelMap as $cid => $label)
                <option value="{{ $label }}"></option>
                @endforeach
            </datalist>
        </form>

        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-600">
            <span class="font-extrabold text-slate-700">
                {{ $isEn ? 'Active:' : 'Aktif:' }}
            </span>

            @if($originVal)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $i18n['origin'] }} <span class="font-extrabold">{{ $formatCityLabel($originVal) }}</span>
            </span>
            @endif

            @if($destinationVal)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $i18n['destination'] }} <span class="font-extrabold">{{ $formatCityLabel($destinationVal) }}</span>
            </span>
            @endif

            @if($departDateVal)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="calendar" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $i18n['depart_date'] }} <span class="font-extrabold">{{ $fmtDate($departDateVal) }}</span>
            </span>
            @endif

            @if($tripTypeVal === 'RoundTrip' && $returnDateVal)
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="calendar" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $i18n['return_date'] }} <span class="font-extrabold">{{ $fmtDate($returnDateVal) }}</span>
            </span>
            @endif

            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-1">
                <i data-lucide="users" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                {{ $i18n['pax'] }} <span class="font-extrabold">{{ $totalPax }}</span>
            </span>

            @if(!$originVal && !$destinationVal && !$departDateVal)
            <span class="text-slate-500">{{ $isEn ? 'None' : 'Tidak ada' }}</span>
            @endif
        </div>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-12 lg:py-14">
    <div class="flex items-end justify-between gap-4 mb-6" data-aos="fade-up">
        <div>
            <h2 class="text-xl lg:text-2xl font-extrabold text-slate-900">
                {{ $i18n['results_title'] }}
            </h2>

            @php $resultCount = count($results); @endphp

            <p class="mt-1 text-slate-600 text-sm">
                {{ $resultCount }} {{ $i18n['results_sub'] }}
            </p>

            @if($hasSearch)
            <p class="mt-1 text-xs text-slate-500">
                {{ $formatCityLabel($originVal) }} → {{ $formatCityLabel($destinationVal) }}
                • {{ $fmtDate($departDateVal) }}
                @if($tripTypeVal === 'RoundTrip' && filled($returnDateVal))
                • {{ $fmtDate($returnDateVal) }}
                @endif
                • {{ $totalPax }} {{ $isEn ? 'Passenger' : 'Penumpang' }}
            </p>
            @endif
        </div>
    </div>

    @if($resultCount > 0)
    <div class="grid gap-6 lg:grid-cols-2 items-stretch" data-aos="fade-up" data-aos-delay="120">
        @foreach($results as $r)
        @php
        $key = (string) data_get($r, 'key', '');
        $journey = (array) data_get($r, 'journey', []);

        $segments = (array) data_get($journey, 'segment', []);
        $segmentCount = count($segments);
        $stops = max(0, $segmentCount - 1);

        $pricingData = (array) data_get($r, 'pricing', []);
        $currency = (string) data_get($pricingData, 'currency', data_get($journey, 'currency', 'IDR'));
        $sumPrice = data_get($pricingData, 'final_price', data_get($journey, 'sumPrice', null));
        $isManualPrice = (bool) data_get($pricingData, 'is_manual', false);

        $jiOrigin = (string) data_get($journey, 'jiOrigin', $originVal);
        $jiDestination = (string) data_get($journey, 'jiDestination', $destinationVal);

        $departIso = data_get($journey, 'jiDepartTime', null);
        $arriveIso = data_get($journey, 'jiArrivalTime', null);

        $airlineNameMap = [
        'GA' => 'Garuda Indonesia',
        'QG' => 'Citilink Indonesia',
        'JT' => 'Lion Air',
        'ID' => 'Batik Air',
        'IW' => 'Wings Air',
        'IU' => 'Super Air Jet',
        'IP' => 'Pelita Air',
        '8B' => 'TransNusa',
        'KD' => 'Batik Air Malaysia',
        'AK' => 'AirAsia',
        'QZ' => 'Indonesia AirAsia',
        'SJ' => 'Sriwijaya Air',
        'IN' => 'NAM Air',
        ];

        $firstFlight = data_get($segments, '0.flightDetail.0', []);
        $airlineCode = strtoupper((string) (data_get($firstFlight, 'airlineCode') ?: data_get($journey, 'airlineID', '')));
        $flightNumber = trim((string) data_get($firstFlight, 'flightNumber', ''));
        $airlineFullName = (string) ($airlineNameMap[$airlineCode] ?? $airlineCode);
        $displayAirline = trim($airlineFullName . ($flightNumber !== '' ? ' ' . $flightNumber : ''));
        $category = (string) data_get($journey, 'category', '');

        $displayJiOrigin = $formatCityLabel($jiOrigin);
        $displayJiDestination = $formatCityLabel($jiDestination);
        @endphp

        <a
            href="{{ $key !== '' ? route('flights.show', $key) : route('flights.index') }}"
            class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">
            <div class="p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow-sm">
                            <i data-lucide="plane" class="w-4 h-4" style="color:#0194F3;"></i>
                            {{ $displayAirline !== '' ? $displayAirline : ($isEn ? 'Flight' : 'Penerbangan') }}
                        </span>


                    </div>

                    <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700">
                        <i data-lucide="users" class="w-4 h-4 mr-1" style="color:#0194F3;"></i>
                        {{ $totalPax }} {{ $isEn ? 'Passenger' : 'Penumpang' }}
                    </span>
                </div>

                <div class="mt-4">
                    <div class="text-[15px] font-extrabold text-[#0194F3]">
                        {{ $displayJiOrigin }} → {{ $displayJiDestination }}
                    </div>

                    <div class="mt-2 grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-xs font-extrabold text-slate-600">{{ $isEn ? 'Depart' : 'Berangkat' }}</div>
                            <div class="mt-1 text-sm font-bold text-slate-700">{{ $displayJiOrigin }}</div>
                            <div class="mt-1 font-extrabold text-slate-900">{{ $fmtTime($departIso) }}</div>
                            <div class="mt-0.5 text-xs text-slate-500">{{ $fmtDate($departIso) }}</div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-xs font-extrabold text-slate-600">{{ $i18n['duration'] }}</div>
                            <div class="mt-1 font-extrabold text-slate-900">{{ $fmtDuration($departIso, $arriveIso) }}</div>
                            <div class="mt-0.5 text-xs text-slate-500">
                                {{ $stops === 0 ? $i18n['direct'] : ($stops . ' ' . $i18n['transit']) }}
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="text-xs font-extrabold text-slate-600">{{ $isEn ? 'Arrive' : 'Tiba' }}</div>
                            <div class="mt-1 text-sm font-bold text-slate-700">{{ $displayJiDestination }}</div>
                            <div class="mt-1 font-extrabold text-slate-900">{{ $fmtTime($arriveIso) }}</div>
                            <div class="mt-0.5 text-xs text-slate-500">{{ $fmtDate($arriveIso) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 px-5 py-4">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="text-xs text-slate-600 font-bold">{{ $i18n['price'] }}</div>

                        @if($isManualPrice)

                        @endif
                    </div>

                    <div class="mt-1 text-base font-extrabold text-rose-600">
                        {{ $fmtMoney($sumPrice, $currency) }}
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="card p-10 text-center" data-aos="fade-up">
        <div class="mx-auto h-14 w-14 rounded-2xl border flex items-center justify-center"
            style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
            <i data-lucide="{{ $hasSearch ? 'search-x' : 'search' }}" class="w-6 h-6" style="color:#0194F3;"></i>
        </div>

        <h3 class="mt-4 text-lg font-extrabold text-slate-900">
            {{ $hasSearch ? $i18n['empty_title_after'] : $i18n['empty_title_before'] }}
        </h3>

        <p class="mt-2 text-slate-600">
            {{ $hasSearch ? $i18n['empty_desc_after'] : $i18n['empty_desc_before'] }}
        </p>

        <a href="{{ route('flights.index') }}" class="btn btn-primary mt-5">
            {{ $isEn ? 'Back to Search' : 'Kembali ke Pencarian' }}
        </a>
    </div>
    @endif
</section>

<section class="max-w-7xl mx-auto px-4 pb-16">
    <div class="rounded-3xl text-white p-8 lg:p-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative overflow-hidden"
        style="background: linear-gradient(90deg, #0194F3 0%, rgba(1,148,243,0.70) 100%);"
        data-aos="fade-up">
        <div class="absolute inset-0 opacity-60 pointer-events-none">
            <svg class="absolute -top-10 -right-10 w-72 h-72" viewBox="0 0 300 300" fill="none" aria-hidden="true">
                <circle cx="150" cy="150" r="120" fill="#FFFFFF" fill-opacity="0.10" />
                <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#FFFFFF" stroke-opacity="0.22" stroke-width="3" stroke-linecap="round" />
                <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#FFFFFF" stroke-opacity="0.18" stroke-width="3" stroke-linecap="round" />
            </svg>
        </div>

        <div class="max-w-2xl relative">
            <h2 class="text-2xl font-extrabold">
                {{ $siteSettings['flight_cta_title'] ?? $i18n['cta_title'] }}
            </h2>
            <p class="mt-2 text-white/90">
                {{ $siteSettings['flight_cta_desc'] ?? $i18n['cta_desc'] }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 relative">
            <a
                href="{{ $siteSettings['flight_cta_link'] ?? '#' }}"
                class="btn bg-white text-slate-900 hover:bg-white/90">
                <i data-lucide="messages-square" class="w-4 h-4"></i>
                {{ $siteSettings['flight_cta_button'] ?? $i18n['cta_btn'] }}
            </a>

            <a
                href="{{ route('tours.index') }}"
                class="btn btn-ghost border-white/30 text-white hover:bg-white/10">
                <i data-lucide="compass" class="w-4 h-4"></i>
                {{ $isEn ? 'See Tours' : 'Lihat Tour' }}
            </a>
        </div>
    </div>
</section>

@endsection