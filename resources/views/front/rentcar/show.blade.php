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
                <strong style="font-size: 20px;">
                    Rp{{ number_format($package->price_per_day, 0, ',', '.') }}
                </strong>
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

            <form id="bookingForm" onsubmit="return false;">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Pickup Date</label>
                    <input type="date" class="form-control" id="pickup">
                </div>

                <div class="mb-3">
                    <label class="form-label">Return Date</label>
                    <input type="date" class="form-control" id="return">
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

                {{-- Tombol hanya membuka popup modern checkout --}}
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

{{-- Popup booking modern (Alpine) --}}
@include('front.rentcar.partials.booking-popup')

{{-- Script perhitungan hari & harga + trigger popup --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const pickup  = document.getElementById('pickup');
    const ret     = document.getElementById('return');
    const daysEl  = document.getElementById('days');
    const totalEl = document.getElementById('total');
    const btnBook = document.getElementById('btnBook');

    const pricePerDay = {{ $package->price_per_day }};

    function recalc() {
        if (!pickup.value || !ret.value) {
            daysEl.textContent  = '0';
            totalEl.textContent = 'Rp0';
            btnBook.disabled    = true;
            return;
        }

        const start = new Date(pickup.value);
        const end   = new Date(ret.value);

        if (end < start) {
            daysEl.textContent  = '0';
            totalEl.textContent = 'Rp0';
            btnBook.disabled    = true;
            return;
        }

        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

        daysEl.textContent = diffDays;

        const total = diffDays * pricePerDay;
        totalEl.textContent = 'Rp' + total.toLocaleString('id-ID');

        btnBook.disabled = false;
    }

    pickup.addEventListener('change', recalc);
    ret.addEventListener('change', recalc);

    btnBook.addEventListener('click', function () {
        // Alpine component di partial akan dengar event ini dan membuka popup
        window.dispatchEvent(new CustomEvent('open-rentcar-booking'));
    });
});
</script>

@endsection
