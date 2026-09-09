@php
$isEn = app()->getLocale() === 'en';
$i18n = [
'title' => $isEn ? 'Restoran Booking' : 'Booking Restoran',
'name' => $isEn ? 'Name' : 'Nama',
'full_name' => $isEn ? 'Full name' : 'Nama lengkap',
'email' => 'Email',
'whatsapp' => $isEn ? 'WhatsApp' : 'WhatsApp',
'wa_placeholder' => $isEn ? 'WhatsApp number' : 'Nomor WhatsApp',
'promo_code' => $isEn ? 'Promo Code' : 'Kode Promo',
'promo_placeholder' => $isEn ? 'Enter promo code' : 'Masukkan kode promo',
'use' => $isEn ? 'Apply' : 'Gunakan',
'used' => $isEn ? 'Applied' : 'Dipakai',
'total_price' => $isEn ? 'Total Price' : 'Total Harga',
'book_now' => $isEn ? 'Lanjut ke Pembayaran' : 'Lanjut ke Pembayaran',
'processing' => $isEn ? 'Processing...' : 'Memproses...',
'promo_already_used' => $isEn ? 'Promo has already been applied for this booking.' : 'Promo sudah digunakan untuk booking ini.',
'promo_empty' => $isEn ? 'Promo code is empty.' : 'Kode promo belum diisi.',
'pick_menu_first' => $isEn ? 'Please select at least 1 menu first.' : 'Silakan pilih minimal 1 menu makanan terlebih dahulu.',
'server_unreachable' => $isEn ? 'Failed to reach server.' : 'Gagal menghubungi server.',
'required_fields' => $isEn ? 'Name, email, and WhatsApp are required.' : 'Nama, email, dan WhatsApp wajib diisi.',
'required_dates' => $isEn ? 'Visit date, time, and participants are required.' : 'Tanggal kunjungan, jam reservasi, & jumlah peserta wajib diisi.',
];
@endphp

<script>
  window.restoranBookingPopup = function(basePrice, slug) {
    const I18N = @json($i18n);

    // Hitung tanggal minimal besok (H-1) dalam waktu lokal YYYY-MM-DD
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowYear = tomorrow.getFullYear();
    const tomorrowMonth = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const tomorrowDay = String(tomorrow.getDate()).padStart(2, '0');
    const minTomorrowStr = `${tomorrowYear}-${tomorrowMonth}-${tomorrowDay}`;

    return {
      isOpen: false,
      loading: false,

      name: '',
      email: '',
      phone: '',

      promoCode: '',
      promoId: null,
      promoMsg: '',
      promoLocked: false,
      promoLoading: false,

      minDate: minTomorrowStr,
      departure_date: minTomorrowStr,
      pickup_time: '12:00',
      participants: 1,

      cart: [],
      subtotal: 0,
      discount: 0,
      total: 0,

      token: @json(csrf_token()),

      init() {
        this.calc();
      },

      get cartTotalQty() {
        return this.cart.reduce((sum, item) => sum + (Number(item.qty) || 0), 0);
      },

      addMenuFromEvent(detail) {
        if (!detail || !detail.id) return;
        const existing = this.cart.find(i => i.id === detail.id);
        if (existing) {
          existing.qty += 1;
        } else {
          this.cart.push({
            id: detail.id,
            name: detail.name || '',
            price: Number(detail.price) || 0,
            qty: 1,
            thumbnail: detail.thumbnail || ''
          });
        }
        this.calc();
      },

      updateQty(id, delta) {
        const item = this.cart.find(i => i.id === id);
        if (item) {
          item.qty += delta;
          if (item.qty <= 0) {
            this.removeItem(id);
          } else {
            this.calc();
          }
        }
      },

      removeItem(id) {
        this.cart = this.cart.filter(i => i.id !== id);
        this.calc();
      },

      clearCart() {
        this.cart = [];
        this.calc();
      },

      open(detail) {
        if (detail?.departure_date && detail.departure_date >= this.minDate) {
          this.departure_date = detail.departure_date;
        } else {
          this.departure_date = this.minDate;
        }
        if (detail?.pickup_time) this.pickup_time = detail.pickup_time;
        if (detail?.participants) this.participants = detail.participants;

        if (detail?.id) {
          this.addMenuFromEvent(detail);
        }

        this.isOpen = true;
        this.calc();
      },

      close() {
        this.isOpen = false;
        this.loading = false;
      },

      goToMenuSection() {
        this.isOpen = false;
        setTimeout(() => {
          const el = document.getElementById('daftar-menu-resto') || document.querySelector('[data-purpose="fitur-paket"]');
          if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 100);
      },

      calc() {
        this.subtotal = this.cart.reduce((sum, item) => {
          return sum + ((Number(item.price) || 0) * (Number(item.qty) || 0));
        }, 0);

        if (this.promoId && this.discount > 0) {
          this.total = Math.max(0, this.subtotal - this.discount);
        } else {
          this.total = this.subtotal;
        }
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

        if (this.promoLocked) {
          this.promoMsg = `<span class="text-slate-600">${I18N.promo_already_used}</span>`;
          return;
        }
        if (this.promoLoading) return;

        const code = (this.promoCode || '').trim();
        if (!code) {
          this.promoMsg = `<span class="text-red-600">${I18N.promo_empty}</span>`;
          return;
        }
        if (this.subtotal <= 0) {
          this.promoMsg = `<span class="text-red-600">${I18N.pick_menu_first}</span>`;
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
              this.promoMsg = `<span class="text-red-600">${res.message}</span>`;
              this.promoId = null;
              this.discount = 0;
              this.calc();
              return;
            }
            this.discount = (this.subtotal - res.final_price) > 0 ? (this.subtotal - res.final_price) : 0;
            this.total = res.final_price;
            this.promoId = res.promo_id;
            this.promoLocked = true;
            this.promoMsg = `<span class="text-emerald-600 font-semibold">Diskon berhasil diterapkan!</span>`;
          })
          .catch(() => {
            this.promoLoading = false;
            this.promoMsg = `<span class="text-red-600">${I18N.server_unreachable}</span>`;
          });
      },

      submitBooking() {
        if (!this.name || !this.email || !this.phone) {
          alert(I18N.required_fields);
          return;
        }
        if (!this.departure_date || !this.pickup_time || !this.participants) {
          alert(I18N.required_dates);
          return;
        }
        if (this.departure_date < this.minDate) {
          alert('Waktu reservasi minimal harus H-1 (mulai besok).');
          return;
        }
        if (this.cart.length === 0) {
          alert(I18N.pick_menu_first);
          return;
        }

        this.loading = true;

        fetch(`/restoran/${slug}/draft-booking`, {
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
              departure_date: this.departure_date,
              pickup_time: this.pickup_time,
              participants: this.participants,
              promo_id: this.promoId ? Number(this.promoId) : null,
              menus: this.cart.map(i => ({
                id: i.id,
                qty: i.qty
              }))
            })
          })
          .then(async (r) => {
            const data = await r.json().catch(() => null);

            if (!r.ok) {
              const msg = data?.error || data?.message || `Booking gagal (HTTP ${r.status})`;
              alert(msg);
              this.loading = false;
              return null;
            }

            return data;
          })
          .then(res => {
            if (res && res.redirect) {
              window.location.href = res.redirect;
            } else if (res) {
              alert('Gagal membuat pesanan restoran.');
              this.loading = false;
            }
          })
          .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
            this.loading = false;
          });
      }
    };
  };

  if (window.Alpine && typeof Alpine.data === 'function') {
    Alpine.data('restoranBookingPopup', window.restoranBookingPopup);
  } else {
    document.addEventListener('alpine:init', () => {
      if (window.Alpine && typeof Alpine.data === 'function') {
        Alpine.data('restoranBookingPopup', window.restoranBookingPopup);
      }
    });
  }
</script>

<div
  x-data="restoranBookingPopup({{ (int) $package->price_per_pax }}, '{{ $package->slug }}')"
  x-on:open-restoran-booking.window="open($event.detail)"
  x-on:resto-add-menu.window="addMenuFromEvent($event.detail)"
  x-cloak>

  {{-- FLOATING CART BAR (Muncul saat keranjang terisi dan modal belum terbuka) --}}
  <div
    x-show="cart.length > 0 && !isOpen"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-16 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-16 opacity-0"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-[92%] max-w-lg bg-slate-900/95 backdrop-blur-md text-white p-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center justify-between"
    style="display:none">
    <div class="flex items-center gap-3">
      <div class="relative w-10 h-10 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold">
        <i class="fa-solid fa-bag-shopping text-sm"></i>
        <span class="absolute -top-1.5 -right-1.5 bg-amber-400 text-slate-900 text-[10px] font-extrabold px-1.5 py-0.5 rounded-full leading-none" x-text="cartTotalQty"></span>
      </div>
      <div>
        <div class="text-xs text-slate-300 font-medium">
          <span class="font-bold text-white" x-text="cartTotalQty"></span> Menu Dipilih
        </div>
        <div class="text-sm font-extrabold text-sky-400">
          Rp <span x-text="format(total)"></span>
        </div>
      </div>
    </div>
    <button
      type="button"
      @click="isOpen = true"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-extrabold text-white shadow transition"
      style="background:#0194F3;"
      onmouseover="this.style.background='#0186DB'"
      onmouseout="this.style.background='#0194F3'">
      Lihat Pesanan
      <i class="fa-solid fa-angle-right text-[10px]"></i>
    </button>
  </div>

  {{-- BACKDROP & MODAL RESERVASI --}}
  <div
    x-show="isOpen"
    x-transition.opacity
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-[999] p-3 sm:p-4 overflow-y-auto"
    @click.self="close()"
    style="display:none">
    
    <div
      class="bg-white rounded-2xl w-full max-w-lg relative border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

      {{-- HEADER --}}
      <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <div>
          <h2 class="text-lg font-extrabold text-slate-900 mb-0">{{ $i18n['title'] }}</h2>
          <p class="text-xs text-slate-500 mt-0.5">{{ $package->title }}</p>
        </div>

        <button
          type="button"
          class="text-slate-400 hover:text-slate-700 text-2xl leading-none transition"
          @click="close()"
          aria-label="Close">&times;</button>
      </div>

      {{-- MODAL BODY --}}
      <div class="p-6 space-y-4 overflow-y-auto flex-1">

        {{-- 1. DATA PEMESAN --}}
        <div>
          <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">1. Data Pemesan</div>
          <div class="grid sm:grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-semibold text-slate-600">{{ $i18n['name'] }} <span class="text-red-500">*</span></label>
              <input type="text" x-model="name"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                placeholder="{{ $i18n['full_name'] }}" required>
            </div>

            <div>
              <label class="text-xs font-semibold text-slate-600">Email <span class="text-red-500">*</span></label>
              <input type="email" x-model="email"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                placeholder="Email" required>
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs font-semibold text-slate-600">WhatsApp <span class="text-red-500">*</span></label>
              <input type="text" x-model="phone"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                placeholder="{{ $i18n['wa_placeholder'] }}" required>
            </div>
          </div>
        </div>

        {{-- 2. JADWAL & JUMLAH TAMU --}}
        <div>
          <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">2. Jadwal & Meja Reservasi</div>
          <div class="grid sm:grid-cols-3 gap-3">
            <div>
              <label class="text-xs font-semibold text-slate-600">Tanggal Kunjungan <span class="text-red-500">*</span></label>
              <input type="date" x-model="departure_date" :min="minDate"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
              <p class="text-[10px] text-slate-400 mt-1">Minimal H-1 (mulai besok).</p>
            </div>
            <div>
              <label class="text-xs font-semibold text-slate-600">Jam Reservasi <span class="text-red-500">*</span></label>
              <input type="time" x-model="pickup_time"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400" required>
            </div>
            <div>
              <label class="text-xs font-semibold text-slate-600">Jumlah Tamu (Pax)</label>
              <input type="number" x-model.number="participants" min="1"
                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            </div>
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Biaya dihitung murni dari pesanan makanan di bawah (tanpa biaya per tamu).</p>
        </div>

        {{-- 3. KERANJANG MENU RESTORAN --}}
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">
              3. Menu Makanan yang Dipesan (<span x-text="cartTotalQty"></span>)
            </div>
            <template x-if="cart.length > 0">
              <button type="button" @click="clearCart()" class="text-[11px] text-red-500 hover:underline">
                Kosongkan
              </button>
            </template>
          </div>

          {{-- Empty state --}}
          <template x-if="cart.length === 0">
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-center">
              <i class="fa-solid fa-utensils text-slate-300 text-lg mb-1.5 block"></i>
              <p class="text-xs text-slate-600 font-semibold">Keranjang Menu Masih Kosong</p>
              <p class="text-[11px] text-slate-400 mt-0.5 mb-3">Pilih dan tambahkan menu lezat yang tersedia di bawah ini.</p>
              <button
                type="button"
                @click="goToMenuSection()"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition"
                style="background-color: #0194F3;">
                <i class="fa-solid fa-plus text-[10px]"></i>
                Tambahkan Pesanan
              </button>
            </div>
          </template>

          {{-- List of items --}}
          <template x-if="cart.length > 0">
            <div>
              <div class="rounded-xl border border-slate-200 divide-y divide-slate-100 max-h-48 overflow-y-auto bg-white">
                <template x-for="(item, idx) in cart" :key="item.id">
                  <div class="p-2.5 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                      <template x-if="item.thumbnail">
                        <img :src="item.thumbnail" class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0" :alt="item.name">
                      </template>
                      <template x-if="!item.thumbnail">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 flex-shrink-0">
                          <i class="fa-solid fa-bowl-food text-xs"></i>
                        </div>
                      </template>
                      <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate" x-text="item.name"></div>
                        <div class="text-[11px] text-slate-500">
                          Rp <span x-text="format(item.price)"></span>
                        </div>
                      </div>
                    </div>

                    {{-- Stepper Qty --}}
                    <div class="flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200">
                      <button type="button" @click="updateQty(item.id, -1)" class="w-5 h-5 rounded flex items-center justify-center font-bold text-slate-600 hover:bg-white transition leading-none">-</button>
                      <span class="w-6 text-center font-extrabold text-slate-900 text-xs" x-text="item.qty"></span>
                      <button type="button" @click="updateQty(item.id, 1)" class="w-5 h-5 rounded flex items-center justify-center font-bold text-slate-600 hover:bg-white transition leading-none">+</button>
                    </div>

                    {{-- Item Subtotal & Delete --}}
                    <div class="flex items-center gap-2">
                      <div class="font-extrabold text-slate-900 text-right min-w-[70px]">
                        Rp <span x-text="format(item.price * item.qty)"></span>
                      </div>
                      <button type="button" @click="removeItem(item.id)" class="text-slate-400 hover:text-red-500 p-1" title="Hapus menu">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                      </button>
                    </div>
                  </div>
                </template>
              </div>

              {{-- Tombol Tambahkan Menu Lainnya --}}
              <div class="mt-2 flex justify-between items-center">
                <span class="text-[11px] text-slate-400">Ingin menu tambahan?</span>
                <button
                  type="button"
                  @click="goToMenuSection()"
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                  <i class="fa-solid fa-plus text-[10px]"></i>
                  Tambahkan Pesanan
                </button>
              </div>
            </div>
          </template>
        </div>

        {{-- 4. KODE PROMO --}}
        <div>
          <label class="text-xs font-semibold text-slate-600">{{ $i18n['promo_code'] }}</label>
          <div class="mt-1 flex gap-2">
            <input
              type="text"
              x-model.trim="promoCode"
              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
              placeholder="{{ $i18n['promo_placeholder'] }}" />

            <button
              type="button"
              @click="applyPromo()"
              class="shrink-0 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 disabled:opacity-60 transition"
              :disabled="promoLoading || promoLocked">
              <span x-show="!promoLocked && !promoLoading">{{ $i18n['use'] }}</span>
              <span x-show="promoLocked && !promoLoading">{{ $i18n['used'] }}</span>
              <span x-show="promoLoading">{{ $i18n['processing'] }}</span>
            </button>
          </div>
          <div class="mt-1.5 text-xs" x-html="promoMsg"></div>
        </div>

        {{-- 5. RINGKASAN TOTAL HARGA --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-2">
          <div class="flex justify-between text-slate-600">
            <span>Subtotal Menu (<span x-text="cartTotalQty"></span> porsi)</span>
            <span class="font-bold text-slate-900">Rp <span x-text="format(subtotal)"></span></span>
          </div>
          <template x-if="discount > 0">
            <div class="flex justify-between text-emerald-600 font-semibold">
              <span>Diskon Promo</span>
              <span>- Rp <span x-text="format(discount)"></span></span>
            </div>
          </template>
          <div class="border-t border-slate-200 pt-2 flex justify-between items-center text-sm font-extrabold text-slate-900">
            <span>{{ $i18n['total_price'] }}</span>
            <span class="text-base text-sky-600">Rp <span x-text="format(total)"></span></span>
          </div>
        </div>

      </div>

      {{-- FOOTER / ACTION --}}
      <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3">
        <button
          type="button"
          @click="close()"
          class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">
          Tutup
        </button>

        <button
          type="button"
          class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white transition shadow disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2"
          style="background:#0194F3;"
          onmouseover="this.style.background='#0186DB'"
          onmouseout="this.style.background='#0194F3'"
          :disabled="loading"
          @click="submitBooking()">
          <span x-show="!loading">{{ $i18n['book_now'] }}</span>
          <span x-show="loading">{{ $i18n['processing'] }}</span>
          <i x-show="!loading" class="fa-solid fa-arrow-right text-[10px]"></i>
        </button>
      </div>

    </div>
  </div>
</div>