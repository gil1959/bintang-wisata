@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';

$search = is_array($search ?? null) ? $search : [];
$journey = is_array($journey ?? null) ? $journey : [];
$cities = is_array($cities ?? null) ? $cities : [];
$countries = is_array($countries ?? null) ? $countries : [];
$key = (string)($key ?? '');

$origin = strtoupper((string) data_get($journey, 'jiOrigin', data_get($search, 'origin', '')));
$destination = strtoupper((string) data_get($journey, 'jiDestination', data_get($search, 'destination', '')));

$departIso = data_get($journey, 'jiDepartTime', null);
$arriveIso = data_get($journey, 'jiArrivalTime', null);

$price = is_array($price ?? null) ? $price : [];
$displayPrice = is_array($displayPrice ?? null) ? $displayPrice : $price;
$pricingData = is_array($pricingData ?? null) ? $pricingData : [];

$priceStatus = strtoupper((string) data_get($price, 'status', ''));
$isRepriced = $priceStatus === 'SUCCESS' && is_numeric(data_get($price, 'sumFare'));

$currency = (string) data_get($pricingData, 'currency', data_get($displayPrice, 'currency', data_get($journey, 'currency', 'IDR')));
$repricedFare = data_get($displayPrice, 'sumFare', null);
$estimatedFare = data_get($journey, 'sumPrice', null);
$resolvedPrice = data_get($pricingData, 'final_price', null);
$sumPrice = is_numeric($resolvedPrice) ? $resolvedPrice : (is_numeric($repricedFare) ? $repricedFare : $estimatedFare);

$segments = (array) data_get($journey, 'segment', []);
$segmentCount = count($segments);
$stops = max(0, $segmentCount - 1);

$paxAdult = (int) data_get($search, 'paxAdult', 1);
$paxChild = (int) data_get($search, 'paxChild', 0);
$paxInfant = (int) data_get($search, 'paxInfant', 0);
$totalPax = max(1, $paxAdult + $paxChild + $paxInfant);

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

$mins = abs((int) $a->diffInMinutes($b, false));
$h = intdiv($mins, 60);
$m = $mins % 60;

return $h . 'h ' . str_pad((string) $m, 2, '0', STR_PAD_LEFT) . 'm';
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

$countryOptions = [];
foreach ($countries as $country) {
$countryId = strtoupper(trim((string) data_get($country, 'countryID', '')));
$countryName = trim((string) data_get($country, 'countryName', ''));
if ($countryId !== '' && $countryName !== '') {
$countryOptions[] = [
'id' => $countryId,
'name' => $countryName,
];
}
}
@endphp

@section('title', $isEn ? 'Flight Booking Form' : 'Form Booking Tiket Pesawat')

@section('content')
<div
    x-data="flightBookingPage({
        paxAdult: {{ (int) $paxAdult }},
        paxChild: {{ (int) $paxChild }},
        paxInfant: {{ (int) $paxInfant }},
        origin: '{{ (string) data_get($search, 'origin', '') }}',
        destination: '{{ (string) data_get($search, 'destination', '') }}',
        departDate: '{{ (string) data_get($search, 'departDate', '') }}',
        addonsUrl: '{{ route('flights.addons', $key) }}',
        seatUrl: '{{ route('flights.seat', $key) }}',
        bookUrl: '{{ route('flights.book', $key) }}',
        csrfToken: '{{ csrf_token() }}',
        isEn: {{ $isEn ? 'true' : 'false' }},
        priceStatus: '{{ strtoupper((string) data_get($price, 'status', '')) }}',
        isRepriced: {{ $isRepriced ? 'true' : 'false' }},
        countries: @js($countryOptions),
    })"
    x-cloak
    class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between gap-3 mb-6">
        <a href="{{ route('flights.show', $key) }}" class="btn btn-ghost inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>{{ $isEn ? 'Back to Flight Detail' : 'Kembali ke Detail Tiket' }}</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $isEn ? 'Booking Form' : 'Form Booking' }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ $isEn ? 'Complete passenger and contact details to continue checkout.' : 'Lengkapi data kontak dan penumpang untuk lanjut checkout.' }}</p>
                </div>

                <div class="p-6">
                    <div x-show="error" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" x-text="error"></div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 mb-6">
                        <div class="text-lg font-extrabold text-slate-900">
                            {{ $formatCityLabel($origin) }} → {{ $formatCityLabel($destination) }}
                        </div>
                        <div class="mt-2 text-sm text-slate-600">
                            {{ $fmtDate($departIso ?: data_get($search,'departDate')) }}
                            • {{ $totalPax }} pax
                            • <span class="font-extrabold text-rose-600">{{ $fmtMoney($sumPrice, $currency) }}</span>
                        </div>
                    </div>

                    <div x-show="currentStep === 'form'" class="space-y-6">
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <div class="text-base font-extrabold text-slate-900 mb-4">{{ $isEn ? 'Contact Details' : 'Data Kontak' }}</div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'First Name' : 'Nama Depan' }}</label>
                                    <input type="text" x-model="form.contact.first_name" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                </div>

                                <div>
                                    <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Last Name' : 'Nama Belakang' }}</label>
                                    <input type="text" x-model="form.contact.last_name" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                </div>

                                <div>
                                    <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Title' : 'Titel' }}</label>
                                    <select x-model="form.contact.title" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        <option value="MR">MR</option>
                                        <option value="MRS">MRS</option>
                                        <option value="MISS">MISS</option>
                                        <option value="MSTR">MSTR</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-[11px] font-extrabold text-slate-600">Email</label>
                                    <input type="email" x-model="form.contact.email" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="nama@email.com">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'WhatsApp Number' : 'Nomor WhatsApp' }}</label>
                                    <input
                                        type="text"
                                        x-model="form.contact.phone"
                                        inputmode="numeric"
                                        @input="normalizePhoneInput()"
                                        class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm"
                                        placeholder="6285709...">
                                    <div class="mt-1 text-xs text-slate-500">{{ $isEn ? 'Use country code at the beginning. Example: 6281234567890' : 'Gunakan kode negara di depan. Contoh: 6281234567890' }}</div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" x-model="form.insurance">
                                        <span>{{ $isEn ? 'Add insurance if available' : 'Tambahkan asuransi jika tersedia' }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(pax, index) in form.paxDetails" :key="index">
                                <div class="rounded-2xl border border-slate-200 p-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div class="text-base font-extrabold text-slate-900" x-text="pax.ui.label"></div>
                                        <div class="text-[11px] rounded-full bg-slate-100 px-2 py-1 text-slate-600" x-text="pax.type"></div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">Title</label>
                                            <select x-model="pax.title" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <option value="MR">MR</option>
                                                <option value="MRS">MRS</option>
                                                <option value="MISS">MISS</option>
                                                <option value="MSTR">MSTR</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">Gender</label>
                                            <select x-model="pax.gender" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'First Name' : 'Nama Depan' }}</label>
                                            <input type="text" x-model="pax.firstName" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Last Name' : 'Nama Belakang' }}</label>
                                            <input type="text" x-model="pax.lastName" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Birth Date' : 'Tanggal Lahir' }}</label>
                                            <input type="date" x-model="pax.birthDate" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">Email</label>
                                            <input type="email" x-model="pax.Email" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">Nationality</label>
                                            <select x-model="pax.nationality" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <template x-for="country in countryOptions" :key="'nat-' + country.id">
                                                    <option :value="country.id" x-text="country.name + ' (' + country.id + ')'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Birth Country' : 'Negara Lahir' }}</label>
                                            <select x-model="pax.birthCountry" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <template x-for="country in countryOptions" :key="'birth-' + country.id">
                                                    <option :value="country.id" x-text="country.name + ' (' + country.id + ')'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="text-[11px] font-extrabold text-slate-600">ID Number</label>
                                            <input type="text" x-model="pax.IDNumber" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div class="sm:col-span-2" x-show="pax.type === 'Infant'">
                                            <label class="text-[11px] font-extrabold text-slate-600">
                                                {{ $isEn ? 'Parent Adult Passenger' : 'Penumpang Dewasa Orang Tua' }}
                                                <span class="text-red-500">*</span>
                                            </label>

                                            <select x-model="pax.parent"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <option value="">{{ $isEn ? '-- Select adult passenger --' : '-- Pilih penumpang dewasa --' }}</option>
                                                <template x-for="adult in adultPassengerOptions(index)" :key="'adult-parent-' + index + '-' + adult.key">
                                                    <option :value="adult.fullName || adult.firstName" x-text="adult.label"></option>
                                                </template>
                                            </select>

                                            <div class="mt-1 text-[11px] text-slate-500">
                                                {{ $isEn ? 'Parent will use the exact first name from the selected adult passenger.' : 'Parent akan memakai nama depan persis dari penumpang dewasa yang dipilih.' }}
                                            </div>
                                        </div>

                                        <div x-show="isInternationalRoute()">
                                            <label class="text-[11px] font-extrabold text-slate-600">Passport Issued Country</label>
                                            <select x-model="pax.passportIssuedCountry" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                                <option value="">-- Select Country --</option>
                                                <template x-for="country in countryOptions" :key="'pass-' + country.id">
                                                    <option :value="country.id" x-text="country.name + ' (' + country.id + ')'"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div x-show="isInternationalRoute()">
                                            <label class="text-[11px] font-extrabold text-slate-600">Passport Number</label>
                                            <input type="text" x-model="pax.passportNumber" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div x-show="isInternationalRoute()">
                                            <label class="text-[11px] font-extrabold text-slate-600">Passport Issued Date</label>
                                            <input type="date" x-model="pax.passportIssuedDate" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>

                                        <div x-show="isInternationalRoute()">
                                            <label class="text-[11px] font-extrabold text-slate-600">Passport Expired Date</label>
                                            <input type="date" x-model="pax.passportExpiredDate" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                class="rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                                style="background:#0194F3"
                                :disabled="loading"
                                @click="continueToAddonStep()">
                                <span x-show="!loading">{{ $isEn ? 'Continue to Add-ons' : 'Lanjut ke Add-on' }}</span>
                                <span x-show="loading">{{ $isEn ? 'Processing...' : 'Memproses...' }}</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="currentStep === 'addons'" class="space-y-6">
                        <template x-for="(pax, index) in form.paxDetails" :key="'addon-' + index">
                            <div class="rounded-2xl border border-slate-200 p-5">
                                <div class="mb-4 text-base font-extrabold text-slate-900" x-text="pax.ui.label"></div>

                                <template x-if="Array.isArray(pax.ui.segmentAddOns) && pax.ui.segmentAddOns.length > 0 && pax.ui.segmentAddOns.every(segment => !segment.baggageOptions.length && !segment.mealOptions.length && !segment.seatOptions.length)">
                                    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                        Supplier tidak mengirim pilihan baggage, meal, atau seat untuk rute / kelas ini.
                                    </div>
                                </template>

                                <div class="space-y-4">
                                    <template x-for="(segment, segmentIndex) in pax.ui.segmentAddOns" :key="'segment-' + index + '-' + segmentIndex">
                                        <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50">
                                            <div class="mb-3 text-sm font-extrabold text-slate-900" x-text="segmentTitle(segment)"></div>

                                            <div class="space-y-5">
                                                <div>
                                                    <div class="text-xs font-extrabold uppercase text-slate-500 mb-3">Baggage</div>
                                                    <template x-if="segment.baggageOptions.length">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <template x-if="segment.isEnableNoBaggage">
                                                                <button
                                                                    type="button"
                                                                    @click="segment.selectedBaggage = ''"
                                                                    class="text-left rounded-2xl border px-4 py-3 transition"
                                                                    :class="segment.selectedBaggage === '' ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                    <div class="font-extrabold text-slate-900">Tanpa baggage</div>
                                                                </button>
                                                            </template>

                                                            <template x-for="option in segment.baggageOptions" :key="'bag-' + segment.key + '-' + option.value">
                                                                <button
                                                                    type="button"
                                                                    @click="segment.selectedBaggage = option.value"
                                                                    class="text-left rounded-2xl border px-4 py-3 transition"
                                                                    :class="segment.selectedBaggage === option.value ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                    <div class="flex items-center justify-between">
                                                                        <span class="font-extrabold text-slate-900" x-text="option.label"></span>
                                                                    </div>
                                                                    <div class="mt-1 text-xs text-slate-500" x-text="option.fare ? ('+ Rp ' + formatNumber(option.fare)) : 'Free / Included'"></div>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!segment.baggageOptions.length">
                                                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">Tidak ada pilihan baggage.</div>
                                                    </template>
                                                </div>

                                                <div>
                                                    <div class="text-xs font-extrabold uppercase text-slate-500 mb-3">Meal</div>
                                                    <template x-if="segment.mealOptions.length">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <button
                                                                type="button"
                                                                @click="segment.selectedMeals = []"
                                                                class="text-left rounded-2xl border px-4 py-3 transition"
                                                                :class="!segment.selectedMeals || segment.selectedMeals.length === 0 ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                <div class="font-extrabold text-slate-900">Tanpa meal</div>
                                                            </button>

                                                            <template x-for="option in segment.mealOptions" :key="'meal-' + segment.key + '-' + option.value">
                                                                <button
                                                                    type="button"
                                                                    @click="toggleMeal(segment, option.value)"
                                                                    class="text-left rounded-2xl border px-4 py-3 transition"
                                                                    :class="Array.isArray(segment.selectedMeals) && segment.selectedMeals.includes(option.value) ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                    <div class="font-extrabold text-slate-900" x-text="option.label"></div>
                                                                    <div class="mt-1 text-xs text-slate-500" x-text="option.fare ? ('+ Rp ' + formatNumber(option.fare)) : 'Free / Included'"></div>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!segment.mealOptions.length">
                                                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">Tidak ada pilihan meal.</div>
                                                    </template>
                                                </div>

                                                <div>
                                                    <div class="text-xs font-extrabold uppercase text-slate-500 mb-3">Seat</div>
                                                    <template x-if="segment.seatOptions.length">
                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                            <button
                                                                type="button"
                                                                @click="selectSegmentSeat(segment, '')"
                                                                class="text-left rounded-2xl border px-4 py-3 transition"
                                                                :class="segment.selectedSeat === '' ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                <div class="font-extrabold text-slate-900">Tanpa seat</div>
                                                            </button>

                                                            <template x-for="option in segment.seatOptions" :key="'seat-' + segment.key + '-' + option.value + '-' + option.compartment">
                                                                <button
                                                                    type="button"
                                                                    @click="selectSegmentSeat(segment, option.value)"
                                                                    class="text-left rounded-2xl border px-4 py-3 transition"
                                                                    :class="segment.selectedSeat === option.value ? 'border-[#0194F3] bg-sky-50' : 'border-slate-200 bg-white'">
                                                                    <div class="font-extrabold text-slate-900" x-text="option.label"></div>
                                                                    <div class="mt-1 text-xs text-slate-500" x-text="option.seatPrice ? ('+ Rp ' + formatNumber(option.seatPrice)) : 'Free / Included'"></div>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!segment.seatOptions.length">
                                                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">Tidak ada pilihan kursi.</div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="flex items-center justify-between gap-3">
                            <button
                                type="button"
                                class="btn btn-ghost border border-slate-200"
                                @click="currentStep = 'form'"
                                :disabled="loading">
                                {{ $isEn ? 'Back' : 'Kembali' }}
                            </button>

                            <button
                                type="button"
                                class="rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                                style="background:#16a34a"
                                :disabled="loading"
                                @click="submitBooking()">
                                <span x-show="!loading">{{ $isEn ? 'Submit Booking' : 'Submit Booking' }}</span>
                                <span x-show="loading">{{ $isEn ? 'Booking...' : 'Booking...' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="text-sm font-extrabold text-[#0194F3]">
                    {{ $formatCityLabel($origin) }} → {{ $formatCityLabel($destination) }}
                </div>

                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>{{ $isEn ? 'Date' : 'Tanggal' }}</span>
                        <span class="font-bold text-slate-900">{{ $fmtDate($departIso ?: data_get($search,'departDate')) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>{{ $isEn ? 'Time' : 'Waktu' }}</span>
                        <span class="font-bold text-slate-900">{{ $fmtTime($departIso) }} - {{ $fmtTime($arriveIso) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>{{ $isEn ? 'Duration' : 'Durasi' }}</span>
                        <span class="font-bold text-slate-900">{{ $fmtDuration($departIso, $arriveIso) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>{{ $isEn ? 'Passengers' : 'Penumpang' }}</span>
                        <span class="font-bold text-slate-900">{{ $totalPax }}</span>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-extrabold text-slate-600">{{ $isEn ? 'Total Price' : 'Total Harga' }}</div>
                    <div class="mt-1 text-2xl font-extrabold text-rose-600">{{ $fmtMoney($sumPrice, $currency) }}</div>
                </div>
            </section>
        </aside>
    </div>
</div>

<script>
    function flightBookingPage(config) {
        return {
            loading: false,
            error: '',
            addonData: null,
            seatData: null,
            priceStatus: config.priceStatus || '',
            isRepriced: Boolean(config.isRepriced),
            currentStep: 'form',
            config,
            countryOptions: Array.isArray(config.countries) ? config.countries : [],

            form: {
                contact: {
                    first_name: '',
                    last_name: '',
                    title: 'MR',
                    phone: '62',
                    email: '',
                },
                insurance: false,
                paxDetails: []
            },

            init() {
                this.form.paxDetails = this.buildPassengers(
                    Number(this.config.paxAdult || 0),
                    Number(this.config.paxChild || 0),
                    Number(this.config.paxInfant || 0)
                );
            },

            buildPassengers(adult, child, infant) {
                const rows = [];
                for (let i = 0; i < adult; i++) rows.push(this.makePassenger('Adult', i + 1));
                for (let i = 0; i < child; i++) rows.push(this.makePassenger('Child', i + 1));
                for (let i = 0; i < infant; i++) rows.push(this.makePassenger('Infant', i + 1));
                return rows;
            },

            makePassenger(type, number) {
                const defaultTitle = type === 'Adult' ? 'MR' : 'MSTR';

                return {
                    IDNumber: '',
                    title: defaultTitle,
                    firstName: '',
                    lastName: '',
                    birthDate: '',
                    gender: 'Male',
                    nationality: 'ID',
                    birthCountry: 'ID',
                    DocType: 'KTP',
                    parent: '',
                    passportNumber: '',
                    passportIssuedCountry: 'ID',
                    passportIssuedDate: '',
                    passportExpiredDate: '',
                    Email: '',
                    type: type,
                    batikMilesNo: '',
                    garudaFrequentFlyer: '',
                    SSR: '',
                    addOns: [],
                    ui: {
                        label: `${type} ${number}`,
                        segmentAddOns: [],
                    }
                };
            },

            normalizeText(value) {
                return String(value || '').trim();
            },

            normalizeSegmentKey(origin, destination, departTime = '') {
                return [
                    this.normalizeText(origin).toUpperCase(),
                    this.normalizeText(destination).toUpperCase(),
                    this.normalizeText(departTime),
                ].join('|');
            },

            segmentTitle(segment) {
                const origin = this.normalizeText(segment.origin).toUpperCase();
                const destination = this.normalizeText(segment.destination).toUpperCase();
                return `${origin} → ${destination}`;
            },

            buildSeatOptions(seatSegment = null) {
                return (Array.isArray(seatSegment?.infos) ? seatSegment.infos : [])
                    .filter((item) => {
                        const seatType = this.normalizeText(item?.seatType).toUpperCase();
                        return Boolean(item?.isOpen) &&
                            Boolean(item?.assignable) &&
                            (seatType === '' || seatType === 'NS');
                    })
                    .map((item) => ({
                        value: this.normalizeText(item.seatDesignator),
                        label: this.normalizeText(item.seatDesignator),
                        compartment: this.normalizeText(item.compartment),
                        seatPrice: item?.seatPrice ?? null,
                    }))
                    .filter((item) => item.value && item.compartment);
            },

            buildSegmentAddonState(addonSegment = null, seatSegment = null) {
                const baggageOptions = (Array.isArray(addonSegment?.baggageInfos) ? addonSegment.baggageInfos : [])
                    .map((item) => ({
                        value: this.normalizeText(item.code),
                        label: this.normalizeText(item.desc || item.code),
                        fare: Number(item.fare || 0),
                    }))
                    .filter((item) => item.value);

                const mealOptions = (Array.isArray(addonSegment?.mealInfos) ? addonSegment.mealInfos : [])
                    .map((item) => ({
                        value: this.normalizeText(item.code),
                        label: this.normalizeText(item.desc || item.code),
                        fare: Number(item.fare || 0),
                    }))
                    .filter((item) => item.value);

                const origin = this.normalizeText(addonSegment?.origin || seatSegment?.origin).toUpperCase();
                const destination = this.normalizeText(addonSegment?.destination || seatSegment?.destination).toUpperCase();
                const departTime = this.normalizeText(seatSegment?.departTime || '');
                const arrivalTime = this.normalizeText(seatSegment?.arrivalTime || '');

                const isEnableNoBaggage = addonSegment?.isEnableNoBaggage !== false;
                let defaultBaggage = '';

                if (!isEnableNoBaggage && baggageOptions.length > 0) {
                    const sorted = [...baggageOptions].sort((a,b) => a.fare - b.fare);
                    defaultBaggage = sorted[0].value;
                }

                return {
                    key: this.normalizeSegmentKey(origin, destination, departTime),
                    origin,
                    destination,
                    departTime,
                    arrivalTime,
                    baggageOptions,
                    mealOptions,
                    seatOptions: this.buildSeatOptions(seatSegment),
                    isEnableNoBaggage,
                    selectedBaggage: defaultBaggage,
                    selectedMeals: [],
                    selectedSeat: '',
                    selectedCompartment: '',
                };
            },

            findSeatSegment(origin, destination, departTime = '') {
                const rawSeatAddOns = this.seatData?.data?.seatAddOns || this.seatData?.seatAddOns || [];
                const seatAddOns = Array.isArray(rawSeatAddOns) ? rawSeatAddOns : [];
                const targetKey = this.normalizeSegmentKey(origin, destination, departTime);

                return seatAddOns.find((segment) => {
                    const candidateKey = this.normalizeSegmentKey(
                        segment?.origin,
                        segment?.destination,
                        segment?.departTime || ''
                    );

                    return candidateKey === targetKey ||
                        (
                            this.normalizeText(segment?.origin).toUpperCase() === this.normalizeText(origin).toUpperCase() &&
                            this.normalizeText(segment?.destination).toUpperCase() === this.normalizeText(destination).toUpperCase()
                        );
                }) || null;
            },

            collectSegmentSources() {
                const rawAddOns = this.addonData?.data?.addOns || this.addonData?.addOns || [];
                const addOns = Array.isArray(rawAddOns) ? rawAddOns : [];
                
                const rawSeatAddOns = this.seatData?.data?.seatAddOns || this.seatData?.seatAddOns || [];
                const seatAddOns = Array.isArray(rawSeatAddOns) ? rawSeatAddOns : [];
                
                const map = new Map();

                addOns.forEach((addonSegment) => {
                    const key = this.normalizeSegmentKey(
                        addonSegment?.origin,
                        addonSegment?.destination,
                        ''
                    );

                    map.set(key, {
                        addonSegment,
                        seatSegment: this.findSeatSegment(addonSegment?.origin, addonSegment?.destination, '')
                    });
                });

                seatAddOns.forEach((seatSegment) => {
                    const keyWithTime = this.normalizeSegmentKey(
                        seatSegment?.origin,
                        seatSegment?.destination,
                        seatSegment?.departTime || ''
                    );
                    
                    const genericKey = this.normalizeSegmentKey(
                        seatSegment?.origin,
                        seatSegment?.destination,
                        ''
                    );

                    if (map.has(keyWithTime)) {
                        map.get(keyWithTime).seatSegment = seatSegment;
                        return;
                    }
                    
                    if (map.has(genericKey)) {
                        map.get(genericKey).seatSegment = seatSegment;
                        return;
                    }

                    map.set(keyWithTime, {
                        addonSegment: null,
                        seatSegment,
                    });
                });

                return Array.from(map.values());
            },

            hasRealSegmentSelection(segment) {
                return Boolean(
                    this.normalizeText(segment?.selectedBaggage) ||
                    (Array.isArray(segment?.selectedMeals) && segment.selectedMeals.length > 0) ||
                    this.normalizeText(segment?.selectedSeat) ||
                    this.normalizeText(segment?.selectedCompartment)
                );
            },

            selectSegmentSeat(segmentState, optionValue) {
                const selected = (Array.isArray(segmentState.seatOptions) ? segmentState.seatOptions : [])
                    .find((item) => item.value === optionValue);

                segmentState.selectedSeat = selected ? selected.value : '';
                segmentState.selectedCompartment = selected ? selected.compartment : '';
            },

            toggleMeal(segment, optionValue) {
                if (!Array.isArray(segment.selectedMeals)) {
                    segment.selectedMeals = [];
                }
                const idx = segment.selectedMeals.indexOf(optionValue);
                if (idx > -1) {
                    segment.selectedMeals.splice(idx, 1);
                } else {
                    segment.selectedMeals.push(optionValue);
                }
            },

            normalizePhoneInput() {
                let value = String(this.form.contact.phone || '').replace(/\D+/g, '');
                if (value.startsWith('0')) {
                    value = '62' + value.substring(1);
                }
                if (value !== '' && !value.startsWith('62')) {
                    value = '62' + value.replace(/^0+/, '');
                }
                this.form.contact.phone = value;
            },

            splitPhone() {
                this.normalizePhoneInput();

                const value = String(this.form.contact.phone || '');
                const local = value.startsWith('62') ? value.substring(2) : value;
                const area = local.substring(0, 3);
                const remaining = local.substring(3);

                return {
                    country_code_phone: '62',
                    area_code_phone: area,
                    remaining_phone_no: remaining,
                };
            },

            isInternationalRoute() {
                const origin = String(this.config.origin || '').toUpperCase().trim();
                const destination = String(this.config.destination || '').toUpperCase().trim();
                const domestic = ['CGK', 'SUB', 'DPS', 'UPG', 'BDO', 'JOG', 'YIA', 'SRG', 'SOC', 'LOP', 'BPN', 'PKU', 'PLM', 'PDG', 'BTJ', 'KNO', 'HLP'];
                return !(domestic.includes(origin) && domestic.includes(destination));
            },

            adultPassengerOptions(currentIndex) {
                return (Array.isArray(this.form.paxDetails) ? this.form.paxDetails : [])
                    .map((pax, idx) => ({
                        pax,
                        idx
                    }))
                    .filter(({
                        pax,
                        idx
                    }) => pax.type === 'Adult' && idx !== currentIndex)
                    .map(({
                        pax,
                        idx
                    }) => {
                        const firstName = String(pax.firstName || '').trim();
                        const lastName = String(pax.lastName || '').trim();
                        const fullName = [firstName, lastName].filter(Boolean).join(' ').trim();

                        return {
                            key: `${idx}-${firstName}-${lastName}`,
                            firstName,
                            fullName,
                            label: fullName ? `Adult ${idx + 1} - ${fullName}` : `Adult ${idx + 1}`,
                        };
                    });
            },

            validateContact() {
                if (!this.form.contact.first_name.trim() || !this.form.contact.last_name.trim()) {
                    this.error = this.config.isEn ? 'Contact first name and last name are required.' : 'Nama depan dan nama belakang kontak wajib diisi.';
                    return false;
                }

                if (!this.form.contact.email.trim()) {
                    this.error = this.config.isEn ? 'Contact email is required.' : 'Email kontak wajib diisi.';
                    return false;
                }

                this.normalizePhoneInput();
                if (String(this.form.contact.phone || '').length < 9) {
                    this.error = this.config.isEn ? 'Contact phone number is incomplete.' : 'Nomor telepon kontak belum lengkap.';
                    return false;
                }

                return true;
            },

            validatePassengers() {
                if (!Array.isArray(this.form.paxDetails) || this.form.paxDetails.length === 0) {
                    this.error = this.config.isEn ? 'Passenger data is required.' : 'Data penumpang wajib diisi.';
                    return false;
                }

                for (let i = 0; i < this.form.paxDetails.length; i++) {
                    const pax = this.form.paxDetails[i];
                    const label = pax.ui?.label || `Passenger ${i + 1}`;

                    if (!String(pax.firstName || '').trim() || !String(pax.lastName || '').trim()) {
                        this.error = `${label}: ${this.config.isEn ? 'first name and last name are required.' : 'nama depan dan nama belakang wajib diisi.'}`;
                        return false;
                    }

                    if (!String(pax.birthDate || '').trim()) {
                        this.error = `${label}: ${this.config.isEn ? 'birth date is required.' : 'tanggal lahir wajib diisi.'}`;
                        return false;
                    }

                    if (pax.type === 'Infant') {
                        const parent = String(pax.parent || '').trim();
                        const adultParentCandidates = this.adultPassengerOptions(i)
                            .flatMap(item => [
                                String(item.fullName || '').trim(),
                                String(item.firstName || '').trim(),
                            ])
                            .filter(Boolean);

                        if (!adultParentCandidates.includes(parent)) {
                            this.error = `${label}: ${this.config.isEn ? 'selected parent is invalid.' : 'parent yang dipilih tidak valid.'}`;
                            return false;
                        }

                        if (!parent) {
                            this.error = `${label}: ${this.config.isEn ? 'adult parent must be selected for infant.' : 'penumpang dewasa untuk bayi wajib dipilih.'}`;
                            return false;
                        }
                    }

                    if (!String(pax.nationality || '').trim() || !String(pax.birthCountry || '').trim()) {
                        this.error = `${label}: ${this.config.isEn ? 'nationality and birth country are required.' : 'nationality dan negara lahir wajib diisi.'}`;
                        return false;
                    }
                }

                return true;
            },

            buildAddonRequestPayload() {
                const split = this.splitPhone();

                return {
                    contact: {
                        first_name: this.form.contact.first_name,
                        last_name: this.form.contact.last_name,
                        title: this.form.contact.title,
                        phone: this.form.contact.phone,
                        country_code_phone: split.country_code_phone,
                        area_code_phone: split.area_code_phone,
                        remaining_phone_no: split.remaining_phone_no,
                        email: this.form.contact.email,
                    },
                    insurance: this.form.insurance,
                    paxDetails: this.form.paxDetails.map((pax) => {
                        const copy = JSON.parse(JSON.stringify(pax));
                        delete copy.ui;
                        return copy;
                    })
                };
            },

            async fetchAddonsAndSeat() {
                let res = await fetch(this.config.addonsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.config.csrfToken,
                    },
                    body: JSON.stringify(this.buildAddonRequestPayload())
                });

                let addonData = await res.json();
                if (!res.ok) {
                    throw new Error(addonData.message || addonData.error || addonData.respMessage || 'Gagal ambil baggage/meal.');
                }

                this.addonData = addonData;

                let seatData = {
                    seatAddOns: [],
                    status: 'SUCCESS',
                    respMessage: 'Seat unavailable, continue without seat selection.',
                };

                try {
                    res = await fetch(this.config.seatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken,
                        }
                    });

                    const parsed = await res.json();

                    if (res.ok) {
                        seatData = parsed;
                    } else {
                        console.warn('Seat request failed, continue without seat selection.', parsed);
                    }
                } catch (e) {
                    console.warn('Seat request error, continue without seat selection.', e);
                }

                this.seatData = seatData;
                this.hydrateAddonOptions();
                this.hydrateSeatOptions();
            },

            normalizeObjectKeys(obj) {
                if (!obj || typeof obj !== 'object' || Array.isArray(obj)) return obj;
                const out = {};
                for (const [key, value] of Object.entries(obj)) out[String(key).toLowerCase()] = value;
                return out;
            },

            hydrateAddonOptions() {
                const segmentSources = this.collectSegmentSources();

                for (let i = 0; i < this.form.paxDetails.length; i++) {
                    const pax = this.form.paxDetails[i];

                    pax.ui.segmentAddOns = segmentSources.map(({
                        addonSegment,
                        seatSegment
                    }) => {
                        return this.buildSegmentAddonState(addonSegment, seatSegment);
                    });
                }
            },

            hydrateSeatOptions() {
                const rawSeatAddOns = this.seatData?.data?.seatAddOns || this.seatData?.seatAddOns || [];
                const seatAddOns = Array.isArray(rawSeatAddOns) ? rawSeatAddOns : [];

                for (let i = 0; i < this.form.paxDetails.length; i++) {
                    const pax = this.form.paxDetails[i];

                    if (!Array.isArray(pax.ui.segmentAddOns) || pax.ui.segmentAddOns.length === 0) {
                        pax.ui.segmentAddOns = [];
                    }

                    const currentMap = new Map(
                        pax.ui.segmentAddOns.map((segmentState) => [segmentState.key, segmentState])
                    );

                    seatAddOns.forEach((seatSegment) => {
                        const segmentKey = this.normalizeSegmentKey(
                            seatSegment?.origin,
                            seatSegment?.destination,
                            seatSegment?.departTime || ''
                        );

                        const existing = currentMap.get(segmentKey);

                        if (existing) {
                            currentMap.set(segmentKey, {
                                ...existing,
                                departTime: this.normalizeText(seatSegment?.departTime || existing.departTime || ''),
                                arrivalTime: this.normalizeText(seatSegment?.arrivalTime || existing.arrivalTime || ''),
                                seatOptions: this.buildSeatOptions(seatSegment),
                                selectedSeat: existing.selectedSeat || '',
                                selectedCompartment: existing.selectedCompartment || '',
                                selectedMeals: existing.selectedMeals || [],
                                selectedBaggage: existing.selectedBaggage || '',
                                isEnableNoBaggage: existing.isEnableNoBaggage !== false,
                            });
                            return;
                        }

                        currentMap.set(segmentKey, this.buildSegmentAddonState(null, seatSegment));
                    });

                    pax.ui.segmentAddOns = Array.from(currentMap.values());
                }
            },

            selectSeat(pax, option) {
                pax.ui.selectedSeat = option.value;
                pax.ui.selectedCompartment = option.compartment || '';
            },

            hasBaggageOptions(pax) {
                return Array.isArray(pax.ui?.baggageOptions) && pax.ui.baggageOptions.length > 0;
            },

            hasMealOptions(pax) {
                return Array.isArray(pax.ui?.mealOptions) && pax.ui.mealOptions.length > 0;
            },

            hasSeatOptions(pax) {
                return Array.isArray(pax.ui?.seatOptions) && pax.ui.seatOptions.length > 0;
            },

            applyPassengerAddOns() {
                this.form.paxDetails = this.form.paxDetails.map((pax) => {
                    const addOns = [];

                    (Array.isArray(pax.ui.segmentAddOns) ? pax.ui.segmentAddOns : []).forEach((segment) => {
                        if (!this.hasRealSegmentSelection(segment)) {
                            return;
                        }

                        addOns.push({
                            aoOrigin: segment.origin || '',
                            aoDestination: segment.destination || '',
                            baggageString: segment.selectedBaggage || '',
                            meals: Array.isArray(segment.selectedMeals) ? segment.selectedMeals : [],
                            seat: segment.selectedSeat || '',
                            compartment: segment.selectedCompartment || '',
                        });
                    });

                    return {
                        ...pax,
                        addOns,
                    };
                });
            },

            async continueToAddonStep() {
                if (!this.isRepriced || this.priceStatus !== 'SUCCESS') {
                    this.error = this.config.isEn ? 'Latest airline price is not valid yet.' : 'Harga final airline belum valid.';
                    return;
                }

                this.error = '';

                if (!this.validateContact() || !this.validatePassengers()) {
                    return;
                }

                this.loading = true;

                try {
                    await this.fetchAddonsAndSeat();
                    this.currentStep = 'addons';
                } catch (e) {
                    this.error = e.message || 'Terjadi error saat mengambil add-on dan seat.';
                } finally {
                    this.loading = false;
                }
            },

            async submitBooking() {
                if (!this.isRepriced || this.priceStatus !== 'SUCCESS') {
                    this.error = this.config.isEn ? 'Booking is blocked because Airline/Price has not succeeded.' : 'Booking diblokir karena Airline/Price belum berhasil.';
                    return;
                }

                this.error = '';
                this.loading = true;

                try {
                    this.applyPassengerAddOns();

                    const res = await fetch(this.config.bookUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.config.csrfToken,
                        },
                        body: JSON.stringify({
                            paxDetails: this.form.paxDetails.map((pax) => {
                                const copy = JSON.parse(JSON.stringify(pax));
                                delete copy.ui;
                                return copy;
                            })
                        })
                    });

                    const bookingData = await res.json();

                    if (!res.ok) {
                        throw new Error(bookingData.message || bookingData.error || bookingData.respMessage || 'Gagal booking ke supplier.');
                    }

                    if (bookingData.redirect) {
                        window.location.href = bookingData.redirect;
                        return;
                    }

                    throw new Error('Respon booking supplier tidak sesuai.');
                } catch (e) {
                    this.error = e.message || 'Terjadi error jaringan. Coba lagi.';
                } finally {
                    this.loading = false;
                }
            },

            formatNumber(value) {
                const num = Number(value || 0);
                return new Intl.NumberFormat('id-ID').format(num);
            }
        }
    }
</script>
@endsection