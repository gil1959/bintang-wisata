@php
$supplierBook = (array) ($supplierBook ?? []);
$supplierDetail = (array) ($supplierDetail ?? []);
$supplierStatus = (array) ($supplierStatus ?? []);
$showPrintHeader = $showPrintHeader ?? true;
$showQrSection = $showQrSection ?? true;

$ticketHtml = $supplierDetail['ticketDetail'] ?? '';
$flightDeparts = (array) ($supplierDetail['flightDeparts'] ?? []);
$flightReturns = (array) ($supplierDetail['flightReturns'] ?? []);
$passengers = (array) ($supplierDetail['passengers'] ?? []);
$referenceNo = $supplierDetail['referenceNo'] ?? ($supplierBook['referenceNo'] ?? '');
$issuedDate = $supplierDetail['issuedDate'] ?? '';
$ticketStatus = strtoupper((string) ($supplierStatus['ticket_status'] ?? ($supplierDetail['ticketStatus'] ?? '')));
$airlineName = $supplierDetail['airline'] ?? ($supplierBook['airlineID'] ?? 'Airline');
$airlineCode = $supplierBook['airlineID'] ?? ($supplierDetail['airlineID'] ?? ($supplierDetail['airline'] ?? ''));
$bookingCode = $supplierBook['bookingCode'] ?? ($supplierDetail['bookingCode'] ?? '');
$routeTitle = trim(($supplierDetail['origin'] ?? '-') . ' → ' . ($supplierDetail['destination'] ?? '-'));

$qrValue = trim(implode(' | ', array_filter([
$bookingCode ? 'BOOK:' . $bookingCode : null,
$referenceNo ? 'REF:' . $referenceNo : null,
(!empty($supplierDetail['origin']) && !empty($supplierDetail['destination']))
? ($supplierDetail['origin'] . '-' . $supplierDetail['destination'])
: null,
])));

$kelasMap = [
'F' => 'First Class', 'A' => 'First Class',
'C' => 'Business', 'D' => 'Business', 'I' => 'Business', 'Z' => 'Business',
'Y' => 'Economy', 'B' => 'Economy', 'M' => 'Economy', 'H' => 'Economy',
'K' => 'Economy', 'L' => 'Economy', 'Q' => 'Economy', 'T' => 'Economy',
'N' => 'Economy', 'R' => 'Economy', 'S' => 'Economy', 'V' => 'Economy',
'W' => 'Economy', 'X' => 'Economy', 'O' => 'Economy', 'P' => 'Economy',
'G' => 'Economy', 'E' => 'Economy',
];
@endphp

<style>
    /* ─── TICKET WRAPPER ─────────────────────────────────────────── */
    .bw-ticket {
        border: 1px solid #dbe5ef;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        /* Prevent collapsing below readable width in narrow containers */
        min-width: 320px;
    }

    /* ─── HEADER ─────────────────────────────────────────────────── */
    .bw-ticket__head {
        padding: 20px 24px;
        background: linear-gradient(135deg, #f0f8ff 0%, #f8fbff 60%, #eef6ff 100%);
        border-bottom: 1px solid #dbe5ef;
    }

    .bw-ticket__head-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, 260px);
        gap: 20px;
        align-items: start;
    }

    .bw-ticket__eyebrow {
        font-size: 11px;
        line-height: 16px;
        letter-spacing: .12em;
        text-transform: uppercase;
        font-weight: 700;
        color: #0194F3;
        margin-bottom: 6px;
    }

    /* FIXED: removed word-break: break-word that caused per-character wrapping */
    .bw-ticket__title {
        font-size: 20px;
        line-height: 1.25;
        font-weight: 800;
        color: #0f172a;
        overflow-wrap: break-word;
        word-break: normal;
        hyphens: auto;
    }

    .bw-ticket__subtitle {
        margin-top: 6px;
        font-size: 14px;
        line-height: 20px;
        font-weight: 500;
        color: #64748b;
    }

    /* ─── META (right side of header) ───────────────────────────── */
    .bw-ticket__meta {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 132px;
        align-items: start;
        gap: 14px;
        min-width: 0;
    }

    .bw-ticket__meta-text {
        text-align: right;
        min-width: 0;
    }

    .bw-ticket__status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 999px;
        border: 1.5px solid #6ee7b7;
        background: #ecfdf5;
        color: #065f46;
        font-size: 11px;
        letter-spacing: .06em;
        font-weight: 800;
        text-transform: uppercase;
    }

    .bw-ticket__label {
        margin-top: 10px;
        font-size: 11px;
        line-height: 16px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .bw-ticket__value {
        font-size: 18px;
        line-height: 1.2;
        color: #0f172a;
        font-weight: 800;
        /* FIXED: prevent giant booking codes from wrapping oddly */
        white-space: nowrap;
    }

    .bw-ticket__ref {
        margin-top: 3px;
        font-size: 12px;
        line-height: 16px;
        color: #94a3b8;
        white-space: nowrap;
    }

    /* ─── HEADER QR (RIGHT SIDE, SINGLE QR ONLY) ─────────────────── */
    .bw-ticket__qr {
        width: 132px;
        min-width: 132px;
        height: 132px;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        border-radius: 0;
        overflow: visible;
        padding: 0;
        margin-left: auto;
    }

    .bw-ticket__qr canvas,
    .bw-ticket__qr img {
        width: 132px !important;
        height: 132px !important;
        max-width: 132px !important;
        max-height: 132px !important;
        display: block !important;
        flex-shrink: 0 !important;
    }

    /* ─── BODY ───────────────────────────────────────────────────── */
    .bw-ticket__body {
        padding: 20px 24px 24px;
    }

    /* ─── FLIGHT SECTION ─────────────────────────────────────────── */
    .bw-ticket__section {
        border: 1px solid #dbe5ef;
        background: #f8fafc;
        border-radius: 16px;
        padding: 16px 20px;
    }

    .bw-ticket__section-title {
        font-size: 11px;
        line-height: 16px;
        letter-spacing: .08em;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 14px;
    }

    /* FIXED: explicit min-width on columns so they never collapse to 0 */
    .bw-ticket__flight-grid {
        display: grid;
        grid-template-columns: minmax(90px, 140px) 1fr minmax(90px, 140px);
        gap: 12px;
        align-items: center;
    }

    .bw-ticket__time {
        font-size: 26px;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -.02em;
    }

    .bw-ticket__airport {
        margin-top: 2px;
        font-size: 13px;
        line-height: 1;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .bw-ticket__date {
        margin-top: 8px;
        font-size: 13px;
        line-height: 18px;
        font-weight: 500;
        color: #64748b;
    }

    .bw-ticket__center {
        min-width: 0;
        text-align: center;
    }

    /* ─── FLIGHT ROUTE LINE ──────────────────────────────────────── */
    .bw-ticket__line {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bw-ticket__line::before,
    .bw-ticket__line::after {
        content: "";
        flex: 1 1 auto;
        height: 1.5px;
        background: linear-gradient(90deg, #cbd5e1, #94a3b8);
        border-radius: 999px;
    }

    .bw-ticket__flight-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #0194F3;
        flex-shrink: 0;
    }

    /* FIXED: plane icon is now sized consistently */
    .bw-ticket__plane-icon {
        display: block;
        flex-shrink: 0;
        color: #0194F3;
    }

    .bw-ticket__flight-no {
        font-size: 14px;
        line-height: 1;
        color: #0f172a;
        font-weight: 700;
        white-space: nowrap;
    }

    .bw-ticket__class {
        margin-top: 8px;
        text-align: center;
        font-size: 13px;
        line-height: 18px;
        color: #64748b;
        font-weight: 500;
    }

    /* ─── PASSENGER LIST ─────────────────────────────────────────── */
    .bw-ticket__passenger-box {
        border: 1px solid #dbe5ef;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .bw-ticket__passenger-head {
        padding: 13px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #dbe5ef;
        font-size: 14px;
        line-height: 20px;
        font-weight: 700;
        color: #0f172a;
    }

    .bw-ticket__pax-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 13px 18px;
        border-top: 1px solid #f1f5f9;
    }

    .bw-ticket__pax-row:first-of-type {
        border-top: 0;
    }

    .bw-ticket__pax-name {
        font-size: 15px;
        line-height: 22px;
        font-weight: 600;
        color: #0f172a;
        overflow-wrap: break-word;
    }

    .bw-ticket__pax-type {
        font-size: 13px;
        line-height: 18px;
        font-weight: 500;
        color: #64748b;
        white-space: nowrap;
    }

    /* ─── FOOTER GRID ────────────────────────────────────────────── */
    .bw-ticket__foot-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .bw-ticket__foot-card {
        border: 1px solid #dbe5ef;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px 16px;
        min-width: 0;
    }

    .bw-ticket__foot-label {
        font-size: 11px;
        line-height: 16px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .bw-ticket__foot-value {
        margin-top: 6px;
        font-size: 15px;
        line-height: 20px;
        color: #0f172a;
        font-weight: 700;
        overflow-wrap: break-word;
    }

    /* ─── RESPONSIVE ─────────────────────────────────────────────── */
    @media (max-width: 640px) {

        .bw-ticket__head,
        .bw-ticket__body {
            padding: 16px;
        }

        .bw-ticket__head-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .bw-ticket__meta {
            align-items: flex-start;
            min-width: 0;
        }

        .bw-ticket__meta-text {
            text-align: left;
        }

        .bw-ticket__meta {
            grid-template-columns: minmax(0, 1fr) 120px;
            align-items: start;
        }

        .bw-ticket__qr {
            width: 120px;
            min-width: 120px;
            height: 120px;
        }

        .bw-ticket__qr canvas,
        .bw-ticket__qr img {
            width: 120px !important;
            height: 120px !important;
            max-width: 120px !important;
            max-height: 120px !important;
        }

        .bw-ticket__title {
            font-size: 18px;
        }

        .bw-ticket__time {
            font-size: 22px;
        }

        .bw-ticket__foot-grid {
            grid-template-columns: 1fr;
        }

        .bw-ticket__pax-row {
            grid-template-columns: 1fr;
            gap: 2px;
        }

        .bw-ticket__pax-type {
            white-space: normal;
        }
    }
</style>

<div class="bw-ticket">

    {{-- ═══════════════ HEADER ═══════════════ --}}
    <div class="bw-ticket__head">
        <div class="bw-ticket__head-grid">

            {{-- Left: title + airline --}}
            <div>
                @if($showPrintHeader)
                <div class="bw-ticket__eyebrow">E-Ticket</div>
                @endif
                <div class="bw-ticket__title">{{ $routeTitle }}</div>
                <div class="bw-ticket__subtitle">
                    {{ $airlineName }} · {{ strtoupper((string) ($supplierDetail['tripType'] ?? '-')) }}
                </div>
            </div>

            {{-- Right: status + booking code only --}}
            <div class="bw-ticket__meta">
                <div class="bw-ticket__meta-text">
                    @if($ticketStatus)
                    <span class="bw-ticket__status">{{ $ticketStatus }}</span>
                    @endif

                    @if($bookingCode)
                    <div class="bw-ticket__label">Booking Code</div>
                    <div class="bw-ticket__value">{{ $bookingCode }}</div>
                    @endif

                    @if($referenceNo)
                    <div class="bw-ticket__ref">Ref: {{ $referenceNo }}</div>
                    @endif
                </div>

                @if($showQrSection && $qrValue)
                <div class="bw-ticket__qr" data-ticket-qr="{{ e($qrValue) }}" data-ticket-qr-size="132"></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════ BODY ═══════════════ --}}
    <div class="bw-ticket__body">

        @if(!empty($ticketHtml))
        {{-- Raw supplier HTML --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs overflow-auto">
            {!! $ticketHtml !!}
        </div>

        @else
        <div class="space-y-3">

            {{-- ─── DEPARTURE FLIGHTS ─────────────────────── --}}
            @foreach($flightDeparts as $fd)
            @php
            $kr = strtoupper(trim((string) ($fd['fdFlightClass'] ?? '')));
            $kelas = $kelasMap[$kr] ?? 'Economy';
            @endphp

            <div class="bw-ticket__section">
                <div class="bw-ticket__section-title">Penerbangan Pergi</div>

                <div class="bw-ticket__flight-grid">

                    {{-- DEP --}}
                    <div>
                        <div class="bw-ticket__time">{{ \Carbon\Carbon::parse($fd['fdDepartTime'])->format('H:i') }}</div>
                        <div class="bw-ticket__airport">{{ $fd['fdOrigin'] ?? '-' }}</div>
                        @if(!empty($fd['departTerminal']))
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Terminal {{ $fd['departTerminal'] }}</div>
                        @endif
                        <div class="bw-ticket__date">{{ \Carbon\Carbon::parse($fd['fdDepartTime'])->translatedFormat('d F Y') }}</div>
                    </div>

                    {{-- CENTER: flight number + class --}}
                    <div class="bw-ticket__center">
                        <div class="bw-ticket__line">
                            <span class="bw-ticket__flight-badge">
                                {{-- FIXED: plane faces RIGHT for departure --}}
                                <svg class="bw-ticket__plane-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18" height="18"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    style="transform:rotate(90deg);"
                                    aria-hidden="true">
                                    <path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9L2 14v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16Z" />
                                </svg>
                                <span class="bw-ticket__flight-no">{{ $airlineCode ? $airlineCode . '-' : '' }}{{ ltrim(str_replace('+', '', (string) ($fd['flightNumber'] ?? '-')), ' -') }}</span>
                            </span>
                        </div>
                        <div class="bw-ticket__class">{{ $kelas }}</div>
                        <div style="margin-top:6px; font-size:11px; color:#64748b; line-height:1.4;">
                            <div><span style="font-weight:600;">Bagasi:</span> {{ !empty($fd['fdBaggage']) ? $fd['fdBaggage'] : '20 KG' }}</div>
                            <div><span style="font-weight:600;">Kabin:</span> 7 KG</div>
                        </div>
                    </div>

                    {{-- ARR --}}
                    <div style="text-align:right;">
                        <div class="bw-ticket__time">{{ \Carbon\Carbon::parse($fd['fdArrivalTime'])->format('H:i') }}</div>
                        <div class="bw-ticket__airport">{{ $fd['fdDestination'] ?? '-' }}</div>
                        @if(!empty($fd['arriveTerminal']))
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Terminal {{ $fd['arriveTerminal'] }}</div>
                        @endif
                        <div class="bw-ticket__date">{{ \Carbon\Carbon::parse($fd['fdArrivalTime'])->translatedFormat('d F Y') }}</div>
                    </div>

                </div>
            </div>
            @endforeach

            {{-- ─── RETURN FLIGHTS ────────────────────────── --}}
            @foreach($flightReturns as $fr)
            @php
            $kr = strtoupper(trim((string) ($fr['fdFlightClass'] ?? '')));
            $kelas = $kelasMap[$kr] ?? 'Economy';
            @endphp

            <div class="bw-ticket__section">
                <div class="bw-ticket__section-title">Penerbangan Pulang</div>

                <div class="bw-ticket__flight-grid">

                    {{-- DEP --}}
                    <div>
                        <div class="bw-ticket__time">{{ \Carbon\Carbon::parse($fr['fdDepartTime'])->format('H:i') }}</div>
                        <div class="bw-ticket__airport">{{ $fr['fdOrigin'] ?? '-' }}</div>
                        @if(!empty($fr['departTerminal']))
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Terminal {{ $fr['departTerminal'] }}</div>
                        @endif
                        <div class="bw-ticket__date">{{ \Carbon\Carbon::parse($fr['fdDepartTime'])->translatedFormat('d F Y') }}</div>
                    </div>

                    {{-- CENTER: flight number + class --}}
                    <div class="bw-ticket__center">
                        <div class="bw-ticket__line">
                            <span class="bw-ticket__flight-badge">
                                {{-- FIXED: plane faces LEFT for return --}}
                                <svg class="bw-ticket__plane-icon"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18" height="18"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    style="transform:rotate(-90deg);"
                                    aria-hidden="true">
                                    <path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9L2 14v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16Z" />
                                </svg>
                                <span class="bw-ticket__flight-no">{{ $airlineCode ? $airlineCode . '-' : '' }}{{ ltrim(str_replace('+', '', (string) ($fr['flightNumber'] ?? '-')), ' -') }}</span>
                            </span>
                        </div>
                        <div class="bw-ticket__class">{{ $kelas }}</div>
                        <div style="margin-top:6px; font-size:11px; color:#64748b; line-height:1.4;">
                            <div><span style="font-weight:600;">Bagasi:</span> {{ !empty($fr['fdBaggage']) ? $fr['fdBaggage'] : '20 KG' }}</div>
                            <div><span style="font-weight:600;">Kabin:</span> 7 KG</div>
                        </div>
                    </div>

                    {{-- ARR --}}
                    <div style="text-align:right;">
                        <div class="bw-ticket__time">{{ \Carbon\Carbon::parse($fr['fdArrivalTime'])->format('H:i') }}</div>
                        <div class="bw-ticket__airport">{{ $fr['fdDestination'] ?? '-' }}</div>
                        @if(!empty($fr['arriveTerminal']))
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Terminal {{ $fr['arriveTerminal'] }}</div>
                        @endif
                        <div class="bw-ticket__date">{{ \Carbon\Carbon::parse($fr['fdArrivalTime'])->translatedFormat('d F Y') }}</div>
                    </div>

                </div>
            </div>
            @endforeach

        </div>

        {{-- ─── PASSENGER LIST ────────────────────────────── --}}
        @if(!empty($passengers))
        @php
            $orderMeta = (isset($order) && is_object($order)) ? ((array) $order->meta) : [];
            $orderPaxDetails = (array) ($orderMeta['paxDetails'] ?? []);
            
            $findOrderPax = function($p) use ($orderPaxDetails) {
                $pFirst = strtolower(trim((string)($p['firstName'] ?? '')));
                $pLast = strtolower(trim((string)($p['lastName'] ?? '')));
                
                foreach($orderPaxDetails as $idx => $op) {
                    $opFirst = strtolower(trim((string)($op['firstName'] ?? '')));
                    $opLast = strtolower(trim((string)($op['lastName'] ?? '')));
                    if ($pFirst === $opFirst && $pLast === $opLast) {
                        $op['_index'] = $idx;
                        return $op;
                    }
                }
                return null;
            };

            $isZeroBased = in_array(strtoupper($supplierBook['airlineID'] ?? $supplierDetail['airline'] ?? ''), ['QG', 'QZ']);
            
            $getAdultName = function($parentSeq) use ($orderPaxDetails, $isZeroBased) {
                if ($parentSeq === null || $parentSeq === '') return null;
                if (!is_numeric($parentSeq)) return $parentSeq;
                $seq = (int)$parentSeq;
                $adultIndex = $isZeroBased ? $seq : ($seq - 1);
                
                $adults = array_values(array_filter($orderPaxDetails, fn($p) => strtolower($p['type'] ?? '') === 'adult'));
                if (isset($adults[$adultIndex])) {
                    $title = $adults[$adultIndex]['title'] ?? '';
                    $first = $adults[$adultIndex]['firstName'] ?? '';
                    $last = $adults[$adultIndex]['lastName'] ?? '';
                    return trim("$title $first $last");
                }
                return "Adult #$seq";
            };

            $addonsResp = $orderMeta['addons_response']['addOns'] ?? [];
            $getBaggageDesc = function($code) use ($addonsResp) {
                if (empty($code)) return $code;
                foreach ($addonsResp as $ao) {
                    foreach ($ao['baggageInfos'] ?? [] as $bag) {
                        if (($bag['code'] ?? '') === $code) return $bag['desc'] ?? $code;
                    }
                }
                return $code;
            };
            
            $getMealDesc = function($code) use ($addonsResp) {
                if (empty($code)) return $code;
                foreach ($addonsResp as $ao) {
                    foreach ($ao['mealInfos'] ?? [] as $ml) {
                        if (($ml['code'] ?? '') === $code) return $ml['desc'] ?? $code;
                    }
                }
                return $code;
            };
        @endphp
        <div class="bw-ticket__passenger-box" style="margin-top:14px;">
            <div class="bw-ticket__passenger-head">Penumpang</div>
            @foreach($passengers as $pax)
            @php
                $opax = $findOrderPax($pax) ?: [];
                $paxType = ucfirst(strtolower($pax['type'] ?? '-'));
                $paxName = trim(($pax['title'] ?? '') . ' ' . ($pax['firstName'] ?? '') . ' ' . ($pax['lastName'] ?? ''));
                
                $parentSeq = $opax['parent'] ?? $pax['parent'] ?? null;
                $parentName = null;
                if ((strtolower($pax['type'] ?? '') === 'infant' || strtolower($opax['type'] ?? '') === 'infant') && $parentSeq !== null && $parentSeq !== '') {
                    $parentName = $getAdultName($parentSeq);
                }
            @endphp
            <div class="bw-ticket__pax-row" style="grid-template-columns: minmax(0, 1fr);">
                <div class="bw-ticket__pax-name">
                    {{ $paxName }} <span style="font-weight:normal; font-size:13px; color:#64748b; margin-left:4px;">({{ $paxType }})</span>
                    @if($parentName)
                        <div style="font-size:12px; font-weight:500; color:#0194F3; margin-top:2px;">
                            Dipangku oleh: {{ $parentName }}
                        </div>
                    @endif
                    <div style="font-size:12.5px; font-weight:600; color:#475569; margin-top:6px;">
                        E-Ticket: <span style="color:#0f172a; font-weight:700;">{{ $pax['ticketNo'] ?? $opax['ticketNo'] ?? $referenceNo ?? 'PENDING' }}</span>
                    </div>
                </div>

                {{-- ADDONS --}}
                @if(!empty($opax['addOns']) && is_array($opax['addOns']))
                <div style="grid-column: 1 / -1; margin-top: 4px; padding-top: 8px; border-top: 1px dashed #e2e8f0; font-size: 13px;">
                    <div style="font-weight:700; color:#64748b; margin-bottom:4px; font-size:11px; text-transform:uppercase; letter-spacing:0.04em;">Add-ons:</div>
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 8px;">
                    @foreach($opax['addOns'] as $addon)
                        @php
                            $origin = strtoupper(trim((string)($addon['aoOrigin'] ?? $addon['origin'] ?? '')));
                            $dest = strtoupper(trim((string)($addon['aoDestination'] ?? $addon['destination'] ?? '')));
                            $baggageCode = trim((string)($addon['baggageString'] ?? ''));
                            $baggage = $getBaggageDesc($baggageCode);
                            
                            $mealsRaw = (array)($addon['meals'] ?? []);
                            $meals = [];
                            foreach ($mealsRaw as $mCode) {
                                $meals[] = $getMealDesc($mCode);
                            }
                            
                            $seat = trim((string)($addon['seat'] ?? ''));
                            $compartment = trim((string)($addon['compartment'] ?? ''));
                            if (empty($baggage) && empty($meals) && empty($seat)) continue;
                        @endphp
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px;">
                            <div style="font-weight:700; color:#0f172a; font-size:12px; margin-bottom:4px;">{{ $origin }} → {{ $dest }}</div>
                            <div style="color:#475569; font-size:12px; line-height:1.5;">
                                @php $addonTexts = []; @endphp
                                @if(!empty($baggage))
                                    @php $addonTexts[] = "Bagasi: " . $baggage; @endphp
                                @endif
                                @if(!empty($meals))
                                    @php $addonTexts[] = "Meal: " . implode(', ', $meals); @endphp
                                @endif
                                @if(!empty($seat))
                                    @php $addonTexts[] = "Seat: " . $seat . (!empty($compartment) ? " ($compartment)" : ""); @endphp
                                @endif
                                {{ implode(', ', $addonTexts) }}
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
        @endif



        {{-- ─── FOOTER NOTES ──────────────────────────────── --}}
        <div style="margin-top:32px; padding-top:24px; border-top:1px solid #dbe5ef;">
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px; text-align:center;">
                
                {{-- Note 1 --}}
                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 10h2"/><path d="M16 14h2"/><path d="M6.17 15a3 3 0 0 1 5.66 0"/><circle cx="9" cy="11" r="2"/><rect x="2" y="5" width="20" height="14" rx="2"/>
                    </svg>
                    <div style="font-size:12.5px; color:#4f6e9b; font-weight:500; line-height:1.5;">
                        Tunjukan E-Tiket dan Identitas Para Penumpang Saat Check-in
                    </div>
                </div>

                {{-- Note 2 --}}
                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><path d="M5 3 2 6"/><path d="m22 6-3-3"/><path d="M6.38 18.7 4 21"/><path d="M17.64 18.67 20 21"/>
                    </svg>
                    <div style="font-size:12.5px; color:#4f6e9b; font-weight:500; line-height:1.5;">
                        Check-in Paling Lambat 90 Menit Sebelum Keberangkatan
                    </div>
                </div>

                {{-- Note 3 --}}
                <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                    <div style="font-size:12.5px; color:#4f6e9b; font-weight:500; line-height:1.5;">
                        Waktu Tertera Adalah Waktu Bandara Setempat
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── FOOTER: booking / ref / issued ────────────── --}}
        <div class="bw-ticket__foot-grid" style="margin-top:24px;">
            <div class="bw-ticket__foot-card">
                <div class="bw-ticket__foot-label">Booking Code</div>
                <div class="bw-ticket__foot-value">{{ $bookingCode ?: '-' }}</div>
            </div>
            <div class="bw-ticket__foot-card">
                <div class="bw-ticket__foot-label">Reference No</div>
                <div class="bw-ticket__foot-value">{{ $referenceNo ?: '-' }}</div>
            </div>
            <div class="bw-ticket__foot-card">
                <div class="bw-ticket__foot-label">Issued Date</div>
                <div class="bw-ticket__foot-value">
                    {{ !empty($issuedDate) ? \Carbon\Carbon::parse($issuedDate)->format('d/m/Y H:i') : '-' }}
                </div>
            </div>
        </div>

    </div>{{-- end .bw-ticket__body --}}
</div>{{-- end .bw-ticket --}}