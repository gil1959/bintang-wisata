@extends('layouts.front')

@section('content')

<div class="container py-5">

    <div class="row g-4">

        {{-- LEFT IMAGE --}}
        <div class="col-md-6">
            <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                 class="w-100 shadow-sm"
                 style="border-radius: 12px; height: 340px; object-fit: cover;">
        </div>

        {{-- RIGHT INFO --}}
        <div class="col-md-6">

            <h2 class="fw-bold">{{ $package->title }}</h2>

            <p class="text-muted">
                <strong style="font-size: 20px;">Rp{{ number_format($package->price_per_day, 0, ',', '.') }}</strong>
                / hari
            </p>

            <h5 class="mt-4 fw-semibold">Fitur Paket</h5>

            <ul class="list-unstyled mt-2">
                @foreach ($package->features as $feat)
                    <li class="mb-2">
                        @if($feat['available'])
                            <span style="color: green;">✔</span>
                        @else
                            <span style="color: red;">✘</span>
                        @endif
                        {{ $feat['name'] }}
                    </li>
                @endforeach
            </ul>

            <hr>

            <h5 class="fw-semibold">Booking</h5>

            <form method="POST" action="#" id="bookingForm" onsubmit="return false;">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Pickup Date</label>
                    <input type="date" name="pickup_date" class="form-control" id="pickup">
                </div>

                <div class="mb-3">
                    <label class="form-label">Return Date</label>
                    <input type="date" name="return_date" class="form-control" id="return">
                </div>

                <div class="p-3 rounded shadow-sm mb-3" style="background:#f8f9ff;">
                    <div class="d-flex justify-content-between">
                        <span>Total Hari:</span>
                        <strong id="days">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Total Harga:</span>
                        <strong id="total">Rp0</strong>
                    </div>
                </div>

                {{-- BUTTON HANYA BUKA POPUP --}}
                <button type="button"
                        class="btn w-100 text-white mt-2"
                        style="background:#0194F3;"
                        id="btnBook"
                        disabled>
                    Book Now
                </button>

            </form>

        </div>

    </div>

</div>

{{-- POPUP BOOKING --}}
<div id="rentcarModal"
     class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center"
     style="background: rgba(0,0,0,.5); z-index: 1050;">

    <div class="bg-white rounded-3 shadow p-4"
         style="width: 100%; max-width: 480px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">Booking Rent Car</h5>
            <button type="button" class="btn btn-sm btn-light" id="btnCloseModal">
                ✕
            </button>
        </div>

        <form id="rentcarPopupForm" onsubmit="return false;">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" id="popupName" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="popupEmail" required>
            </div>

            <div class="mb-3">
                <label class="form-label">No. WhatsApp</label>
                <input type="text" name="phone" class="form-control" id="popupPhone" required>
            </div>

            {{-- PROMO --}}
            <div class="mb-3">
                <label class="form-label">Kode Promo (opsional)</label>

                <div class="input-group">
                    <input type="text"
                           name="promo_code"
                           class="form-control"
                           id="popupPromo"
                           placeholder="Contoh: LIBURAN50">

                    <button class="btn btn-primary" type="button" id="btnApplyPromo">
                        Gunakan
                    </button>
                </div>

                <small id="promoMessage" class="text-success d-none"></small>
                <small id="promoError" class="text-danger d-none"></small>
            </div>

            <div class="p-3 rounded mb-3" style="background:#f8f9ff;">
                <div class="d-flex justify-content-between">
                    <span>Total Hari:</span>
                    <strong id="popupDays">0</strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Diskon Promo:</span>
                    <strong id="popupDiscount">Rp0</strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Total Harga:</span>
                    <strong id="popupTotal">Rp0</strong>
                </div>
            </div>

            {{-- HIDDEN FIELDS --}}
            <input type="hidden" name="pickup_date" id="popupPickup">
            <input type="hidden" name="return_date" id="popupReturn">
            <input type="hidden" name="total_days" id="popupTotalDays">
            <input type="hidden" name="total_price" id="popupTotalPrice">
            <input type="hidden" name="discount" id="popupDiscountValue" value="0">

            <button type="submit"
                    class="btn w-100 text-white"
                    style="background:#0194F3;"
                    id="popupSubmitBtn">
                Booking Sekarang
            </button>
        </form>

    </div>
</div>


{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const pickup   = document.getElementById('pickup');
    const ret      = document.getElementById('return');
    const days     = document.getElementById('days');
    const total    = document.getElementById('total');
    const btnBook  = document.getElementById('btnBook');

    const modal        = document.getElementById('rentcarModal');
    const btnClose     = document.getElementById('btnCloseModal');
    const popupDays    = document.getElementById('popupDays');
    const popupTotal   = document.getElementById('popupTotal');
    const popupPickup  = document.getElementById('popupPickup');
    const popupReturn  = document.getElementById('popupReturn');
    const popupTotalDays  = document.getElementById('popupTotalDays');
    const popupTotalPrice = document.getElementById('popupTotalPrice');

    const popupDiscount = document.getElementById('popupDiscount');
    const popupDiscountValue = document.getElementById('popupDiscountValue');

    const pricePerDay = {{ $package->price_per_day }};

    function calc() {
        if (!pickup.value || !ret.value) return;

        let start = new Date(pickup.value);
        let end   = new Date(ret.value);

        if (end < start) {
            days.textContent  = '0';
            total.textContent = 'Rp0';
            btnBook.disabled  = true;
            return;
        }

        let diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        days.textContent = diff;

        let t = diff * pricePerDay;
        total.textContent = 'Rp' + t.toLocaleString('id-ID');

        btnBook.dataset.days  = diff;
        btnBook.dataset.total = t;
        btnBook.disabled      = false;
    }

    pickup.addEventListener('change', calc);
    ret.addEventListener('change', calc);

    btnBook.addEventListener('click', function () {
        const d = this.dataset.days || 0;
        const t = this.dataset.total || 0;

        popupDays.textContent  = d;
        popupTotal.textContent = 'Rp' + Number(t).toLocaleString('id-ID');

        popupPickup.value      = pickup.value;
        popupReturn.value      = ret.value;
        popupTotalDays.value   = d;
        popupTotalPrice.value  = t;

        modal.classList.remove('d-none');
        modal.classList.add('d-flex');
    });

    btnClose.addEventListener('click', function () {
        modal.classList.add('d-none');
        modal.classList.remove('d-flex');
    });

    // ===================================
    //            PROMO LOGIC
    // ===================================
    document.getElementById('btnApplyPromo').addEventListener('click', function () {

    const code = document.getElementById('popupPromo').value.trim();
    const promoMessage = document.getElementById('promoMessage');
    const promoError = document.getElementById('promoError');
    const baseTotal = Number(popupTotalPrice.value);

    promoMessage.classList.add('d-none');
    promoError.classList.add('d-none');

    if (!code) {
        promoError.textContent = "Masukkan kode promo.";
        promoError.classList.remove('d-none');
        return;
    }

    fetch("/promo/validate", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            code: code,
            price: baseTotal
        })
    })
    .then(r => r.json())
    .then(res => {

        if (!res.valid) {
            promoError.textContent = res.message;
            promoError.classList.remove('d-none');
            popupDiscount.textContent = "Rp0";
            popupDiscountValue.value = 0;
            popupTotal.textContent = "Rp" + baseTotal.toLocaleString("id-ID");
            return;
        }

        // Apply diskon dari server
        popupDiscount.textContent = "Rp" + res.discount.toLocaleString("id-ID");
        popupDiscountValue.value = res.discount;
        popupTotal.textContent = "Rp" + res.final_price.toLocaleString("id-ID");

        promoMessage.textContent = "Promo berhasil digunakan!";
        promoMessage.classList.remove('d-none');
    })
    .catch(err => {
        promoError.textContent = "Terjadi kesalahan koneksi.";
        promoError.classList.remove('d-none');
    });

});

});
</script>

@endsection
