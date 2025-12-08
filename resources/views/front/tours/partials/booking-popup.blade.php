<div
    x-data="{
        isOpen: false,
        tier: null,
        count: 1,
        total: 0,
        flightInfo: flightInfo,

        open(e) {
            this.tier = e.detail.tier;
            this.count = this.tier.is_custom ? 2 : this.tier.min_people;
            this.recalc();
            this.isOpen = true;
        },
        close() { this.isOpen = false },
        recalc() {
            let min = this.tier.is_custom ? 2 : this.tier.min_people;
            let max = this.tier.max_people ?? 9999;
            if (this.count < min) this.count = min;
            if (this.count > max) this.count = max;
            this.total = this.count * this.tier.price;
        }
    }"
    x-on:open-booking.window="open($event)"
>
    <div
        x-show="isOpen"
        x-transition.opacity
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-40"
        style="display: none;"
    >
        <div
            x-show="isOpen"
            x-transition.scale
            class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 p-6 relative"
        >
            <button
                type="button"
                class="absolute top-3 right-10 text-gray-400 hover:text-gray-600"
                @click="close()"
            >
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Booking Form</h2>

            <form class="space-y-4 rounded-lg">
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Lengkap</label>
                        <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Tanggal Keberangkatan</label>
                        <input type="date" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Email</label>
                        <input type="email" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">WhatsApp</label>
                        <input type="text" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>

                <!-- PILIHAN TIKET PESAWAT -->
<div class="space-y-2 mt-4"
     x-show="flightInfo === 'not_included'">

    <label class="font-semibold text-gray-700">Pilihan Tiket Pesawat</label>
    <div class="flex justify-between"> 
<div class="flex items-center gap-2">
        <input type="radio" name="flight_option" value="no_ticket"
               x-model="flightOption"
               class="accent-blue-600" checked>
        <span>Tanpa Tiket</span>
    </div>

    <!-- DENGAN TIKET (TOMBOL KE WHATSAPP) -->
    <button
        type="button"
        class="mt-2 px-4 py-2 w-72 bg-green-600 text-white rounded-lg shadow hover:bg-green-700  text-center font-semibold"
        @click="window.open(
            'https://wa.me/{{ config('app.wa_admin', '6281234567890') }}?text=Halo,%20saya%20ingin%20cek%20harga%20paket%20{{ urlencode($package->title) }}%20dengan%20tiket',
            '_blank'
        )"
    >
        Dengan Tiket (Cek harga via WA)
    </button>
    </div>

    <!-- TANPA TIKET (RADIO) -->
    

</div>


                <div class="mt-2">
                    <label class="text-xs font-semibold text-gray-600">Jumlah Peserta</label>
                    <div class="mt-1 flex items-center gap-3 text-sm">
                        <input
                            type="number"
                            class="w-24 border rounded-lg px-3 py-2 text-center"
                            x-model.number="count"
                            @input="recalc()"
                        >
                        <p>
                            x <span x-text="tier ? tier.price.toLocaleString('id-ID') : 0"></span>
                            = <b>Rp <span x-text="total.toLocaleString('id-ID')"></span></b>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm">
                        <p class="text-gray-500">Total Bayar</p>
                        <p class="text-2xl font-bold text-emerald-600">
                            Rp <span x-text="total.toLocaleString('id-ID')"></span>
                        </p>
                    </div>

                    <button
                        type="button"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold text-sm"
                    >
                        Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
