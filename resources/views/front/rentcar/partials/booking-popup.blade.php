<div
  x-data="rentcarBookingPopup({{ (int) $package->price_per_day }}, '{{ $package->slug }}')"
  x-on:open-rentcar-booking.window="open($event.detail)"
  x-cloak
>
  {{-- backdrop --}}
  <div
    x-show="isOpen"
    x-transition.opacity
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-[999]"
    @click.self="close()"
    style="display:none"
  >
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg relative border border-slate-200 shadow-2xl">

      {{-- HEADER --}}
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-extrabold text-slate-900 mb-0">Booking Rent Car</h2>

        <button
          type="button"
          class="text-slate-400 hover:text-slate-700 text-2xl leading-none"
          @click="close()"
          aria-label="Close"
        >×</button>
      </div>

      <div class="space-y-3">

        <div class="grid sm:grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-semibold text-slate-600">Nama</label>
            <input type="text" x-model="name"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="Nama lengkap">
          </div>

          <div>
            <label class="text-xs font-semibold text-slate-600">Email</label>
            <input type="email" x-model="email"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="Email">
          </div>

          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-slate-600">WhatsApp</label>
            <input type="text" x-model="phone"
              class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="Nomor WhatsApp">
          </div>
        </div>

        {{-- Pickup/Return readonly (biar user tau yang kepilih) --}}
        <div class="grid sm:grid-cols-2 gap-3">
          <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <div class="text-xs text-slate-500">Pickup</div>
            <div class="text-sm font-bold text-slate-900" x-text="pickup || '-'"></div>
          </div>
          <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
            <div class="text-xs text-slate-500">Return</div>
            <div class="text-sm font-bold text-slate-900" x-text="ret || '-'"></div>
          </div>
        </div>

        {{-- PROMO --}}
        <div>
          <label class="text-xs font-semibold text-slate-600">Kode Promo</label>
          <div class="mt-1 flex gap-2">
            <input type="text" x-model="promo"
              class="flex-1 rounded-xl border border-slate-200 px-3 py-2 focus:ring-brand-500 focus:border-brand-500"
              placeholder="Masukkan kode promo">
            <button
              type="button"
              class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800 transition"
              @click="applyPromo()"
            >
              Gunakan
            </button>
          </div>
          <div class="mt-1 text-sm" x-html="promoMsg"></div>
        </div>

        {{-- TOTAL --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm">
          <div class="flex justify-between">
            <span class="text-slate-600">Total Hari</span>
            <b x-text="days"></b>
          </div>
          <div class="flex justify-between mt-1">
            <span class="text-slate-600">Total Harga</span>
            <b>Rp <span x-text="format(total)"></span></b>
          </div>
        </div>

        <button
          type="button"
          class="w-full rounded-xl bg-brand-500 py-3 text-white font-extrabold hover:bg-brand-600 transition disabled:opacity-60 disabled:cursor-not-allowed"
          :disabled="loading"
          @click="submitBooking()"
        >
          <span x-show="!loading">Booking Sekarang</span>
          <span x-show="loading">Memproses...</span>
        </button>

      </div>

    </div>
  </div>
</div>

<script>
function rentcarBookingPopup(basePrice, slug){
  return {
    isOpen:false,
    loading:false,

    name:'',
    email:'',
    phone:'',

    promo:'',
    promoId:null,
    promoMsg:'',

    pickup:'',
    ret:'',

    base: basePrice,
    days: 0,
    total: 0,

    // inject token langsung dari blade (ANTI 419)
    token: @json(csrf_token()),

    open(detail){
      this.pickup = detail?.pickup || '';
      this.ret    = detail?.ret || '';
      this.isOpen = true;
      this.promoMsg = '';
      this.promoId = null;
      this.calc();
    },

    close(){
      this.isOpen = false;
      this.loading = false;
    },

    calc(){
      if(!this.pickup || !this.ret){
        this.days = 0;
        this.total = 0;
        return;
      }
      const start = new Date(this.pickup);
      const end   = new Date(this.ret);
      if(end < start){
        this.days = 0;
        this.total = 0;
        return;
      }
      const diff = Math.ceil((end - start)/(1000*60*60*24)) + 1;
      this.days = diff;
      this.total = diff * this.base;
    },

    format(n){
      try { return Number(n || 0).toLocaleString('id-ID'); }
      catch(e){ return n; }
    },

    applyPromo(){
      this.promoMsg = '';

      if(!this.promo){
        this.promoMsg = `<span class="text-red-600">Kode promo belum diisi.</span>`;
        return;
      }
      if(this.total <= 0){
        this.promoMsg = `<span class="text-red-600">Pilih tanggal terlebih dahulu.</span>`;
        return;
      }

      fetch('/promo/validate', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
          _token: this.token,
          code: this.promo,
          price: this.total
        })
      })
      .then(r => r.json())
      .then(res => {
        if(!res.valid){
          this.promoMsg = `<span class="text-red-600">${res.message}</span>`;
          this.promoId = null;
          return;
        }
        this.total = res.final_price;
        this.promoId = res.promo_id;
        this.promoMsg = `<span class="text-emerald-600">Diskon diterapkan!</span>`;
      })
      .catch(() => {
        this.promoMsg = `<span class="text-red-600">Gagal menghubungi server.</span>`;
      });
    },

    submitBooking(){
      if(!this.name || !this.email || !this.phone){
        alert('Nama, email, dan WhatsApp wajib diisi.');
        return;
      }
      if(!this.pickup || !this.ret){
        alert('Pickup & Return date wajib diisi.');
        return;
      }

      this.loading = true;

      fetch(`/rent-car/${slug}/draft-booking`, {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'Accept':'application/json'
        },
        body: JSON.stringify({
          _token: this.token,
          name: this.name,
          email: this.email,
          phone: this.phone,
          pickup: this.pickup,
          return: this.ret,
          promo_id: this.promoId ? Number(this.promoId) : null,
           final_price: this.total
        })
      })
      .then(async (r) => {
        const text = await r.text();

        if(!r.ok){
          // biar gak “nebak-nebak” lagi, tampilkan error mentahnya
          console.error('Draft rent car failed', r.status, text);
          alert(`Booking gagal (HTTP ${r.status}). Lihat Console untuk detail.`);
          this.loading = false;
          return null;
        }

        // kalau response json
        try { return JSON.parse(text); }
        catch(e){
          console.error('Response bukan JSON:', text);
          alert('Booking gagal: response server bukan JSON.');
          this.loading = false;
          return null;
        }
      })
      .then(res => {
        if(res && res.redirect){
          window.location.href = res.redirect;
        }else if(res){
          alert('Gagal membuat pesanan.');
          this.loading = false;
        }
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan jaringan.');
        this.loading = false;
      });
    }
  }
}
</script>
