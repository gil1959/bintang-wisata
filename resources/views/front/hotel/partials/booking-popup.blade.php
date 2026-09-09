@php
$isEn = app()->getLocale() === 'en';
$tomorrowDate = now()->addDay()->format('Y-m-d');
$dayAfterTomorrowDate = now()->addDays(2)->format('Y-m-d');

$i18n = [
    'title' => $isEn ? 'Hotel Reservation' : 'Reservasi Hotel / Vila',
    'name' => $isEn ? 'Full Name' : 'Nama Lengkap',
    'email' => 'Email',
    'phone' => $isEn ? 'WhatsApp Phone' : 'Nomor WhatsApp',
    'room_details' => $isEn ? 'Selected Room' : 'Pilihan Kamar',
    'checkin' => $isEn ? 'Check-in Date' : 'Tanggal Check-in',
    'checkout' => $isEn ? 'Check-out Date' : 'Tanggal Check-out',
    'room_count' => $isEn ? 'Rooms Count' : 'Jumlah Kamar',
    'total_nights' => $isEn ? 'Total Nights' : 'Total Malam',
    'total_price' => $isEn ? 'Total Price' : 'Total Pembayaran',
    'book_now' => $isEn ? 'Book Now' : 'Lanjutkan Pemesanan',
    'processing' => $isEn ? 'Processing...' : 'Memproses...',
    'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
    'apply' => $isEn ? 'Apply' : 'Gunakan',
    'applied' => $isEn ? 'Applied' : 'Dipakai',
    'date_notice' => $isEn ? 'Reservation must be made at least 1 day in advance (tomorrow onwards).' : 'Reservasi minimal H-1 (minimal besok).',
];
@endphp

<div
  x-data="hotelBookingPopup({{ (int)$package->price_per_night }}, '{{ $package->slug }}', '{{ $tomorrowDate }}', '{{ $dayAfterTomorrowDate }}')"
  x-on:open-hotel-booking.window="open($event.detail)"
  x-cloak>

  {{-- BACKDROP --}}
  <div
    x-show="isOpen"
    x-transition.opacity
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[9999] p-4 overflow-y-auto"
    @click.self="close()"
    style="display:none">

    <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden border border-slate-200 shadow-2xl relative my-auto">
      
      {{-- MODAL HEADER --}}
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-sky-500 text-white flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 leading-tight">{{ $i18n['title'] }}</h3>
            <p class="text-xs text-slate-500 truncate max-w-[280px]">{{ $package->title }}</p>
          </div>
        </div>

        <button
          type="button"
          class="w-8 h-8 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition"
          @click="close()"
          aria-label="Close">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      {{-- MODAL BODY --}}
      <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">

        {{-- SELECTED ROOM INFO CARD --}}
        <div class="rounded-2xl bg-sky-50/70 border border-sky-100 p-4">
          <div class="flex items-start justify-between gap-2">
            <div>
              <span class="text-[11px] font-bold text-sky-600 uppercase tracking-wider block">Kamar Pilihan</span>
              <h4 class="text-sm font-extrabold text-slate-900 mt-0.5" x-text="room_name || 'Standar / Pilihan Properti'"></h4>
              <div class="flex items-center gap-2 mt-1.5">
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-md"
                      :class="with_breakfast ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200/70 text-slate-700'">
                  <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                  <span x-text="with_breakfast ? 'Termasuk Sarapan' : 'Tanpa Sarapan'"></span>
                </span>
                <span class="text-xs font-extrabold text-sky-600">
                  Rp <span x-text="format(room_rate)"></span> <span class="font-normal text-slate-400 text-[10px]">/malam</span>
                </span>
              </div>
            </div>
            <a href="#room-selection" @click="close()" class="text-xs font-bold text-sky-600 hover:underline shrink-0">
              Ganti Kamar
            </a>
          </div>
        </div>

        {{-- DATE SELECTION (MINIMUM H-1 / TOMORROW) --}}
        <div class="space-y-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                {{ $i18n['checkin'] }} <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                x-model="checkin_date"
                :min="minCheckin"
                @change="onCheckinChanged()"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                required>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                {{ $i18n['checkout'] }} <span class="text-red-500">*</span>
              </label>
              <input
                type="date"
                x-model="checkout_date"
                :min="minCheckout"
                @change="calc()"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                required>
            </div>
          </div>
          <p class="text-[11px] text-sky-600 font-medium flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $i18n['date_notice'] }}
          </p>
        </div>

        {{-- ROOM COUNT & GUEST COUNTER --}}
        <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 flex items-center justify-between">
          <div>
            <span class="text-xs font-bold text-slate-800 block">Jumlah Kamar</span>
            <span class="text-[11px] text-slate-500">Maksimal sesuai sisa ketersediaan</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="if(room_count > 1) { room_count--; calc(); }"
              class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition disabled:opacity-40"
              :disabled="room_count <= 1">
              -
            </button>
            <span class="w-8 text-center text-sm font-extrabold text-slate-900" x-text="room_count"></span>
            <button
              type="button"
              @click="room_count++; calc();"
              class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 flex items-center justify-center transition">
              +
            </button>
          </div>
        </div>

        {{-- CUSTOMER DATA --}}
        <div class="space-y-3 pt-1">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              {{ $i18n['name'] }} <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              x-model="name"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
              placeholder="Contoh: Budi Santoso"
              required>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                {{ $i18n['email'] }} <span class="text-red-500">*</span>
              </label>
              <input
                type="email"
                x-model="email"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                placeholder="nama@email.com"
                required>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">
                {{ $i18n['phone'] }} <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                x-model="phone"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                placeholder="081234567890"
                required>
            </div>
          </div>
        </div>

        {{-- PROMO CODE --}}
        <div class="pt-1">
          <label class="block text-xs font-bold text-slate-700 mb-1">{{ $i18n['promo_code'] }}</label>
          <div class="flex gap-2">
            <input
              type="text"
              x-model.trim="promoCode"
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs uppercase focus:ring-2 focus:ring-sky-400"
              placeholder="Masukkan kode promo"
              :disabled="promoLocked">
            <button
              type="button"
              @click="applyPromo()"
              class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold text-white transition disabled:opacity-50"
              style="background: #0088f8;"
              :disabled="promoLoading || promoLocked">
              <span x-show="!promoLocked && !promoLoading">{{ $i18n['apply'] }}</span>
              <span x-show="promoLocked && !promoLoading">{{ $i18n['applied'] }}</span>
              <span x-show="promoLoading">{{ $i18n['processing'] }}</span>
            </button>
          </div>
          <div class="mt-1.5 text-xs" x-html="promoMsg"></div>
        </div>

        {{-- CALCULATION SUMMARY --}}
        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 space-y-2 text-xs">
          <div class="flex justify-between items-center text-slate-600">
            <span>Durasi Menginap:</span>
            <span class="font-bold text-slate-800"><span x-text="nights"></span> Malam</span>
          </div>

          <div class="flex justify-between items-center text-slate-600">
            <span>Jumlah Kamar:</span>
            <span class="font-bold text-slate-800"><span x-text="room_count"></span> Kamar</span>
          </div>

          <div class="flex justify-between items-center text-slate-600" x-show="discount > 0">
            <span class="text-emerald-600 font-bold">Diskon Promo:</span>
            <span class="font-extrabold text-emerald-600">- Rp <span x-text="format(discount)"></span></span>
          </div>

          <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-sm">
            <span class="font-extrabold text-slate-900">{{ $i18n['total_price'] }}</span>
            <span class="font-black text-base text-sky-600">Rp <span x-text="format(total)"></span></span>
          </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        <button
          type="button"
          class="w-full rounded-2xl py-3.5 text-white font-extrabold text-sm transition shadow-md disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          style="background: #0088f8;"
          :disabled="loading"
          @click="submitBooking()">
          <span x-show="!loading">{{ $i18n['book_now'] }}</span>
          <span x-show="loading">{{ $i18n['processing'] }}</span>
        </button>

        {{-- WHATSAPP CS ASSISTANCE LINK --}}
        @php
            $csNumber = preg_replace('/[^0-9]/', '', $package->cs_contact ?: '628123456789');
            $csUrl = "https://wa.me/{$csNumber}?text=" . urlencode("Halo Bintang Wisata, saya ingin tanya ketersediaan dan reservasi untuk paket: {$package->title}");
        @endphp
        <div class="text-center pt-1">
          <a href="{{ $csUrl }}" target="_blank" rel="noopener noreferrer"
             class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
            <svg class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            Bantuan Reservasi via WhatsApp CS
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  function hotelBookingPopup(defaultPrice, slug, tomorrow, dayAfter) {
    return {
      isOpen: false,
      loading: false,

      name: '{{ auth()->user()->name ?? "" }}',
      email: '{{ auth()->user()->email ?? "" }}',
      phone: '{{ auth()->user()->phone ?? "" }}',

      room_id: null,
      room_name: '',
      with_breakfast: false,
      room_rate: defaultPrice,
      room_count: 1,

      minCheckin: tomorrow,
      minCheckout: dayAfter,
      checkin_date: tomorrow,
      checkout_date: dayAfter,

      nights: 1,
      subtotal: defaultPrice,
      discount: 0,
      total: defaultPrice,

      promoCode: '',
      promoId: null,
      promoMsg: '',
      promoLocked: false,
      promoLoading: false,

      token: @json(csrf_token()),

      open(detail) {
        if (detail) {
          this.room_id = detail.room_id || null;
          this.room_name = detail.room_name || '';
          this.with_breakfast = !!detail.with_breakfast;
          this.room_rate = Number(detail.price || defaultPrice);
          if (detail.checkin_date) this.checkin_date = detail.checkin_date;
          if (detail.checkout_date) this.checkout_date = detail.checkout_date;
        }

        this.isOpen = true;
        this.promoMsg = '';
        this.promoCode = '';
        this.promoId = null;
        this.promoLocked = false;
        this.promoLoading = false;
        this.discount = 0;

        this.calc();
      },

      close() {
        this.isOpen = false;
        this.loading = false;
      },

      onCheckinChanged() {
        if (!this.checkin_date) return;
        const d = new Date(this.checkin_date);
        d.setDate(d.getDate() + 1);
        this.minCheckout = d.toISOString().split('T')[0];

        if (!this.checkout_date || new Date(this.checkout_date) <= new Date(this.checkin_date)) {
          this.checkout_date = this.minCheckout;
        }

        this.calc();
      },

      calc() {
        if (!this.checkin_date || !this.checkout_date) {
          this.nights = 1;
        } else {
          const start = new Date(this.checkin_date);
          const end = new Date(this.checkout_date);
          if (end > start) {
            this.nights = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
          } else {
            this.nights = 1;
          }
        }

        const count = Math.max(1, parseInt(this.room_count) || 1);
        const rate = parseFloat(this.room_rate) || 0;
        this.subtotal = this.nights * rate * count;
        this.total = Math.max(0, this.subtotal - this.discount);
      },

      format(n) {
        try {
          return Number(n || 0).toLocaleString('id-ID');
        } catch (e) {
          return n;
        }
      },

      applyPromo() {
        this.promoMsg = '';
        if (this.promoLocked) return;
        if (this.promoLoading) return;

        const code = (this.promoCode || '').trim();
        if (!code) {
          this.promoMsg = '<span class="text-red-500 font-medium">Kode promo belum diisi.</span>';
          return;
        }

        this.promoLoading = true;

        fetch('/promo/validate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            _token: this.token,
            code: code,
            price: this.subtotal,
            email: this.email
          })
        })
        .then(r => r.json())
        .then(res => {
          this.promoLoading = false;
          if (!res.valid) {
            this.promoMsg = `<span class="text-red-500 font-medium">${res.message || 'Kode promo tidak valid.'}</span>`;
            this.promoId = null;
            return;
          }
          this.discount = Math.max(0, this.subtotal - res.final_price);
          this.total = res.final_price;
          this.promoId = res.promo_id;
          this.promoLocked = true;
          this.promoMsg = '<span class="text-emerald-600 font-bold">Kode promo berhasil digunakan!</span>';
        })
        .catch(() => {
          this.promoLoading = false;
          this.promoMsg = '<span class="text-red-500 font-medium">Gagal memvalidasi promo.</span>';
        });
      },

      submitBooking() {
        if (!this.name || !this.email || !this.phone) {
          alert('Nama, email, dan nomor WhatsApp wajib diisi.');
          return;
        }
        if (!this.checkin_date || !this.checkout_date) {
          alert('Tanggal check-in dan check-out wajib diisi.');
          return;
        }

        // Verify check-in is at least tomorrow
        const tomorrowMidnight = new Date(tomorrow);
        tomorrowMidnight.setHours(0,0,0,0);
        const selectedCheckin = new Date(this.checkin_date);
        selectedCheckin.setHours(0,0,0,0);
        if (selectedCheckin < tomorrowMidnight) {
          alert('Reservasi hanya bisa minimal untuk besok (H-1).');
          return;
        }

        this.loading = true;

        fetch(`/hotel/${slug}/draft-booking`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            _token: this.token,
            name: this.name,
            email: this.email,
            phone: this.phone,
            checkin_date: this.checkin_date,
            checkout_date: this.checkout_date,
            room_id: this.room_id ? Number(this.room_id) : null,
            with_breakfast: this.with_breakfast ? 1 : 0,
            room_count: this.room_count ? Number(this.room_count) : 1,
            promo_id: this.promoId ? Number(this.promoId) : null,
          })
        })
        .then(async (r) => {
          const text = await r.text();
          let json = null;
          try {
            json = JSON.parse(text);
          } catch(e) {}

          if (!r.ok) {
            this.loading = false;
            if (json && json.errors) {
              const msg = Object.values(json.errors).flat().join('\n');
              alert(msg);
            } else if (json && json.message) {
              alert(json.message);
            } else {
              alert(`Booking gagal (${r.status}). Silakan hubungi CS.`);
            }
            return null;
          }
          return json;
        })
        .then(res => {
          if (res && res.redirect) {
            window.location.href = res.redirect;
          } else if (res) {
            this.loading = false;
            alert('Gagal memproses pesanan.');
          }
        })
        .catch(err => {
          console.error(err);
          this.loading = false;
          alert('Terjadi kendala koneksi.');
        });
      }
    }
  }
</script>