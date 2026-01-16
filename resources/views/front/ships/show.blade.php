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
    active: "weekday",
    tiers: {
      weekday: @json($package->tiers->where("type","weekday")->values()),
      weekend: @json($package->tiers->where("type","weekend")->values())
    },

    // selection per tab
    selected: { weekday: null, weekend: null },

    // always reflect selection for active tab
    get selectedTier() { return this.selected[this.active]; },

    selectTier(tier) { this.selected[this.active] = tier; }
  }'
  class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8"
>

  <div class="md:col-span-2 space-y-8">

    <div class="rounded-3xl overflow-hidden shadow-sm border border-slate-200 bg-white">
      <img
        src="{{ $package->thumbnail_path ? asset('storage/'.$package->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Sewa+Kapal' }}"
        alt="{{ $package->title }}"
        class="w-full h-[360px] object-cover"
      >
    </div>

    <div>
      <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900">{{ $package->title }}</h1>

      <div class="mt-2 flex items-center gap-2 text-sm text-slate-700">
        @php
          $avg = (float)($package->rating_value ?? 5);
          $count = (int)($package->rating_count ?? 0);
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
        <span class="text-slate-500"> {{ $count }} ulasan</span>
      </div>
    </div>

    <section class="bg-white border border-slate-200 rounded-2xl p-6">
      <h2 class="text-lg font-bold text-slate-900 mb-4">Fitur Paket</h2>

      @if(!empty($package->features))
        <ul class="grid sm:grid-cols-2 gap-3 text-sm">
          @foreach($package->features as $feat)
            <li class="flex items-center gap-2">
              @if(!empty($feat['available']))
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
              @else
                <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
              @endif
              <span class="text-slate-700">{{ $feat['name'] ?? '-' }}</span>
            </li>
          @endforeach
        </ul>
      @else
        <div class="text-sm text-slate-500">Belum ada fitur yang ditambahkan.</div>
      @endif
    </section>

    @if(!empty($package->long_description))
      <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-lg font-extrabold text-slate-900 mb-4">Deskripsi</div>
        <div class="prose max-w-none break-words">
  {!! $package->long_description !!}
</div>

      </div>
    @endif




</div>

{{-- RESERVASI (DESKTOP: sidebar kanan) --}}

<aside class="md:col-span-1 space-y-6">
  @include('front.ships.partials.reservation')
</aside>
<section class="md:col-span-2 bg-white rounded-xl shadow-sm p-5">
  @include('front.partials.reviews', ['item' => $package, 'type' => 'ship'])
</section>


  @include('front.ships.partials.booking-popup', ['package' => $package])

</div>
@endsection
