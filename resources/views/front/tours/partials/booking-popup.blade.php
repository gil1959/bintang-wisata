<div x-data="tourBooking(@js($package->flight_info), @js(preg_replace('/\D+/', '', $siteSettings['footer_whatsapp'] ?? '6281234567890'))
, @js($package->title), @js($package->slug), {{ $package->id }})"
     x-on:open-booking.window="open($event)"
     x-cloak>

  <!-- Overlay -->
  <div x-show="isOpen"
       class="fixed inset-0 flex items-start justify-center bg-black/50 px-3 pb-4 pt-28 sm:pt-32"
       style="z-index: 9999;"
       x-transition.opacity>

    <!-- Card -->
    <div x-show="isOpen"
         class="w-full max-w-md sm:max-w-lg rounded-2xl bg-white shadow-xl overflow-hidden"
         style="max-height: calc(100vh - 160px);"
         x-transition.scale>

      <!-- Header -->
      <div class="flex items-start justify-between border-b border-slate-200 px-4 py-3">
        <div>
          <h2 class="text-sm sm:text-base font-extrabold text-slate-900">Booking Form</h2>
          <p class="mt-0.5 text-[11px] sm:text-xs text-slate-500">Lengkapi data untuk lanjut pembayaran.</p>
        </div>

        <button type="button"
                class="h-8 w-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700"
                @click="close()"
                aria-label="Tutup">
          ✕
        </button>
      </div>

      <!-- Body (scroll internal) -->
      <div class="px-4 py-3 space-y-3 overflow-y-auto"
           style="max-height: calc(100vh - 160px - 56px);">

        <!-- Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-[11px] font-extrabold text-slate-600">Nama Lengkap</label>
            <input type="text" x-model="form.name"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   placeholder="Nama lengkap">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">Tanggal Keberangkatan</label>
            <input type="date" x-model="form.departure_date"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">Email</label>
            <input type="email" x-model="form.email"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   placeholder="nama@email.com">
          </div>

          <div>
            <label class="text-[11px] font-extrabold text-slate-600">WhatsApp</label>
            <input type="text" x-model="form.phone"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   placeholder="08xxxxxxxxxx">
          </div>
        </div>

        <!-- Flight option -->
        <div x-show="flightInfo === 'not_included'"
             class="rounded-2xl border border-slate-200 p-3">
          <div class="font-extrabold text-slate-800 text-sm">Pilihan Tiket Pesawat</div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input type="radio" name="flight_option" value="no_ticket" x-model="flightOption" class="h-4 w-4">
              <span>Tanpa Tiket</span>
            </label>

            <button type="button"
                    class="w-full sm:w-auto rounded-xl px-3 py-2 text-xs sm:text-sm font-extrabold text-white shadow-sm"
                    style="background:#0194F3"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'"
                    @click="openTicketWA()">
              Dengan Tiket (Cek harga via WA)
            </button>
          </div>
        </div>

        <!-- Participants -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">Jumlah Peserta</div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
            <input type="number"
                   class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   x-model.number="count"
                   @input="recalc()">

            <div class="text-slate-700">
              x <span x-text="tier ? tier.price.toLocaleString('id-ID') : '0'"></span>
              = <span class="font-extrabold">Rp <span x-text="totalFormatted"></span></span>
            </div>
          </div>
        </div>

        <!-- Promo -->
        <div class="rounded-2xl border border-slate-200 p-3">
          <div class="text-[11px] font-extrabold text-slate-600">Kode Promo</div>

          <div class="mt-2 flex flex-col sm:flex-row gap-2">
            <input type="text" x-model="promo.code"
                   class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   placeholder="Contoh: LIBURAN50">

            <button type="button"
                    class="rounded-xl px-4 py-2 text-sm font-extrabold text-white shadow-sm"
                    style="background:#0194F3"
                    onmouseover="this.style.background='#0186DB'"
                    onmouseout="this.style.background='#0194F3'"
                    @click="applyPromo()">
              Gunakan
            </button>
          </div>

          <div class="mt-2 text-sm"
               :class="promo.ok ? 'text-emerald-700' : 'text-rose-600'"
               x-text="promo.message"></div>
        </div>

        <!-- Total + Action -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <div class="text-xs text-slate-500">Total Bayar</div>
            <div class="text-lg sm:text-xl font-extrabold" style="color:#0194F3">
              Rp <span x-text="totalFormatted"></span>
            </div>
          </div>

          <button type="button"
                  class="w-full sm:w-auto rounded-xl px-5 py-2.5 text-sm font-extrabold text-white shadow-sm"
                  style="background:#0194F3"
                  onmouseover="this.style.background='#0186DB'"
                  onmouseout="this.style.background='#0194F3'"
                  @click="submitBooking()">
            Bayar Sekarang
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function tourBooking(flightInfo, waAdmin, packageTitle, packageSlug, productId) {
  return {
    isOpen: false,
    tier: null,
    count: 1,
    total: 0,

    flightInfo,
    flightOption: 'no_ticket',

    form: { name:'', email:'', phone:'', departure_date:'' },

    promo: { code:'', id:null, ok:false, message:'' },

    open(e) {
      this.tier = e.detail.tier;
      this.count = this.tier.is_custom ? 2 : this.tier.min_people;
      this.total = 0;
      this.promo = { code:'', id:null, ok:false, message:'' };
      this.recalc();
      this.isOpen = true;
    },

    close() { this.isOpen = false; },

    recalc() {
      if (!this.tier) return;
      const min = this.tier.is_custom ? 2 : this.tier.min_people;
      const max = this.tier.max_people ?? 9999;
      if (this.count < min) this.count = min;
      if (this.count > max) this.count = max;
      this.total = this.count * this.tier.price;
    },

    get totalFormatted() { return (this.total || 0).toLocaleString('id-ID'); },

    openTicketWA() {
      const text = `Halo, saya ingin cek harga paket ${packageTitle} dengan tiket`;
      window.open(`https://wa.me/${waAdmin}?text=${encodeURIComponent(text)}`, '_blank');
    },

    async applyPromo() {
      const code = (this.promo.code || '').trim();
      if (!code) {
        this.promo = { ...this.promo, ok:false, id:null, message:'Masukkan kode promo.' };
        return;
      }

      const r = await fetch("/promo/validate", {
        method:"POST",
        headers:{
          "Content-Type":"application/json",
          "Accept":"application/json",
          "X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ code, price: this.total })
      });

      const res = await r.json();
      if (!res.valid) {
        this.promo = { ...this.promo, ok:false, id:null, message: res.message || 'Promo tidak valid.' };
        return;
      }

      this.total = Number(res.final_price || this.total);
      this.promo = { ...this.promo, ok:true, id: res.promo_id, message:'Diskon diterapkan.' };
    },

    async submitBooking() {
      const payload = {
        type: "tour",
        product_id: productId,
        name: this.form.name,
        email: this.form.email,
        phone: this.form.phone,
        departure_date: this.form.departure_date,
        participants: this.count,
        promo_id: this.promo.id ? Number(this.promo.id) : null,
        frontend_total: this.total,
        flight_option: this.flightOption,
      };

      const r = await fetch(`/tours/${packageSlug}/draft-booking`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
      });

      if (!r.ok) {
        const text = await r.text();
        console.error('Draft booking failed', r.status, text);
        alert('Booking gagal. Cek lagi data yang diisi / hubungi admin.');
        return;
      }

      const res = await r.json();
      if (res?.redirect) window.location.href = res.redirect;
    },
  }
}
</script>
