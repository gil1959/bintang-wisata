@extends('layouts.front')

@php
$isEn = app()->getLocale() === 'en';

$search = is_array($search ?? null) ? $search : [];
$journey = is_array($journey ?? null) ? $journey : [];
$cities = is_array($cities ?? null) ? $cities : [];
$key = (string)($key ?? '');

$origin = strtoupper((string) data_get($journey, 'jiOrigin', data_get($search, 'origin', '')));
$destination = strtoupper((string) data_get($journey, 'jiDestination', data_get($search, 'destination', '')));

$departIso = data_get($journey, 'jiDepartTime', null);
$arriveIso = data_get($journey, 'jiArrivalTime', null);

$price = is_array($price ?? null) ? $price : [];
$pricingData = is_array($pricingData ?? null) ? $pricingData : [];

$priceStatus = strtoupper((string) data_get($price, 'status', ''));
$isRepriced = $priceStatus === 'SUCCESS' && is_numeric(data_get($price, 'sumFare'));

$currency = (string) data_get($price, 'currency', data_get($journey, 'currency', 'IDR'));
$repricedFare = data_get($price, 'sumFare', null);
$estimatedFare = data_get($journey, 'sumPrice', null);
$resolvedPrice = data_get($pricingData, 'final_price', null);
$sumPrice = is_numeric($resolvedPrice) ? $resolvedPrice : ($isRepriced ? $repricedFare : $estimatedFare);
$priceLabel = $isRepriced ? ($isEn ? 'Total Price' : 'Total Harga') : ($isEn ? 'Estimated Price' : 'Estimasi Harga');
$isManualPrice = (bool) data_get($pricingData, 'is_manual', false);

$priceDepart = (array) data_get($price, 'priceDepart', []);
$priceReturn = (array) data_get($price, 'priceReturn', []);
$searchKey = (string) data_get($price, 'searchKey', '');

$category = (string) data_get($journey, 'category', '');
$airlineID = strtoupper((string) data_get($journey, 'airlineID', ''));
$journeyReference = (string) data_get($journey, 'journeyReference', '');

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
foreach ($cities as $c) {
$cid = strtoupper((string) data_get($c, 'cityID', ''));
$cname = trim((string) data_get($c, 'cityName', ''));
if ($cid !== '') {
$cityNameMap[$cid] = $cname !== '' ? $cname : $cid;
}
}

$formatCityLabel = function ($code) use ($cityNameMap) {
$code = strtoupper(trim((string) $code));

if ($code === '') {
return '-';
}

$name = trim((string) ($cityNameMap[$code] ?? ''));

if ($name === '') {
return $code;
}

$name = preg_replace('/\s+/', ' ', $name);
$name = preg_replace('/\s*,\s*/', ', ', $name);
$name = preg_replace('/(?<!\s)\( /', ' (' , $name);

    return trim($name) . ' (' . $code . ')' ;
    };

    $firstFlightDetail=(array) data_get($journey, 'segment.0.flightDetail.0' , []);
    $airlineCode=strtoupper((string) data_get($firstFlightDetail, 'airlineCode' , $airlineID));
    $flightNumber=trim((string) data_get($firstFlightDetail, 'flightNumber' , '' ));
    $airlineFullName=(string) ($airlineNameMap[$airlineCode] ?? $airlineCode);
    $displayAirline=trim($airlineFullName . ($flightNumber !=='' ? ' ' . $flightNumber : '' ));
    $displayOrigin=$formatCityLabel($origin);
    $displayDestination=$formatCityLabel($destination);

    $segments=(array) data_get($journey, 'segment' , []);
    $segmentCount=count($segments);
    $stops=max(0, $segmentCount - 1);

    $paxAdult=(int) data_get($search, 'paxAdult' , 1);
    $paxChild=(int) data_get($search, 'paxChild' , 0);
    $paxInfant=(int) data_get($search, 'paxInfant' , 0);
    $totalPax=max(1, $paxAdult + $paxChild + $paxInfant);

    $safeParse=function ($value) {
    try {
    if (!filled($value)) return null;
    return \Carbon\Carbon::parse($value);
    } catch (\Throwable $e) {
    return null;
    }
    };

    $fmtTime=function ($value) use ($safeParse) {
    $c=$safeParse($value);
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

    $pageTitle = $isEn
    ? "Flight Detail {$origin} - {$destination}"
    : "Detail Tiket {$origin} - {$destination}";
    @endphp

    @section('title', $pageTitle)

    @section('content')

    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- ================= LEFT CONTENT ================= --}}
        <div class="md:col-span-2 space-y-8">

            {{-- Breadcrumb / Back --}}
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('flights.index', $search) }}"
                    class="btn btn-ghost inline-flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>{{ $isEn ? 'Back to Results' : 'Kembali ke Hasil' }}</span>
                </a>

                <a href="{{ route('flights.index') }}"
                    class="btn btn-ghost inline-flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>{{ $isEn ? 'New Search' : 'Cari Baru' }}</span>
                </a>
            </div>

            {{-- Header summary --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">
                    <div class="min-w-0 flex-1">
                        <div class="space-y-2">
                            <div class="text-sm font-semibold text-slate-500">
                                {{ $isEn ? 'Flight Detail' : 'Detail Tiket Pesawat' }}
                            </div>

                            <h1 class="text-2xl md:text-3xl font-extrabold leading-tight text-slate-900">
                                {{ $isEn ? 'Flight Ticket' : 'Tiket Pesawat' }}
                            </h1>

                            <div class="text-xl md:text-2xl font-extrabold leading-snug text-[#0194F3] break-words">
                                {{ $displayOrigin }}
                                <span class="text-slate-400 font-bold mx-1">→</span>
                                {{ $displayDestination }}
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-2">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span class="font-bold text-slate-700">{{ $fmtDate($departIso ?: data_get($search,'departDate')) }}</span>
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-2">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                <span class="font-bold text-slate-700">
                                    {{ $fmtTime($departIso) }} - {{ $fmtTime($arriveIso) }}
                                </span>
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-2">
                                <i data-lucide="timer" class="w-4 h-4"></i>
                                <span class="font-bold text-slate-700">{{ $fmtDuration($departIso, $arriveIso) }}</span>
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-2">
                                <i data-lucide="route" class="w-4 h-4"></i>
                                <span class="font-bold text-slate-700">
                                    {{ $stops === 0 ? ($isEn ? 'Direct Flight' : 'Penerbangan Langsung') : ($stops . ' ' . ($isEn ? 'Transit Stop' : 'Transit')) }}
                                </span>
                            </span>

                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 border border-slate-200 px-3 py-2">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                <span class="font-bold text-slate-700">{{ $totalPax }} {{ $isEn ? 'Passenger' : 'Penumpang' }}</span>
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($displayAirline !== '')
                            <span class="inline-flex items-center gap-2 rounded-full bg-sky-50 border border-sky-200 px-4 py-2 text-sm font-bold text-sky-900">
                                <i data-lucide="plane" class="w-4 h-4"></i>
                                {{ $isEn ? 'Flight' : 'Penerbangan' }}: {{ $displayAirline }}
                            </span>
                            @endif
                        </div>

                        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                            <div class="flex items-start gap-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 mt-0.5"></i>
                                <div>
                                    <div class="font-extrabold text-sm">
                                        {{ $isEn ? 'Important' : 'Penting' }}
                                    </div>
                                    <div class="mt-1 text-sm leading-6">
                                        {{ $isEn
                                ? 'Flight prices are dynamic and may change during the booking process.'
                                : 'Harga tiket pesawat bersifat dinamis dan bisa berubah saat proses booking.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="xl:w-[220px] xl:flex-shrink-0">
                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 xl:text-right">
                            <div class="flex items-center gap-2 xl:justify-end">
                                <div class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    {{ $priceLabel }}
                                </div>

                                @if($isManualPrice)

                                @endif
                            </div>

                            <div class="mt-2 text-3xl font-extrabold leading-tight text-rose-600 break-words">
                                {{ $fmtMoney($sumPrice, $currency) }}
                            </div>



                        </div>
                    </div>
                </div>
            </section>



            {{-- Segments --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-xl font-extrabold mb-5 text-[#0194F3] flex items-center gap-2">
                    <i data-lucide="list" class="w-5 h-5 text-[#0194F3]"></i>
                    <span>{{ $isEn ? 'Flight Segments' : 'Rincian Penerbangan' }}</span>
                </h2>

                @if($segmentCount === 0)
                <div class="text-sm text-slate-600">
                    {{ $isEn ? 'No segment detail available.' : 'Detail segment tidak tersedia.' }}
                </div>
                @else
                <div class="space-y-4">
                    @foreach($segments as $idx => $seg)
                    @php
                    $flightDetails = (array) data_get($seg, 'flightDetail', []);
                    $availableDetails = (array) data_get($seg, 'availableDetail', []);

                    $firstFd = (array) data_get($flightDetails, '0', []);
                    $fdOrigin = (string) data_get($firstFd, 'fdOrigin', '');
                    $fdDestination = (string) data_get($firstFd, 'fdDestination', '');
                    $fdDepartTime = data_get($firstFd, 'fdDepartTime', null);
                    $fdArrivalTime = data_get($firstFd, 'fdArrivalTime', null);

                    // Transit label
                    $legTitle = $isEn ? ('Leg ' . ($idx + 1)) : ('Penerbangan ' . ($idx + 1));
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-base font-extrabold text-slate-900 leading-snug">
                                    {{ $legTitle }}
                                </div>

                                @if($fdOrigin !== '' && $fdDestination !== '')
                                <div class="mt-1 text-sm font-bold text-[#0194F3] leading-6 break-words">
                                    {{ $formatCityLabel($fdOrigin) }} → {{ $formatCityLabel($fdDestination) }}
                                </div>
                                @endif

                                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                        {{ $fmtTime($fdDepartTime) }} - {{ $fmtTime($fdArrivalTime) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="timer" class="w-4 h-4"></i>
                                        {{ $fmtDuration($fdDepartTime, $fdArrivalTime) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                        {{ $fmtDate($fdDepartTime) }}
                                    </span>
                                </div>
                            </div>

                            <div class="lg:text-right">
                                <div class="text-xs text-slate-500 font-bold uppercase tracking-wide">
                                    {{ $isEn ? 'Info' : 'Info' }}
                                </div>
                                <div class="mt-1 text-sm text-slate-700">
                                    {{ $isEn ? 'Transit:' : 'Transit:' }} <span class="font-extrabold">{{ $stops }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Flight details list --}}
                        <div class="mt-4 space-y-3">
                            @foreach($flightDetails as $fdIdx => $fd)
                            @php
                            $airlineCode = (string) data_get($fd, 'airlineCode', '');
                            $flightNumber = (string) data_get($fd, 'flightNumber', '');
                            $dpt = data_get($fd, 'fdDepartTime', null);
                            $arr = data_get($fd, 'fdArrivalTime', null);
                            $o = (string) data_get($fd, 'fdOrigin', '');
                            $d = (string) data_get($fd, 'fdDestination', '');
                            $routeInfo = (string) data_get($fd, 'routeInfo', '');
                            $passportRequired = (bool) data_get($fd, 'passportRequired', false);
                            @endphp

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    @php
                                    $segmentAirlineCode = strtoupper((string) data_get($fd, 'airlineCode', ''));
                                    $segmentFlightNumber = trim((string) data_get($fd, 'flightNumber', ''));
                                    $segmentAirlineFullName = (string) ($airlineNameMap[$segmentAirlineCode] ?? $segmentAirlineCode);
                                    $segmentDisplayAirline = trim($segmentAirlineFullName . ($segmentFlightNumber !== '' ? ' ' . $segmentFlightNumber : ''));
                                    @endphp

                                    <div class="inline-flex items-center gap-2 text-base font-extrabold text-slate-900">
                                        <i data-lucide="plane" class="w-4 h-4" style="color:#0194F3;"></i>
                                        <span>
                                            {{ $isEn ? 'Flight' : 'Penerbangan' }}:
                                            {{ $segmentDisplayAirline !== '' ? $segmentDisplayAirline : ($isEn ? 'Not available' : 'Tidak tersedia') }}
                                        </span>
                                    </div>

                                    @if($passportRequired)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-extrabold text-amber-900">
                                        <i data-lucide="passport" class="w-3.5 h-3.5"></i>
                                        {{ $isEn ? 'Passport required' : 'Butuh paspor' }}
                                    </span>
                                    @endif
                                </div>

                                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                    <div class="rounded-2xl bg-white border border-slate-200 p-4">
                                        <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $isEn ? 'Depart' : 'Berangkat' }}</div>
                                        <div class="mt-2 text-sm font-bold leading-6 text-slate-700 break-words">
                                            {{ $formatCityLabel($o) }}
                                        </div>
                                        <div class="mt-2 text-xl font-extrabold text-slate-900">
                                            {{ $fmtTime($dpt) }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $fmtDate($dpt) }}</div>
                                    </div>

                                    <div class="rounded-2xl bg-white border border-slate-200 p-4">
                                        <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $isEn ? 'Duration' : 'Durasi' }}</div>
                                        <div class="mt-2 text-xl font-extrabold text-slate-900">
                                            {{ $fmtDuration($dpt, $arr) }}
                                        </div>
                                        <div class="mt-2 text-sm leading-6 text-slate-500 break-words">
                                            {{ $formatCityLabel($o) }} → {{ $formatCityLabel($d) }}
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-white border border-slate-200 p-4">
                                        <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">{{ $isEn ? 'Arrive' : 'Tiba' }}</div>
                                        <div class="mt-2 text-sm font-bold leading-6 text-slate-700 break-words">
                                            {{ $formatCityLabel($d) }}
                                        </div>
                                        <div class="mt-2 text-xl font-extrabold text-slate-900">
                                            {{ $fmtTime($arr) }}
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $fmtDate($arr) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Available classes --}}
                        @if(count($availableDetails) > 0)
                        <div class="mt-5">
                            <div class="text-sm font-extrabold text-slate-700">
                                {{ $isEn ? 'Cabin Class' : 'Kelas Kabin' }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700">
                                    <i data-lucide="badge-check" class="w-4 h-4" style="color:#0194F3;"></i>
                                    {{ $isEn ? 'Economy Class' : 'Kelas Ekonomi' }}
                                </span>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endforeach
                </div>
                @endif
            </section>

            {{-- Notes --}}
            <section class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold mb-3 text-[#0194F3] flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-[#0194F3]"></i>
                    <span>{{ $isEn ? 'Notes' : 'Catatan' }}</span>
                </h2>

                <ul class="list-disc pl-5 space-y-2 text-sm text-slate-700">
                    <li>
                        {{ $isEn
            ? 'Please make sure passenger names match the ID or passport exactly.'
            : 'Pastikan nama penumpang sama persis dengan KTP atau paspor.' }}
                    </li>
                    <li>
                        {{ $isEn
            ? 'After checkout, availability and fare will follow the latest airline confirmation.'
            : 'Setelah checkout, ketersediaan kursi dan harga mengikuti konfirmasi terbaru dari maskapai.' }}
                    </li>
                </ul>
            </section>

        </div>

        {{-- ================= SIDEBAR ================= --}}
        <aside class="md:col-span-1 space-y-6">
            @include('front.flights.partials.reservation', [
            'key' => $key,
            'search' => $search,
            'journey' => $journey,
            'price' => $price,
            'priceError' => $priceError,
            'cities' => $cities,
            ])
        </aside>

    </div>

    @endsection