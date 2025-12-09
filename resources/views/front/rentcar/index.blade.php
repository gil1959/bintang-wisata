@extends('layouts.front')

@section('content')

<div class="container py-5">

    <h2 class="fw-bold mb-4">Daftar Paket Rent Car</h2>

    <div class="row g-4">

        @foreach ($packages as $package)

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                     class="card-img-top"
                     style="height: 200px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px;">

                <div class="card-body">

                    <h5 class="fw-semibold">{{ $package->title }}</h5>

                    <p class="text-muted mb-1" style="font-size: 14px;">
                        <strong>Rp{{ number_format($package->price_per_day, 0, ',', '.') }}</strong> / hari
                    </p>

                    {{-- fitur singkat --}}
                    <div class="small text-muted mb-3">
                        @foreach(array_slice($package->features, 0, 3) as $f)
                            <div>
                                @if($f['available'])
                                    <span style="color: green;">✔</span>
                                @else
                                    <span style="color: red;">✘</span>
                                @endif
                                {{ $f['name'] }}
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('rentcar.show', $package->slug) }}"
                       class="btn w-100 text-white"
                       style="background:#0194F3; border-radius: 8px;">
                        Booking
                    </a>

                </div>
            </div>
        </div>

        @endforeach

    </div>

</div>

@endsection
