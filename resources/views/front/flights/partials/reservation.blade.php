@php
$isEn = app()->getLocale() === 'en';

$key = (string)($key ?? '');
$search = is_array($search ?? null) ? $search : [];
$journey = is_array($journey ?? null) ? $journey : [];
$cities = is_array($cities ?? null) ? $cities : [];

$origin = strtoupper((string) data_get($journey, 'jiOrigin', data_get($search, 'origin', '')));
$destination = strtoupper((string) data_get($journey, 'jiDestination', data_get($search, 'destination', '')));

$departIso = data_get($journey, 'jiDepartTime', null);
$arriveIso = data_get($journey, 'jiArrivalTime', null);

$price = is_array($price ?? null) ? $price : []; // reservation price
$displayPrice = is_array($displayPrice ?? null) ? $displayPrice : $price;

$priceStatus = strtoupper((string) data_get($price, 'status', ''));
$isRepriced = $priceStatus === 'SUCCESS' && is_numeric(data_get($price, 'sumFare'));

$currency = (string) data_get($displayPrice, 'currency', data_get($journey, 'currency', 'IDR'));
$repricedFare = data_get($displayPrice, 'sumFare', null);
$estimatedFare = data_get($journey, 'sumPrice', null);
$pricingData = is_array($pricingData ?? null) ? $pricingData : [];
$resolvedPrice = data_get($pricingData, 'final_price', null);
$sumPrice = is_numeric($resolvedPrice) ? $resolvedPrice : (is_numeric($repricedFare) ? $repricedFare : $estimatedFare);
$isManualPrice = (bool) data_get($pricingData, 'is_manual', false);

$priceError = (string) ($priceError ?? '');

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

$displayOrigin = $formatCityLabel($origin);
$displayDestination = $formatCityLabel($destination);

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

$waAdmin = preg_replace('/\D+/', '', $siteSettings['footer_whatsapp'] ?? '6281234567890');
$waLink = $waAdmin ? ('https://wa.me/' . $waAdmin) : '#';

$draftUrl = $key !== '' ? route('flights.draft', $key) : '#';
@endphp

<div
    x-data="flightReservationForm({
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
})"
    x-on:keydown.window.escape="if(open) closeModal()"
    x-cloak>
    <div class="sticky top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100">

        <h2 class="font-bold text-lg mb-4 text-gray-900">
            {{ $isEn ? 'Flight Reservation' : 'Reservasi Tiket Pesawat' }}
        </h2>

        {{-- Summary --}}
        <div class="space-y-3">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-extrabold text-[#0194F3]">
                    {{ $displayOrigin }} → {{ $displayDestination }}
                </div>

                <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <div class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Depart' : 'Berangkat' }}</div>
                        <div class="mt-1 font-extrabold text-slate-900">{{ $fmtDate($departIso ?: data_get($search,'departDate')) }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $fmtTime($departIso) }}</div>
                    </div>

                    <div>
                        <div class="text-[11px] font-extrabold text-slate-600">{{ $isEn ? 'Arrive' : 'Tiba' }}</div>
                        <div class="mt-1 font-extrabold text-slate-900">{{ $fmtDate($arriveIso) }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $fmtTime($arriveIso) }}</div>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 rounded-full bg-white border border-slate-200 px-3 py-1 font-extrabold text-slate-700">
                        <i data-lucide="timer" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $fmtDuration($departIso, $arriveIso) }}
                    </span>

                    <span class="inline-flex items-center gap-1 rounded-full bg-white border border-slate-200 px-3 py-1 font-extrabold text-slate-700">
                        <i data-lucide="route" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $stops === 0 ? ($isEn ? 'Direct' : 'Langsung') : ($stops . ' ' . ($isEn ? 'Transit' : 'Transit')) }}
                    </span>

                    <span class="inline-flex items-center gap-1 rounded-full bg-white border border-slate-200 px-3 py-1 font-extrabold text-slate-700">
                        <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $totalPax }} {{ $isEn ? 'Passenger' : 'Penumpang' }}
                    </span>
                </div>
            </div>

            {{-- Price --}}
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="flex items-center gap-2">
                    <div class="text-[11px] font-extrabold text-slate-600">
                        {{ $isEn ? 'Total Price' : 'Total Harga' }}
                    </div>


                </div>

                <div class="mt-1 text-xl font-extrabold text-rose-600">
                    {{ $fmtMoney($sumPrice, $currency) }}
                </div>
            </div>

            {{-- Warnings --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600">
                {{ $isEn
        ? 'Please make sure passenger name, email, and WhatsApp number are correct before continuing.'
        : 'Pastikan nama penumpang, email, dan nomor WhatsApp sudah benar sebelum melanjutkan.' }}
            </div>
            {{-- Buttons --}}
            <a
                href="{{ route('flights.booking', $key) }}"
                class="w-full mt-2 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition {{ !$isRepriced ? 'pointer-events-none opacity-60' : '' }}">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>{{ $isEn ? 'Continue Booking' : 'Lanjut Booking' }}</span>
            </a>

            <a
                href="{{ $waLink }}"
                target="_blank"
                rel="noopener"
                class="w-full mt-2 btn btn-ghost border border-slate-200 flex items-center justify-center gap-2">
                <i data-lucide="messages-square" class="w-4 h-4"></i>
                <span>{{ $isEn ? 'Chat Admin' : 'Chat Admin' }}</span>
            </a>

        </div>
    </div>


</div>

<script>
    function flightReservationForm(config) {
        return {
            open: false,
            loading: false,
            error: '',
            addonData: null,
            seatData: null,
            priceStatus: config.priceStatus || '',
            isRepriced: Boolean(config.isRepriced),
            currentStep: 'form',
            config,

            form: {
                contact: {
                    first_name: '',
                    last_name: '',
                    title: 'MR',
                    country_code_phone: '62',
                    area_code_phone: '',
                    remaining_phone_no: '',
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

                for (let i = 0; i < adult; i++) {
                    rows.push(this.makePassenger('Adult', i + 1));
                }

                for (let i = 0; i < child; i++) {
                    rows.push(this.makePassenger('Child', i + 1));
                }

                for (let i = 0; i < infant; i++) {
                    rows.push(this.makePassenger('Infant', i + 1));
                }

                return rows;
            },

            makePassenger(type, number) {
                const defaultTitle = type === 'Adult' ? 'MR' : (type === 'Child' ? 'MSTR' : 'MSTR');

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
                    passportIssuedCountry: '',
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
                        baggageOptions: [],
                        mealOptions: [],
                        seatOptions: [],
                        selectedBaggage: '',
                        selectedMeal: '',
                        selectedSeat: '',
                        selectedCompartment: '',
                    }
                };
            },

            get csrf() {
                return this.config.csrfToken;
            },

            openModal() {
                this.error = '';
                this.open = true;
                document.body.classList.add('overflow-hidden');
            },

            closeModal() {
                this.open = false;
                document.body.classList.remove('overflow-hidden');
            },

            isInternationalRoute() {
                const origin = String(this.config.origin || '').toUpperCase().trim();
                const destination = String(this.config.destination || '').toUpperCase().trim();

                const domestic = ['CGK', 'SUB', 'DPS', 'UPG', 'BDO', 'JOG', 'YIA', 'SRG', 'SOC', 'LOP', 'BPN', 'PKU', 'PLM', 'PDG', 'BTJ', 'KNO', 'HLP'];

                return !(domestic.includes(origin) && domestic.includes(destination));
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

                if (!this.form.contact.area_code_phone.trim() || !this.form.contact.remaining_phone_no.trim()) {
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

                    if (!String(pax.nationality || '').trim() || !String(pax.birthCountry || '').trim()) {
                        this.error = `${label}: ${this.config.isEn ? 'nationality and birth country are required.' : 'nationality dan negara lahir wajib diisi.'}`;
                        return false;
                    }
                }

                return true;
            },

            async fetchAddonsAndSeat() {
                let res = await fetch(this.config.addonsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify(this.buildAddonRequestPayload())
                });

                let addonData = await res.json();
                if (!res.ok) {
                    throw new Error(addonData.message || addonData.error || addonData.respMessage || 'Gagal ambil baggage/meal.');
                }

                this.addonData = addonData;

                res = await fetch(this.config.seatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    }
                });

                let seatData = await res.json();
                if (!res.ok) {
                    throw new Error(seatData.message || seatData.error || seatData.respMessage || 'Gagal ambil seat map.');
                }

                this.seatData = seatData;

                this.hydrateAddonOptions();
                this.hydrateSeatOptions();
            },

            buildAddonRequestPayload() {
                return {
                    contact: this.form.contact,
                    insurance: this.form.insurance,
                    paxDetails: this.form.paxDetails.map((pax) => {
                        const copy = JSON.parse(JSON.stringify(pax));
                        delete copy.ui;
                        return copy;
                    })
                };
            },

            hydrateAddonOptions() {
                const addOns = Array.isArray(this.addonData?.addOns) ? this.addonData.addOns : [];

                for (let i = 0; i < this.form.paxDetails.length; i++) {
                    const pax = this.form.paxDetails[i];

                    pax.ui.baggageOptions = this.extractAddonOptions(addOns, [
                        'baggage',
                        'baggageCode',
                        'baggageText',
                        'baggageLabel',
                        'code',
                        'text',
                        'label',
                        'value'
                    ], ['baggage']);

                    pax.ui.mealOptions = this.extractAddonOptions(addOns, [
                        'meal',
                        'mealCode',
                        'mealText',
                        'mealLabel',
                        'code',
                        'text',
                        'label',
                        'value'
                    ], ['meal']);
                }
            },

            hydrateSeatOptions() {
                const seatAddOns = Array.isArray(this.seatData?.seatAddOns) ? this.seatData.seatAddOns : [];
                const flattenedSeats = this.flattenSeatOptions(seatAddOns);

                for (let i = 0; i < this.form.paxDetails.length; i++) {
                    this.form.paxDetails[i].ui.seatOptions = flattenedSeats;
                }
            },

            normalizeObjectKeys(obj) {
                if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
                    return obj;
                }

                const out = {};
                for (const [key, value] of Object.entries(obj)) {
                    out[String(key).toLowerCase()] = value;
                }
                return out;
            },

            extractAddonOptions(source, candidateFields = [], keywordHints = []) {
                const result = [];
                const seen = new Set();

                const pushOption = (rawValue, rawLabel = null) => {
                    const value = String(rawValue || '').trim();
                    const label = String(rawLabel || rawValue || '').trim();

                    if (!value || !label) return;

                    const key = `${value}||${label}`;
                    if (seen.has(key)) return;

                    seen.add(key);
                    result.push({
                        value,
                        label
                    });
                };

                const walk = (node) => {
                    if (Array.isArray(node)) {
                        node.forEach(walk);
                        return;
                    }

                    if (!node || typeof node !== 'object') {
                        return;
                    }

                    const normalized = this.normalizeObjectKeys(node);
                    const keys = Object.keys(normalized);

                    for (const field of candidateFields) {
                        const lowerField = String(field).toLowerCase();
                        if (typeof normalized[lowerField] === 'string' && normalized[lowerField].trim() !== '') {
                            let matchedByHint = keywordHints.length === 0;

                            if (!matchedByHint) {
                                matchedByHint = keys.some((k) =>
                                    keywordHints.some((hint) => String(k).includes(String(hint).toLowerCase()))
                                );
                            }

                            if (matchedByHint) {
                                pushOption(
                                    normalized[lowerField],
                                    normalized['text'] ?? normalized['label'] ?? normalized[lowerField]
                                );
                            }
                        }
                    }

                    Object.values(node).forEach(walk);
                };

                walk(source);
                return result;
            },

            flattenSeatOptions(source) {
                const result = [];
                const seen = new Set();

                const walk = (node) => {
                    if (Array.isArray(node)) {
                        node.forEach(walk);
                        return;
                    }

                    if (!node || typeof node !== 'object') {
                        return;
                    }

                    const normalized = this.normalizeObjectKeys(node);

                    const seatDesignator = String(
                        normalized.seatdesignator ??
                        normalized.seat ??
                        normalized.seattext ??
                        ''
                    ).trim();

                    const compartment = String(
                        normalized.compartment ??
                        normalized.cabin ??
                        ''
                    ).trim();

                    const seatType = String(
                        normalized.seattype ?? ''
                    ).trim().toUpperCase();

                    const rawIsOpen = normalized.isopen ?? normalized.open ?? normalized.available ?? null;

                    const isOpen = typeof rawIsOpen === 'boolean' ?
                        rawIsOpen :
                        String(rawIsOpen).toLowerCase() === 'true';

                    const seatPrice =
                        normalized.seatprice ??
                        normalized.price ??
                        normalized.amount ??
                        null;

                    if (seatDesignator !== '' && compartment !== '' && (seatType === '' || seatType === 'NS') && isOpen === true) {
                        const key = `${seatDesignator}||${compartment}`;
                        if (!seen.has(key)) {
                            seen.add(key);
                            result.push({
                                value: seatDesignator,
                                label: seatDesignator,
                                compartment: compartment,
                                seatPrice: seatPrice
                            });
                        }
                    }

                    Object.values(node).forEach(walk);
                };

                walk(source);
                return result;
            },

            applyPassengerAddOns() {
                this.form.paxDetails = this.form.paxDetails.map((pax) => {
                    const addOns = [];

                    if (pax.ui.selectedBaggage || pax.ui.selectedMeal || pax.ui.selectedSeat || pax.ui.selectedCompartment) {
                        addOns.push({
                            baggage: pax.ui.selectedBaggage || '',
                            meal: pax.ui.selectedMeal || '',
                            seat: pax.ui.selectedSeat || '',
                            compartment: pax.ui.selectedCompartment || '',
                        });
                    }

                    return {
                        ...pax,
                        addOns
                    };
                });
            },

            hasAnyAddonOrSeatOption() {
                return this.form.paxDetails.some((pax) => {
                    return (pax.ui.baggageOptions || []).length > 0 ||
                        (pax.ui.mealOptions || []).length > 0 ||
                        (pax.ui.seatOptions || []).length > 0;
                });
            },

            supplierAddonSummary() {
                const addOns = Array.isArray(this.addonData?.addOns) ? this.addonData.addOns : [];
                const firstAddon = addOns[0] && typeof addOns[0] === 'object' ? addOns[0] : {};

                return {
                    baggageAvailable: Array.isArray(firstAddon.baggageInfos) && firstAddon.baggageInfos.length > 0,
                    mealAvailable: Array.isArray(firstAddon.mealInfos) && firstAddon.mealInfos.length > 0,
                    seatAvailable: Array.isArray(this.seatData?.seatAddOns) && this.seatData.seatAddOns.length > 0,
                    noBaggageAllowed: Boolean(firstAddon.isEnableNoBaggage ?? true),
                };
            },

            async continueToAddonStep() {
                if (!this.isRepriced || this.priceStatus !== 'SUCCESS') {
                    this.error = this.config.isEn ?
                        'Latest airline price is not valid yet. Please reload flight detail or choose another schedule.' :
                        'Harga final airline belum valid. Muat ulang detail penerbangan atau pilih jadwal lain.';
                    return;
                }
                this.error = '';

                if (!this.validateContact() || !this.validatePassengers()) {
                    return;
                }

                this.loading = true;

                try {
                    await this.fetchAddonsAndSeat();

                    console.log('ADDON RESPONSE', this.addonData);
                    console.log('SEAT RESPONSE', this.seatData);
                    console.log('PARSED PAX UI', this.form.paxDetails);

                    this.currentStep = 'addons';

                    if (!this.hasAnyAddonOrSeatOption()) {
                        this.error = '';
                    }
                } catch (e) {
                    this.error = e.message || 'Terjadi error saat mengambil add-on dan seat.';
                } finally {
                    this.loading = false;
                }
            },

            onSeatChange(pax) {
                const selected = (pax.ui.seatOptions || []).find((item) => item.value === pax.ui.selectedSeat);
                pax.ui.selectedCompartment = selected ? String(selected.compartment || '') : '';
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

            async submitBooking() {
                if (!this.isRepriced || this.priceStatus !== 'SUCCESS') {
                    this.error = this.config.isEn ?
                        'Booking is blocked because Airline/Price has not succeeded.' :
                        'Booking diblokir karena Airline/Price belum berhasil.';
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
                            'X-CSRF-TOKEN': this.csrf,
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
            }
        }
    }
</script>