@php
    $todayDate = now()->format('Y-m-d');
@endphp

<div x-data="{
    isOpen: false,
    package: null,
    pax: 1,
    departureDate: '{{ $todayDate }}',
    name: '{{ auth()->user()->name ?? '' }}',
    email: '{{ auth()->user()->email ?? '' }}',
    phone: '{{ auth()->user()->phone ?? '' }}',
    loading: false,
    errorMessage: '',
    open(pkg) {
        this.package = pkg;
        this.isOpen = true;
        this.errorMessage = '';
    },
    close() {
        this.isOpen = false;
    },
    get subtotal() {
        return this.package ? (this.package.price * this.pax) : 0;
    },
    formatRupiah(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    },
    submitBooking() {
        this.loading = true;
        this.errorMessage = '';

        fetch('/travel-bus/' + this.package.slug + '/order/draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
            },
            body: JSON.stringify({
                name: this.name,
                email: this.email,
                phone: this.phone,
                departure_date: this.departureDate,
                pax: this.pax
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(res => {
            this.loading = false;
            if (res.status === 200 && res.body.redirect) {
                window.location.href = res.body.redirect;
            } else {
                this.errorMessage = res.body.message || (res.body.errors ? Object.values(res.body.errors)[0][0] : 'Terjadi kesalahan.');
            }
        })
        .catch(err => {
            this.loading = false;
            this.errorMessage = 'Terjadi kesalahan koneksi.';
        });
    }
}"
x-on:open-bus-booking.window="open($event.detail)"
x-cloak>

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition.opacity
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[9999] p-4 overflow-y-auto"
         @click.self="close()"
         style="display:none">

        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden border border-slate-200 shadow-2xl relative my-auto">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1 .4-1 1v7c0 .6.4 1 1 1h2"/>
                            <circle cx="7" cy="17" r="2"/>
                            <path d="M9 17h6"/>
                            <circle cx="17" cy="17" r="2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 leading-tight">Pesan Tiket Travel</h3>
                        <p class="text-xs text-slate-500" x-text="package ? (package.armada_name + ' (' + package.travel_type + ')') : ''"></p>
                    </div>
                </div>

                <button type="button" @click="close()" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <form @submit.prevent="submitBooking()" class="p-6 space-y-4">
                <template x-if="errorMessage">
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold rounded-xl" x-text="errorMessage"></div>
                </template>

                {{-- Ringkasan Rute --}}
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between text-xs text-slate-500 font-bold mb-1">
                        <span>Rute Perjalanan</span>
                        <span x-text="package ? package.formatted_duration : ''"></span>
                    </div>
                    <div class="flex items-center gap-2 text-sm font-extrabold text-slate-900">
                        <span x-text="package ? package.departure_location : ''"></span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <span x-text="package ? package.arrival_location : ''"></span>
                    </div>
                    <div class="mt-2 text-xs font-semibold text-slate-600">
                        Jam: <span class="text-slate-900 font-bold" x-text="package ? (package.formatted_departure_time + ' - ' + package.formatted_arrival_time) : ''"></span>
                    </div>
                </div>

                {{-- Tanggal & Pax --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Tanggal Berangkat</label>
                        <input type="date" x-model="departureDate" min="{{ $todayDate }}" required
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Jumlah Kursi (Pax)</label>
                        <input type="number" x-model.number="pax" min="1" max="10" required
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-800">
                    </div>
                </div>

                {{-- Data Pemesan --}}
                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" x-model="name" required placeholder="Nama sesuai KTP"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email</label>
                        <input type="email" x-model="email" required placeholder="email@contoh.com"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Nomor WhatsApp</label>
                        <input type="text" x-model="phone" required placeholder="08xxxxxxxxxx"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                    </div>
                </div>

                {{-- Total Harga --}}
                <div class="border-t border-slate-100 pt-3 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500 font-bold">Total Pembayaran</div>
                        <div class="text-lg font-black text-orange-600" x-text="formatRupiah(subtotal)"></div>
                    </div>
                    <button type="submit" :disabled="loading"
                            class="px-6 py-3 rounded-2xl font-black text-white bg-orange-500 hover:bg-orange-600 transition flex items-center gap-2">
                        <template x-if="loading">
                            <span>Memproses...</span>
                        </template>
                        <template x-if="!loading">
                            <span>Lanjut Pembayaran</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
