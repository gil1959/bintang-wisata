@php
$isEn = app()->getLocale() === 'en';

$key = (string)($key ?? '');
$search = is_array($search ?? null) ? $search : [];
$journey = is_array($journey ?? null) ? $journey : [];

$origin = (string) data_get($journey, 'jiOrigin', data_get($search, 'origin', ''));
$destination = (string) data_get($journey, 'jiDestination', data_get($search, 'destination', ''));

$departIso = data_get($journey, 'jiDepartTime', data_get($search, 'departDate', null));
$currency = (string) data_get($journey, 'currency', 'IDR');
$sumPrice = data_get($journey, 'sumPrice', null);

$paxAdult = (int) data_get($search, 'paxAdult', 1);
$paxChild = (int) data_get($search, 'paxChild', 0);
$paxInfant = (int) data_get($search, 'paxInfant', 0);
$totalPax = max(1, $paxAdult + $paxChild + $paxInfant);

$safeParse = function ($value) {
try {
if (!filled($value)) return null;
return \Carbon\Carbon::parse($value);
} catch (\Throwable $e) {
return null;
}
};

$fmtDate = function ($value) use ($safeParse) {
$c = $safeParse($value);
return $c ? $c->format('d M Y') : '-';
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
@endphp

{{-- IMPORTANT:
    Partial ini mengandalkan Alpine state dari parent:
    - open (boolean)
    - loading (boolean)
    - error (string)
    - form {name,email,phone}
    - submit()
    - closeModal()
--}}

<div
    x-show="open"
    class="fixed inset-0 flex items-start justify-center bg-black/50 px-3 pb-4 pt-28 sm:pt-32"
    style="z-index: 9999;"
    x-transition.opacity
    x-cloak
    @click.self="closeModal()">
    <div
        x-show="open"
        class="w-full max-w-md sm:max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden"
        style="max-height: calc(100vh - 160px);"
        x-transition.scale>
        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-slate-200 px-4 py-3">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900">
                    {{ $isEn ? 'Booking Form' : 'Form Booking' }}
                </h2>
                <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">
                    {{ $isEn ? 'Fill in details to continue checkout.' : 'Isi data untuk lanjut ke checkout.' }}
                </p>
            </div>

            <button
                type="button"
                class="h-8 w-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700"
                @click="closeModal()"
                aria-label="{{ $isEn ? 'Close' : 'Tutup' }}">
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div class="px-4 py-4 space-y-3 overflow-y-auto" style="max-height: calc(100vh - 160px - 56px);">

            {{-- Error --}}
            <template x-if="error">
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
                    <div class="font-extrabold">{{ $isEn ? 'Error' : 'Error' }}</div>
                    <div class="mt-1 text-sm" x-text="error"></div>
                </div>
            </template>

            {{-- Summary mini --}}
            <div class="rounded-2xl border border-slate-200 p-3">
                <div class="text-sm font-extrabold text-slate-900">
                    {{ $origin }} → {{ $destination }}
                </div>
                <div class="mt-1 text-xs text-slate-600">
                    {{ $fmtDate($departIso) }}
                    • {{ $totalPax }} pax
                    • <span class="font-extrabold text-rose-600">{{ $fmtMoney($sumPrice, $currency) }}</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-500">
                    {{ $isEn ? 'Prices may change during booking.' : 'Harga bisa berubah saat booking.' }}
                </div>
            </div>

            {{-- Inputs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="text-[11px] font-extrabold text-slate-600">
                        {{ $isEn ? 'Full Name' : 'Nama Lengkap' }}
                    </label>
                    <input
                        type="text"
                        x-model="form.name"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                        placeholder="{{ $isEn ? 'Full name' : 'Nama lengkap' }}"
                        autocomplete="name">
                </div>

                <div class="sm:col-span-2">
                    <label class="text-[11px] font-extrabold text-slate-600">
                        Email
                    </label>
                    <input
                        type="email"
                        x-model="form.email"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                        placeholder="nama@email.com"
                        autocomplete="email">
                </div>

                <div class="sm:col-span-2">
                    <label class="text-[11px] font-extrabold text-slate-600">
                        WhatsApp
                    </label>
                    <input
                        type="text"
                        x-model="form.phone"
                        class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                        placeholder="08xxxxxxxxxx"
                        autocomplete="tel">
                </div>
            </div>

            {{-- Action --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                <button
                    type="button"
                    class="w-full sm:w-auto rounded-xl px-5 py-2.5 text-sm font-extrabold text-white shadow-sm disabled:opacity-60 disabled:cursor-not-allowed"
                    style="background:#0194F3"
                    onmouseover="if(!this.disabled) this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'"
                    :disabled="loading"
                    @click="submit()">
                    <span x-show="!loading">{{ $isEn ? 'Continue to Checkout' : 'Lanjut ke Checkout' }}</span>
                    <span x-show="loading">{{ $isEn ? 'Processing...' : 'Memproses...' }}</span>
                </button>

                <button
                    type="button"
                    class="w-full sm:w-auto btn btn-ghost border border-slate-200"
                    @click="closeModal()"
                    :disabled="loading">
                    {{ $isEn ? 'Cancel' : 'Batal' }}
                </button>
            </div>

            <div class="text-[11px] text-slate-500 pt-1">
                {{ $isEn
                    ? 'By continuing, you confirm your contact details are correct.'
                    : 'Dengan melanjutkan, kamu memastikan data kontak sudah benar.' }}
            </div>

        </div>
    </div>
</div>