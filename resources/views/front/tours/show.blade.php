@extends('layouts.front')

@section('title', $package->title)

@section('content')

<div
    x-data='{
        active: "domestic",
        tiers: {
            domestic: @json($package->tiers->where("type", "domestic")->values()),
            international: @json($package->tiers->where("type", "international")->values())
        },
        flightInfo: "{{ $package->flight_info }}",
        selectedTier: null
    }'
    class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8"
>

    {{-- =============== LEFT CONTENT =============== --}}
    <div class="md:col-span-2 space-y-8">

        {{-- THUMBNAIL --}}
        @if($package->thumbnail_path)
            <div class="-mt-6 -mx-4 md:mx-0">
                <img
                    src="{{ asset('storage/' . $package->thumbnail_path) }}"
                    class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-md"
                    alt="{{ $package->title }}"
                >
            </div>
        @endif
        {{-- GALLERY / FOTO TAMBAHAN --}}
@if($package->photos->count())
    <section class="mt-6">
        <div class="grid grid-cols-3 gap-3">
            @foreach($package->photos as $photo)
                <img 
                    src="{{ asset('storage/' . $photo->file_path) }}"
                    class="w-full h-32 object-cover rounded-xl shadow hover:opacity-90 transition cursor-pointer"
                    alt="Gallery photo"
                >
            @endforeach
        </div>
    </section>
@endif


        {{-- TITLE + META --}}
        <div class="mt-4">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $package->title }}</h1>

            <div class="mt-2 text-sm text-gray-500 flex flex-wrap items-center gap-4">
                @if($package->duration_text)
                    <span class="flex items-center gap-1">
                        <span>🕒</span> <span>{{ $package->duration_text }}</span>
                    </span>
                @endif

                @if($package->destination)
                    <span class="flex items-center gap-1">
                        <span>📍</span> <span>{{ $package->destination }}</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- DESCRIPTION --}}
        @if($package->long_description)
            <section class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold mb-3 text-[#0194F3]">Tentang Paket</h2>
                <div class="text-sm leading-relaxed text-gray-700">
                    {!! nl2br(e($package->long_description)) !!}
                </div>
            </section>
        @endif

        {{-- ITINERARY --}}
        @if($package->itineraries->count())
            <section class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold mb-4 text-[#0194F3] flex items-center gap-2">
                    🗺️ <span>Itinerary Perjalanan</span>
                </h2>

                <div class="space-y-4">
                    @foreach($package->itineraries as $item)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-[#0194F3]"></div>
                                <div class="flex-1 w-px bg-[#0194F3]/30"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $item->time }}</p>
                                <p class="text-sm text-gray-700">{{ $item->title }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- INCLUDE / EXCLUDE --}}
        <div class="grid md:grid-cols-2 gap-4">
            @if($package->includes)
                <section class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-green-700 mb-3">
                        ✓ Termasuk (Include)
                    </h2>
                    <ul class="list-disc ml-5 space-y-1 text-sm text-gray-700">
                        @foreach($package->includes as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if($package->excludes)
                <section class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-red-700 mb-3">
                        ✕ Tidak Termasuk (Exclude)
                    </h2>
                    <ul class="list-disc ml-5 space-y-1 text-sm text-gray-700">
                        @foreach($package->excludes as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

    </div>

    {{-- =============== SIDEBAR RESERVATION =============== --}}
    @include('front.tours.partials.reservation')

    {{-- =============== POPUP BOOKING =============== --}}
    @include('front.tours.partials.booking-popup')

</div>
@endsection

@section('scripts')
{{-- tidak perlu JS tambahan di sini, semua logic di Alpine inline --}}
@endsection
