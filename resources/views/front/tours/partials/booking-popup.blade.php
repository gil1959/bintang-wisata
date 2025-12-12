<div x-data="tourBooking()" 
     x-on:open-booking.window="open($event)" 
     x-cloak>

    <div
        x-show="isOpen"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-40"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl mx-4 p-6 relative">

            <button
                type="button"
                class="absolute top-3 right-10 text-gray-400 hover:text-gray-600"
                @click="close()"
            >
                ✕
            </button>

            <h2 class="text-xl font-bold mb-4">Booking Form</h2>

            <form class="space-y-4">

                <!-- INPUTS -->
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Nama Lengkap</label>
                        <input id="tourName" type="text" class="form-control mt-1">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">Tanggal Keberangkatan</label>
                        <input id="tourDate" type="date" class="form-control mt-1">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">Email</label>
                        <input id="tourEmail" type="email" class="form-control mt-1">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600">WhatsApp</label>
                        <input id="tourPhone" type="text" class="form-control mt-1">
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

                <!-- JUMLAH PESERTA -->
                <div>
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
                            = <b>Rp <span x-text="totalFormatted"></span></b>
                        </p>
                    </div>
                </div>

                <!-- PROMO -->
                <div>
                    <label class="form-label fw-semibold">Kode Promo</label>

                    <div class="input-group">
                        <input type="text" id="promoCode" class="form-control" placeholder="Contoh: LIBURAN50">

                        <button type="button" id="btnApplyPromo" class="btn text-white" style="background:#0194F3;">
                            Gunakan
                        </button>
                    </div>

                    <div id="promoMessage" class="small mt-2"></div>
                    <input type="hidden" id="promoId">
                </div>

                <!-- TOTAL -->
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm">
                        <p class="text-gray-500">Total Bayar</p>
                        <p class="text-2xl font-bold text-emerald-600">
                            Rp <span id="totalPrice" x-text="totalFormatted"></span>
                        </p>
                    </div>

                    <button
                        type="button"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold text-sm"
                        @click="submitBooking()"
                    >
                        Bayar Sekarang
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function tourBooking() {
    return {
        isOpen: false,
        tier: null,
        count: 1,
        total: 0,

        // NEW: state untuk radio tiket pesawat
        flightOption: 'no_ticket',
        // NEW: copy dari flight_info package (buat x-show)
        flightInfo: "{{ $package->flight_info }}",

        open(e) {
            // e adalah CustomEvent
            this.tier = e.detail.tier;
            this.count = this.tier.is_custom ? 2 : this.tier.min_people;
            this.recalc();
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
        },

        recalc() {
            let min = this.tier.is_custom ? 2 : this.tier.min_people;
            let max = this.tier.max_people ?? 9999;

            if (this.count < min) this.count = min;
            if (this.count > max) this.count = max;

            this.total = this.count * this.tier.price;
        },

        get totalFormatted() {
            return this.total.toLocaleString('id-ID');
        },

        submitBooking() {
            const promoValue = document.getElementById('promoId').value;

            fetch(`/tours/{{ $package->slug }}/draft-booking`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // paksa Laravel balikin JSON
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type: "tour",
                    product_id: {{ $package->id }},

                    name: document.getElementById('tourName').value,
                    email: document.getElementById('tourEmail').value,
                    phone: document.getElementById('tourPhone').value,
                    departure_date: document.getElementById('tourDate').value,

                    participants: this.count,
                    promo_id: promoValue ? Number(promoValue) : null,
                    frontend_total: this.total
                })
            })
            .then(async (r) => {
                if (!r.ok) {
                    const text = await r.text();
                    console.error('Draft booking failed', r.status, text);
                    alert('Booking gagal. Cek lagi data yang diisi / hubungi admin.');
                    return null;
                }

                return r.json();
            })
            .then((res) => {
                if (res && res.redirect) {
                    window.location.href = res.redirect;
                }
            })
            .catch((err) => {
                console.error(err);
                alert('Terjadi kesalahan jaringan. Coba lagi nanti.');
            });
        },
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const promoBtn = document.getElementById("btnApplyPromo");
    const promoCode = document.getElementById("promoCode");
    const promoMsg = document.getElementById("promoMessage");
    const promoId = document.getElementById("promoId");
    const totalPrice = document.getElementById("totalPrice");

    promoBtn.addEventListener("click", function () {
        let code = promoCode.value.trim();
        let price = Number(totalPrice.innerText.replace(/\./g, ''));

        if (!code) {
            promoMsg.innerHTML = `<span class='text-danger'>Masukkan kode promo!</span>`;
            return;
        }

        fetch("/promo/validate", {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content
            },
            body:JSON.stringify({ code: code, price: price })
        })
        .then(r => r.json())
        .then(res => {
            if (!res.valid) {
                promoMsg.innerHTML = `<span class='text-danger'>${res.message}</span>`;
                promoId.value = "";
                return;
            }

            totalPrice.innerText = res.final_price.toLocaleString("id-ID");
            promoMsg.innerHTML = `<span class='text-success'>Diskon diterapkan!</span>`;
            promoId.value = res.promo_id;
        });
    });
});
</script>

