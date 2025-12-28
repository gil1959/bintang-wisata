@extends('layouts.front')

@php
    $metaDesc = $package->seo_description
        ?: \Illuminate\Support\Str::limit(trim(strip_tags($package->long_description ?? '')), 160);

    $metaKeys = $package->seo_keywords ?? '';
@endphp

@section('title', $package->seo_title ?? $package->title)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeys)

@section('content')

<div
    x-data='{
        tiers: @json($package->tiers->values()),
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

        {{-- GALLERY --}}
        @if($package->photos->count())
            <section class="mt-2">
                <div class="grid grid-cols-3 gap-3">
                    @foreach($package->photos as $photo)
                        <img
                            src="{{ asset('storage/' . $photo->file_path) }}"
                            class="w-full h-28 md:h-32 object-cover rounded-xl shadow hover:opacity-90 transition cursor-pointer"
                            alt="Gallery photo"
                            loading="lazy"
                        >
                    @endforeach
                </div>
            </section>
        @endif

        {{-- TITLE + META --}}
        <div class="mt-2">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $package->title }}</h1>

            <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                @php
                    $avg = (float) ($package->rating_value ?? 5);
                    $count = (int) ($package->rating_count ?? 0);
                    $rounded = (int) round($avg);
                @endphp

                <div class="flex gap-1">
                    @for($i=1;$i<=5;$i++)
                        <svg class="w-4 h-4 {{ $i <= $rounded ? 'text-amber-400' : 'text-slate-300' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.444a1 1 0 00-.364 1.118l1.286 3.955c.3.921-.755 1.688-1.54 1.118l-3.363-2.444a1 1 0 00-1.175 0L6.98 18.007c-.784.57-1.838-.197-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L3 9.382c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.286-3.955z"/>
                        </svg>
                    @endfor
                </div>

                <span class="font-semibold">{{ number_format($avg, 1) }}/5</span>
                <span class="text-slate-500">· {{ $count }} ulasan</span>
            </div>

            <div class="mt-2 text-sm text-gray-500 flex flex-wrap items-center gap-4">
                @if($package->duration_text)
                    <span class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-500"></i>
                        <span>{{ $package->duration_text }}</span>
                    </span>
                @endif

                @if($package->destination)
                    <span class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-gray-500"></i>
                        <span>{{ $package->destination }}</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- DESCRIPTION --}}
        @if($package->long_description)
            <section class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold mb-3 text-[#0194F3]">Tentang Paket</h2>
                <div class="text-sm leading-relaxed text-gray-700">
                    {!! $package->long_description !!}
                </div>
            </section>
        @endif

        {{-- ITINERARY (UMRAH: WYSIWYG HTML) --}}
        @if($package->itinerary)
            <section class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="text-lg font-semibold mb-4 text-[#0194F3] flex items-center gap-2">
                    <i data-lucide="map" class="w-5 h-5 text-[#0194F3]"></i>
                    <span>Itinerary Perjalanan</span>
                </h2>

                <div class="text-sm leading-relaxed text-gray-700">
                    {!! $package->itinerary !!}
                </div>
            </section>
        @endif

        {{-- INCLUDE / EXCLUDE (UMRAH: WYSIWYG HTML) --}}
        <div class="grid md:grid-cols-2 gap-4">
            @if($package->include_text)
                <section class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-green-700 mb-3 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        Termasuk (Include)
                    </h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {!! $package->include_text !!}
                    </div>
                </section>
            @endif

            @if($package->exclude_text)
                <section class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <h2 class="text-lg font-semibold text-red-700 mb-3 flex items-center gap-2">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                        Tidak Termasuk (Exclude)
                    </h2>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        {!! $package->exclude_text !!}
                    </div>
                </section>
            @endif
        </div>

    </div>

    {{-- =============== SIDEBAR RESERVATION ONLY =============== --}}
    <aside class="md:col-span-1 space-y-6">
        @include('front.umrah.partials.reservation')
    </aside>

    {{-- =============== REVIEWS =============== --}}
    <section class="md:col-span-2 bg-white rounded-xl shadow-sm p-5">
        @include('front.partials.reviews', ['item' => $package, 'type' => 'umrah'])
    </section>

    {{-- =============== POPUP BOOKING =============== --}}
    @include('front.umrah.partials.booking-popup')

</div>
@endsection
