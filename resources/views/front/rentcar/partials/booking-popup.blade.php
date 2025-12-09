<div
    x-data="{
        isOpen:false,
        name:'',
        email:'',
        phone:'',
        promo:'',
        promoId:'',
        base: {{ $package->price_per_day }},
        days: 0,
        total: 0,

        open(){
            this.isOpen = true;
            this.calc();
        },

        calc(){
            let pickup = document.getElementById('pickup').value;
            let ret    = document.getElementById('return').value;

            if (pickup && ret) {
                let start = new Date(pickup);
                let end   = new Date(ret);
                let diff  = Math.ceil((end - start)/(1000*60*60*24)) + 1;
                this.days = diff > 0 ? diff : 1;
            }

            this.total = this.days * this.base;
        },

        applyPromo(){
            const msg = document.getElementById('promoMsg');
            msg.innerHTML = '';

            fetch('/promo/validate', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrf()
                },
                body: JSON.stringify({
                    code: this.promo,
                    price: this.total
                })
            })
            .then(r => r.json())
            .then(res => {
                if(!res.valid){
                    msg.innerHTML = `<span class='text-danger'>${res.message}</span>`;
                    this.promoId = '';
                    return;
                }

                this.total   = res.final_price;
                this.promoId = res.promo_id;

                msg.innerHTML = `<span class='text-success'>Diskon diterapkan!</span>`;
            })
            .catch(() => {
                msg.innerHTML = `<span class='text-danger'>Gagal menghubungi server.</span>`;
            });
        },

        submitBooking(){
            if(!this.name || !this.email || !this.phone){
                alert('Lengkapi data terlebih dahulu.');
                return;
            }

            fetch(`/rent-car/{{ $package->slug }}/draft-booking`, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrf()
                },
                body: JSON.stringify({
                    type: 'rent_car',
                    product_id: {{ $package->id }},

                    name: this.name,
                    email: this.email,
                    phone: this.phone,

                    pickup_date: document.getElementById('pickup').value,
                    return_date: document.getElementById('return').value,

                    total_days: this.days,
                    promo_id: this.promoId,
                    frontend_total: this.total
                })
            })
            .then(r => r.json())
            .then(res => {
                if(res.redirect){
                    window.location.href = res.redirect;
                } else {
                    alert('Gagal membuat pesanan.');
                }
            });
        }
    }"
>
    <div x-show="isOpen" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-lg">

            <h2 class="text-xl font-bold mb-3">Booking Rent Car</h2>

            <div class="space-y-3">

                <input type="text" x-model="name" class="form-control" placeholder="Nama lengkap">
                <input type="email" x-model="email" class="form-control" placeholder="Email">
                <input type="text" x-model="phone" class="form-control" placeholder="WhatsApp">

                <!-- PROMO -->
                <div>
                    <div class="input-group">
                        <input type="text" x-model="promo" class="form-control" placeholder="Kode Promo">
                        <button type="button" class="btn btn-primary" @click="applyPromo()">Gunakan</button>
                    </div>
                    <div id="promoMsg" class="mt-1 text-sm"></div>
                </div>

                <!-- TOTAL -->
                <div class="p-3 bg-gray-100 rounded">
                    <div class="flex justify-between">
                        <span>Total Hari:</span> 
                        <b x-text="days"></b>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span>Total Harga:</span>
                        <b>Rp <span x-text="total.toLocaleString('id-ID')"></span></b>
                    </div>
                </div>

                <button class="btn btn-success w-full" @click="submitBooking()">Booking Sekarang</button>

            </div>

        </div>
    </div>
</div>

<script>
function csrf(){
    return document.querySelector('meta[name="csrf-token"]').content;
}
</script>
