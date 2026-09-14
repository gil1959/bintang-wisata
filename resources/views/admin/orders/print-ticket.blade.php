<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>E-Ticket Admin - {{ $order->invoice_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <style>
        /* ─── SCREEN STYLES ─────────────────────────────────── */
        html,
        body {
            background: #eef4f8;
        }

        .ticket-print-shell {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
            padding: 28px 20px 36px;
        }

        /* Boarding pass section is hidden on screen */
        .bp-boarding-pass {
            display: none;
        }

        /* ─── BOARDING PASS PRINT STYLES ─────────────────────── */
        @media print {
            @page {
                size: 215mm auto;
                margin: 6mm 8mm;
            }

            html,
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Helvetica Neue', Arial, sans-serif !important;
            }

            .no-print,
            .bw-ticket {
                display: none !important;
            }

            .ticket-print-shell {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .bp-boarding-pass {
                display: flex !important;
                flex-direction: row;
                width: 197mm;
                min-height: 70mm;
                height: auto;
                border: 1.5pt solid #0f172a;
                border-radius: 3mm;
                overflow: visible;
                font-family: 'Helvetica Neue', Arial, sans-serif;
                background: #ffffff;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .bp-main {
                flex: 1 1 auto;
                min-width: 0;
                display: flex;
                flex-direction: column;
                border-right: 1.5pt dashed #94a3b8;
            }

            .bp-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 4mm 5mm 3mm;
                background: #f0f8ff;
                border-bottom: 1pt solid #dbe5ef;
                gap: 8mm;
            }

            .bp-brand {
                font-size: 7pt;
                font-weight: 800;
                color: #0194F3;
                text-transform: uppercase;
                letter-spacing: .08em;
                line-height: 1.3;
            }

            .bp-route {
                font-size: 12pt;
                font-weight: 800;
                color: #0f172a;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .bp-airline {
                font-size: 7pt;
                color: #64748b;
                font-weight: 500;
                margin-top: 1mm;
                white-space: nowrap;
            }

            .bp-status-badge {
                display: inline-flex;
                align-items: center;
                padding: 1mm 3mm;
                border-radius: 999pt;
                border: 1pt solid #6ee7b7;
                background: #ecfdf5;
                color: #065f46;
                font-size: 6pt;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .06em;
                white-space: nowrap;
                flex-shrink: 0;
            }

            .bp-flight {
                display: flex;
                flex-direction: column;
                padding: 3mm 5mm;
                border-bottom: 1pt solid #f1f5f9;
            }

            .bp-flight-label {
                font-size: 6pt;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .08em;
                margin-bottom: 2mm;
            }

            .bp-flight-row {
                display: flex;
                align-items: center;
                gap: 0;
            }

            .bp-iata {
                font-size: 20pt;
                font-weight: 800;
                color: #0f172a;
                letter-spacing: -.02em;
                line-height: 1;
                min-width: 16mm;
            }

            .bp-iata-name {
                font-size: 6pt;
                color: #64748b;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-top: .5mm;
            }

            .bp-time {
                font-size: 9pt;
                font-weight: 700;
                color: #334155;
                line-height: 1;
            }

            .bp-date {
                font-size: 6pt;
                color: #94a3b8;
                font-weight: 500;
                margin-top: .5mm;
            }

            .bp-route-line {
                flex: 1 1 auto;
                display: flex;
                align-items: center;
                gap: 2mm;
                padding: 0 3mm;
                min-width: 0;
            }

            .bp-route-line::before,
            .bp-route-line::after {
                content: "";
                flex: 1 1 auto;
                height: 1pt;
                background: #cbd5e1;
            }

            .bp-flight-badge {
                display: flex;
                align-items: center;
                gap: 1.5mm;
                flex-shrink: 0;
            }

            .bp-flight-no {
                font-size: 8pt;
                font-weight: 700;
                color: #0f172a;
                white-space: nowrap;
            }

            .bp-arr {
                text-align: right;
            }

            .bp-passengers {
                padding: 2.5mm 5mm;
                border-bottom: 1pt solid #f1f5f9;
                flex: 1 1 auto;
            }

            .bp-pax-label {
                font-size: 6pt;
                font-weight: 700;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .08em;
                margin-bottom: 1mm;
            }

            .bp-pax-list {
                font-size: 7.5pt;
                font-weight: 600;
                color: #0f172a;
                line-height: 1.4;
            }

            .bp-footer {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                padding: 0;
                margin-top: auto;
            }

            .bp-footer-cell {
                padding: 2mm 5mm;
                border-right: 1pt solid #f1f5f9;
            }

            .bp-footer-cell:last-child {
                border-right: none;
            }

            .bp-footer-label {
                font-size: 5.5pt;
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .bp-footer-value {
                font-size: 7.5pt;
                font-weight: 800;
                color: #0f172a;
                margin-top: .5mm;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .bp-stub {
                width: 56mm;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                gap: 3mm;
                padding: 3mm 2.5mm;
                background: #f8fafc;
            }

            .bp-stub-top {
                width: 100%;
                text-align: center;
            }

            .bp-stub-brand {
                font-size: 6pt;
                font-weight: 800;
                color: #0194F3;
                text-transform: uppercase;
                letter-spacing: .08em;
            }

            .bp-stub-status {
                display: inline-flex;
                align-items: center;
                padding: 1mm 3mm;
                border-radius: 999pt;
                border: 1pt solid #6ee7b7;
                background: #ecfdf5;
                color: #065f46;
                font-size: 6pt;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin: 1.5mm auto;
            }

            .bp-stub-qr {
                width: 40mm;
                height: 40mm;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0;
                border: none;
                background: transparent;
                overflow: visible;
                flex-shrink: 0;
                margin: 2mm auto 1.5mm;
                padding: 0;
            }

            .bp-stub-qr canvas,
            .bp-stub-qr img {
                width: 40mm !important;
                height: 40mm !important;
                max-width: 40mm !important;
                max-height: 40mm !important;
                display: block !important;
            }

            .bp-stub-code-label {
                font-size: 5.5pt;
                color: #94a3b8;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .06em;
                text-align: center;
            }

            .bp-stub-code-value {
                font-size: 13pt;
                font-weight: 800;
                color: #0f172a;
                text-align: center;
                letter-spacing: .02em;
                margin-top: .5mm;
            }

            .bp-stub-ref {
                font-size: 6pt;
                color: #94a3b8;
                text-align: center;
                margin-top: .5mm;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                width: 100%;
            }
        }
    </style>
</head>

<body class="text-slate-900">
    <div class="ticket-print-shell">

        {{-- SCREEN: header + full card --}}
        <div class="no-print flex items-center justify-between gap-4 mb-5">
            <div>
                <div class="text-xl font-extrabold text-slate-900">Admin · Bintang Wisata · E-Ticket</div>
                <div class="text-sm text-slate-500 mt-1">{{ $order->invoice_number }}</div>
            </div>
            <button
                onclick="window.print()"
                class="inline-flex items-center justify-center rounded-2xl px-6 py-3 text-sm font-extrabold text-white"
                style="background:#0194F3;">
                Cetak Sekarang
            </button>
        </div>

        @include('user.orders.partials.flight-ticket-card', [
        'order' => $order,
        'supplierBook' => $supplierBook,
        'supplierDetail' => $supplierDetail,
        'supplierStatus' => $supplierStatus,
        'showPrintHeader' => false,
        ])

        {{-- PRINT: boarding pass --}}
        @php
        $bpBook = (array) ($supplierBook ?? []);
        $bpDetail = (array) ($supplierDetail ?? []);
        $bpStatus = (array) ($supplierStatus ?? []);
        $bpDeparts = (array) ($bpDetail['flightDeparts'] ?? []);
        $bpReturns = (array) ($bpDetail['flightReturns'] ?? []);
        $bpPassengers = (array) ($bpDetail['passengers'] ?? []);
        $bpBookCode = $bpBook['bookingCode'] ?? ($bpDetail['bookingCode'] ?? '');
        $bpRefNo = $bpDetail['referenceNo'] ?? ($bpBook['referenceNo'] ?? '');
        $bpIssued = $bpDetail['issuedDate'] ?? '';
        $bpStatus2 = strtoupper((string) ($bpStatus['ticket_status'] ?? ($bpDetail['ticketStatus'] ?? '')));
        $bpAirline = $bpDetail['airline'] ?? ($bpBook['airlineID'] ?? 'Airline');
        $bpOrigin = $bpDetail['origin'] ?? '-';
        $bpDest = $bpDetail['destination'] ?? '-';
        $bpTrip = strtoupper((string) ($bpDetail['tripType'] ?? '-'));
        $bpRoute = trim($bpOrigin . ' → ' . $bpDest);
        $bpQrVal = trim(implode(' | ', array_filter([
        $bpBookCode ? 'BOOK:' . $bpBookCode : null,
        $bpRefNo ? 'REF:' . $bpRefNo : null,
        ($bpOrigin !== '-' && $bpDest !== '-') ? ($bpOrigin . '-' . $bpDest) : null,
        ])));
        $bpKelasMap = ['F'=>'First','A'=>'First','C'=>'Business','D'=>'Business','I'=>'Business','Z'=>'Business','Y'=>'Economy','B'=>'Economy','M'=>'Economy','H'=>'Economy','K'=>'Economy','L'=>'Economy','Q'=>'Economy','T'=>'Economy','N'=>'Economy','R'=>'Economy','S'=>'Economy','V'=>'Economy','W'=>'Economy','X'=>'Economy','O'=>'Economy','P'=>'Economy','G'=>'Economy','E'=>'Economy'];
        $bpAirlineCode = $bpBook['airlineID'] ?? ($bpDetail['airlineID'] ?? ($bpDetail['airline'] ?? ''));
        
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

        $isZeroBased = in_array(strtoupper($bpBook['airlineID'] ?? $bpDetail['airline'] ?? ''), ['QG', 'QZ']);
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
        @endphp

        <div class="bp-boarding-pass">
            <div class="bp-main">
                <div class="bp-header">
                    <div style="min-width:0;flex:1;">
                        <div class="bp-brand">✈ Bintang Wisata · E-Ticket</div>
                        <div class="bp-route">{{ $bpRoute }}</div>
                        <div class="bp-airline">{{ $bpAirline }} · {{ $bpTrip }}</div>
                    </div>
                    @if($bpStatus2)<span class="bp-status-badge">{{ $bpStatus2 }}</span>@endif
                </div>

                @foreach($bpDeparts as $bfd)
                @php $bfKr=$bpKelasMap[strtoupper(trim((string)($bfd['fdFlightClass']??'')))]??'Economy'; $bfFlight=trim((string)($bfd['flightNumber']??'-')); @endphp
                <div class="bp-flight">
                    <div class="bp-flight-label">Penerbangan Pergi</div>
                    <div class="bp-flight-row">
                        <div>
                            <div class="bp-time">{{ \Carbon\Carbon::parse($bfd['fdDepartTime'])->format('H:i') }}</div>
                            <div class="bp-iata">{{ $bfd['fdOrigin']??'-' }}</div>
                            @if(!empty($bfd['departTerminal']))<div style="font-size:5.5pt; color:#64748b; margin-top:0.5mm;">Terminal {{ $bfd['departTerminal'] }}</div>@endif
                            <div class="bp-date">{{ \Carbon\Carbon::parse($bfd['fdDepartTime'])->translatedFormat('d M Y') }}</div>
                        </div>
                        <div class="bp-route-line">
                            <div class="bp-flight-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="#0194F3" style="transform:rotate(90deg);flex-shrink:0;">
                                    <path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9L2 14v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16Z" />
                                </svg>
                                <span class="bp-flight-no">{{ $bpAirlineCode ? $bpAirlineCode . '-' : '' }}{{ ltrim(str_replace('+', '', $bfFlight), ' -') }}</span>
                            </div>
                        </div>
                        <div class="bp-arr">
                            <div class="bp-time">{{ \Carbon\Carbon::parse($bfd['fdArrivalTime'])->format('H:i') }}</div>
                            <div class="bp-iata">{{ $bfd['fdDestination']??'-' }}</div>
                            @if(!empty($bfd['arriveTerminal']))<div style="font-size:5.5pt; color:#64748b; margin-top:0.5mm;">Terminal {{ $bfd['arriveTerminal'] }}</div>@endif
                            <div class="bp-date">{{ \Carbon\Carbon::parse($bfd['fdArrivalTime'])->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                    <div style="text-align:center;font-size:6pt;color:#94a3b8;margin-top:1mm;">
                        {{ $bfKr }}
                        <span style="margin: 0 1mm;">•</span> Bagasi: {{ !empty($bfd['fdBaggage']) ? $bfd['fdBaggage'] : '20 KG' }}
                        <span style="margin: 0 1mm;">•</span> Kabin: 7 KG
                    </div>
                </div>
                @endforeach

                @foreach($bpReturns as $bfr)
                @php $bfKr=$bpKelasMap[strtoupper(trim((string)($bfr['fdFlightClass']??'')))]??'Economy'; $bfFlight=trim((string)($bfr['flightNumber']??'-')); @endphp
                <div class="bp-flight">
                    <div class="bp-flight-label">Penerbangan Pulang</div>
                    <div class="bp-flight-row">
                        <div>
                            <div class="bp-time">{{ \Carbon\Carbon::parse($bfr['fdDepartTime'])->format('H:i') }}</div>
                            <div class="bp-iata">{{ $bfr['fdOrigin']??'-' }}</div>
                            @if(!empty($bfr['departTerminal']))<div style="font-size:5.5pt; color:#64748b; margin-top:0.5mm;">Terminal {{ $bfr['departTerminal'] }}</div>@endif
                            <div class="bp-date">{{ \Carbon\Carbon::parse($bfr['fdDepartTime'])->translatedFormat('d M Y') }}</div>
                        </div>
                        <div class="bp-route-line">
                            <div class="bp-flight-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="#0194F3" style="transform:rotate(-90deg);flex-shrink:0;">
                                    <path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9L2 14v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16Z" />
                                </svg>
                                <span class="bp-flight-no">{{ $bpAirlineCode ? $bpAirlineCode . '-' : '' }}{{ ltrim(str_replace('+', '', $bfFlight), ' -') }}</span>
                            </div>
                        </div>
                        <div class="bp-arr">
                            <div class="bp-time">{{ \Carbon\Carbon::parse($bfr['fdArrivalTime'])->format('H:i') }}</div>
                            <div class="bp-iata">{{ $bfr['fdDestination']??'-' }}</div>
                            @if(!empty($bfr['arriveTerminal']))<div style="font-size:5.5pt; color:#64748b; margin-top:0.5mm;">Terminal {{ $bfr['arriveTerminal'] }}</div>@endif
                            <div class="bp-date">{{ \Carbon\Carbon::parse($bfr['fdArrivalTime'])->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                    <div style="text-align:center;font-size:6pt;color:#94a3b8;margin-top:1mm;">
                        {{ $bfKr }}
                        <span style="margin: 0 1mm;">•</span> Bagasi: {{ !empty($bfr['fdBaggage']) ? $bfr['fdBaggage'] : '20 KG' }}
                        <span style="margin: 0 1mm;">•</span> Kabin: 7 KG
                    </div>
                </div>
                @endforeach

                @if(!empty($bpPassengers))
                <div class="bp-passengers">
                    <div class="bp-pax-label">Penumpang</div>
                    <div style="display:flex; flex-direction:column; gap:4mm;">
                        @foreach($bpPassengers as $pax)
                        @php
                            $opax = $findOrderPax($pax) ?: [];
                            $paxType = ucfirst(strtolower($pax['type'] ?? '-'));
                            $paxName = trim(($pax['title'] ?? '') . ' ' . ($pax['firstName'] ?? '') . ' ' . ($pax['lastName'] ?? ''));
                            
                            $parentSeq = $opax['parent'] ?? $pax['parent'] ?? null;
                            $parentName = null;
                            if ((strtolower($pax['type'] ?? '') === 'infant' || strtolower($opax['type'] ?? '') === 'infant') && $parentSeq !== null && $parentSeq !== '') {
                                $parentName = $getAdultName($parentSeq);
                            }

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
                        <div>
                            <div class="bp-pax-list">{{ $paxName }} <span style="font-weight:normal; font-size:6.5pt; color:#64748b; margin-left:2px;">({{ $paxType }})</span></div>
                            @if($parentName)
                            <div style="font-size:6pt; color:#0194F3; font-weight:500;">Dipangku oleh: {{ $parentName }}</div>
                            @endif
                            <div style="font-size:6pt; font-weight:600; color:#475569; margin-top:1.5mm;">
                                E-Ticket: <span style="color:#0f172a; font-weight:800;">{{ $pax['ticketNo'] ?? $opax['ticketNo'] ?? $bpRefNo ?? 'PENDING' }}</span>
                            </div>
                            
                            {{-- ADDONS --}}
                            @if(!empty($opax['addOns']) && is_array($opax['addOns']))
                            <div style="margin-top: 1.5mm; padding-top: 1.5mm; border-top: 0.5pt dashed #e2e8f0;">
                                <div style="display:flex; flex-wrap:wrap; gap: 3mm;">
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
                                    <div style="font-size:5.5pt; line-height:1.4;">
                                        <div style="font-weight:700; color:#0f172a;">{{ $origin }} → {{ $dest }}</div>
                                        <div style="color:#475569;">
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
                </div>
                @endif

                <div class="bp-footer">
                    <div class="bp-footer-cell">
                        <div class="bp-footer-label">Booking Code</div>
                        <div class="bp-footer-value">{{ $bpBookCode ?: '-' }}</div>
                    </div>
                    <div class="bp-footer-cell">
                        <div class="bp-footer-label">Reference No</div>
                        <div class="bp-footer-value">{{ $bpRefNo ?: '-' }}</div>
                    </div>
                    <div class="bp-footer-cell">
                        <div class="bp-footer-label">Issued Date</div>
                        <div class="bp-footer-value">{{ !empty($bpIssued) ? \Carbon\Carbon::parse($bpIssued)->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="bp-stub">
                <div class="bp-stub-top">
                    <div class="bp-stub-brand">Bintang Wisata</div>
                    @if($bpStatus2)<div><span class="bp-stub-status">{{ $bpStatus2 }}</span></div>@endif
                </div>
                @if($bpQrVal)<div class="bp-stub-qr" data-ticket-qr="{{ e($bpQrVal) }}"></div>@endif
                <div>
                    @if($bpBookCode)<div class="bp-stub-code-label">Booking Code</div>
                    <div class="bp-stub-code-value">{{ $bpBookCode }}</div>@endif
                    @if($bpRefNo)<div class="bp-stub-ref">Ref: {{ $bpRefNo }}</div>@endif
                </div>
            </div>
        </div>

        {{-- ─── FOOTER NOTES PRINT ONLY ──────────────────────────────── --}}
        <div class="bp-boarding-pass" style="margin-top:20px; padding:16px 0; border:none; border-top:1.5pt solid #dbe5ef; border-radius:0; background:transparent; display:block !important; width:197mm; min-height:auto;">
            <div style="display:flex; justify-content: space-between; text-align:center;">
                
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; padding: 0 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 10h2"/><path d="M16 14h2"/><path d="M6.17 15a3 3 0 0 1 5.66 0"/><circle cx="9" cy="11" r="2"/><rect x="2" y="5" width="20" height="14" rx="2"/>
                    </svg>
                    <div style="font-size:8pt; color:#4f6e9b; font-weight:600; line-height:1.4;">
                        Tunjukan E-Tiket dan<br>Identitas Para Penumpang<br>Saat Check-in
                    </div>
                </div>

                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; padding: 0 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><path d="M5 3 2 6"/><path d="m22 6-3-3"/><path d="M6.38 18.7 4 21"/><path d="M17.64 18.67 20 21"/>
                    </svg>
                    <div style="font-size:8pt; color:#4f6e9b; font-weight:600; line-height:1.4;">
                        Check-in Paling Lambat<br>90 Menit Sebelum<br>Keberangkatan
                    </div>
                </div>

                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; padding: 0 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4f6e9b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    </svg>
                    <div style="font-size:8pt; color:#4f6e9b; font-weight:600; line-height:1.4;">
                        Waktu Tertera Adalah<br>Waktu Bandara<br>Setempat
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        (function() {
            function renderTicketQrs() {
                document.querySelectorAll('[data-ticket-qr]').forEach(function(el) {
                    var value = el.getAttribute('data-ticket-qr');
                    if (!value || el.getAttribute('data-qr-rendered') === '1') return;
                    var size = 96;
                    el.innerHTML = '';
                    new QRCode(el, {
                        text: value,
                        width: size,
                        height: size,
                        correctLevel: QRCode.CorrectLevel.M
                    });
                    setTimeout(function() {
                        var canvas = el.querySelector('canvas');
                        var img = el.querySelector('img');
                        if (img) {
                            img.style.cssText = 'width:' + size + 'px!important;height:' + size + 'px!important;max-width:' + size + 'px!important;max-height:' + size + 'px!important;display:block!important;';
                            if (canvas) canvas.remove();
                        } else if (canvas) {
                            canvas.style.cssText = 'width:' + size + 'px!important;height:' + size + 'px!important;max-width:' + size + 'px!important;max-height:' + size + 'px!important;display:block!important;';
                        }
                        el.setAttribute('data-qr-rendered', '1');
                    }, 0);
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderTicketQrs);
            } else {
                renderTicketQrs();
            }
        })();
    </script>
</body>

</html>