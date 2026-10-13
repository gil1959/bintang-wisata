@extends('layouts.front')

@section('meta')
    @php
        $mTitle = $package->armada_name . ' - ' . $package->departure_location . ' ke ' . $package->arrival_location;
        $mDesc = !empty($package->description) ? Str::limit(strip_tags($package->description), 150) : 'Pesan tiket ' . $package->armada_name . ' rute ' . $package->departure_location . ' menuju ' . $package->arrival_location . ' dengan harga terbaik di Bintang Wisata.';
        $mImage = !empty($package->thumbnail_path) ? asset('storage/' . $package->thumbnail_path) : asset('logo-atau-banner.jpg');
    @endphp
    <title>{{ $mTitle }} | Bintang Wisata</title>
    <meta name="description" content="{{ $mDesc }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $mTitle }}">
    <meta property="og:description" content="{{ $mDesc }}">
    <meta property="og:image" content="{{ $mImage }}">
@endsection

@push('styles')
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
@endpush

@php
    $realPhotos = [];
    if (!empty($package->thumbnail_path)) {
        $realPhotos[] = [
            'src' => asset('storage/' . $package->thumbnail_path),
            'alt' => $package->armada_name . ' - Cover',
        ];
    }
    if ($package->photos && $package->photos->count() > 0) {
        foreach ($package->photos as $idx => $photo) {
            if (!empty($photo->photo_path)) {
                $realPhotos[] = [
                    'src' => asset('storage/' . $photo->photo_path),
                    'alt' => $package->armada_name . ' - Galeri ' . ($idx + 1),
                ];
            }
        }
    }

    // Real Reviews
    $approvedReviews = $package->reviews ?? collect();
    $reviewCount = $approvedReviews->count();
    $avgRating = $reviewCount > 0 ? round((float)$approvedReviews->avg('rating'), 1) : null;

    $facilityLabels = [
        'tv' => ['label' => 'TV / Audio', 'icon' => 'fa-tv'],
        'reclining_seat' => ['label' => 'Reclining Seat', 'icon' => 'fa-couch'],
        'wifi' => ['label' => 'Free WiFi', 'icon' => 'fa-wifi'],
        'baggage' => ['label' => 'Bagasi Luas', 'icon' => 'fa-suitcase-rolling'],
        'usb' => ['label' => 'USB Charger', 'icon' => 'fa-plug'],
        'ac' => ['label' => 'AC Penuh', 'icon' => 'fa-snowflake'],
        'snack' => ['label' => 'Snack & Minuman', 'icon' => 'fa-cookie-bite'],
        'toilet' => ['label' => 'Toilet di Armada', 'icon' => 'fa-restroom'],
    ];

    $routes = (array)($package->route_details ?? []);
@endphp

@section('content')
<main class="max-w-[1240px] mx-auto px-4 py-6 space-y-6"
      x-data="{
          isBookingModalOpen: false,
          pax: 1,
          pricePerPax: {{ (float)$package->price }},
          departureDate: '{{ now()->addDay()->format('Y-m-d') }}',
          name: '',
          phone: '',
          email: '',
          promoCode: '',
          isSubmittingBooking: false,
          bookingError: '',
          
          // Lightbox state
          lightboxOpen: false,
          lightboxIndex: 0,
          photos: {{ json_encode($realPhotos) }},
          openLightbox(idx) {
              this.lightboxIndex = idx;
              this.lightboxOpen = true;
          },
          nextPhoto() {
              if (this.photos.length > 0) {
                  this.lightboxIndex = (this.lightboxIndex + 1) % this.photos.length;
              }
          },
          prevPhoto() {
              if (this.photos.length > 0) {
                  this.lightboxIndex = (this.lightboxIndex - 1 + this.photos.length) % this.photos.length;
              }
          },

          get totalPrice() {
              return this.pax * this.pricePerPax;
          },

          submitBooking() {
              if (this.isSubmittingBooking) return;
              if (!this.name || !this.phone || !this.email || !this.departureDate) {
                  this.bookingError = 'Mohon lengkapi semua data wajib pemesanan.';
                  return;
              }
              this.bookingError = '';
              this.isSubmittingBooking = true;

              fetch('{{ route('travel-bus.order.draft', $package->slug) }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'Accept': 'application/json',
                  },
                  body: JSON.stringify({
                      name: this.name,
                      phone: this.phone,
                      email: this.email,
                      departure_date: this.departureDate,
                      pax: this.pax,
                      promo_code: this.promoCode
                  })
              })
              .then(res => res.json())
              .then(data => {
                  if (data.redirect) {
                      window.location.href = data.redirect;
                  } else if (data.errors) {
                      const firstKey = Object.keys(data.errors)[0];
                      this.bookingError = data.errors[firstKey][0];
                      this.isSubmittingBooking = false;
                  } else if (data.error || data.message) {
                      this.bookingError = data.error || data.message;
                      this.isSubmittingBooking = false;
                  } else {
                      this.bookingError = 'Gagal memproses pemesanan. Coba lagi.';
                      this.isSubmittingBooking = false;
                  }
              })
              .catch(err => {
                  console.error(err);
                  this.bookingError = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                  this.isSubmittingBooking = false;
              });
          }
      }">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('travel-bus.index') }}" class="hover:text-blue-600 transition">Bus & Travel</a>
        <span>/</span>
        <span class="text-slate-800 font-bold truncate max-w-[280px]">{{ $package->armada_name }}</span>
    </nav>

    {{-- TOP IMAGE GALLERY & LIGHTBOX SLIDER --}}
    <section id="gallery">
        @if(count($realPhotos) === 0)
            <div class="w-full h-64 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col items-center justify-center text-slate-400">
                <i class="fa-solid fa-image text-4xl text-slate-300 mb-2"></i>
                <span class="text-xs font-bold">Foto galeri belum diunggah untuk armada ini</span>
            </div>
        @elseif(count($realPhotos) === 1)
            <div class="w-full h-[360px] md:h-[460px] rounded-3xl overflow-hidden bg-white shadow-sm cursor-pointer group relative"
                 @click="openLightbox(0)">
                <img src="{{ $realPhotos[0]['src'] }}" alt="{{ $realPhotos[0]['alt'] }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-sm text-white px-3.5 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-expand"></i>
                    <span>Klik untuk memperbesar</span>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 h-[380px] md:h-[460px] rounded-3xl overflow-hidden shadow-sm bg-white">
                <div class="md:col-span-8 h-full relative overflow-hidden group cursor-pointer"
                     @click="openLightbox(0)">
                    <img src="{{ $realPhotos[0]['src'] }}" alt="{{ $realPhotos[0]['alt'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="md:col-span-4 grid grid-rows-2 gap-2 h-full">
                    <div class="relative overflow-hidden group h-full cursor-pointer"
                         @click="openLightbox(1)">
                        <img src="{{ $realPhotos[1]['src'] }}" alt="{{ $realPhotos[1]['alt'] }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="relative overflow-hidden group h-full cursor-pointer"
                         @click="openLightbox({{ count($realPhotos) > 2 ? 2 : 0 }})">
                        @if(count($realPhotos) > 2)
                            <img src="{{ $realPhotos[2]['src'] }}" alt="{{ $realPhotos[2]['alt'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @if(count($realPhotos) > 3)
                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-[2px] flex flex-col items-center justify-center text-white p-4 text-center">
                                    <i class="fa-solid fa-images text-2xl mb-1"></i>
                                    <span class="text-sm font-black">+{{ count($realPhotos) - 2 }} Foto Lainnya</span>
                                    <span class="text-[11px] text-slate-200 font-medium">Klik untuk melihat semua</span>
                                </div>
                            @endif
                        @else
                            <img src="{{ $realPhotos[0]['src'] }}" alt="{{ $realPhotos[0]['alt'] }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- MAIN GRID LAYOUT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: Detail Lengkap Paket --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Card Info Header --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-200">
                        {{ $package->travel_type }}
                    </span>
                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-slate-100 text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-van-shuttle text-blue-500"></i>
                        <span>{{ $package->armada_type }}</span>
                    </span>

                    {{-- Real Rating Badge --}}
                    @if($reviewCount > 0)
                        <a href="#reviews" class="px-3 py-1 rounded-xl text-xs font-extrabold bg-blue-50 text-blue-600 border border-blue-200 flex items-center gap-1.5 hover:bg-blue-100 transition">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>{{ number_format($avgRating, 1) }} ({{ $reviewCount }} Ulasan)</span>
                        </a>
                    @else
                        <a href="#reviews" class="px-3 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-500 border border-slate-200 flex items-center gap-1.5 hover:bg-slate-100 transition">
                            <i class="fa-regular fa-star text-slate-400"></i>
                            <span>Belum ada ulasan</span>
                        </a>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 tracking-tight">
                    {{ $package->armada_name }}
                </h1>
                <p class="text-sm font-semibold text-slate-500 mt-1 flex items-center gap-2">
                    <i class="fa-solid fa-route text-blue-500"></i>
                    <span>{{ $package->departure_location }} &rarr; {{ $package->arrival_location }}</span>
                </p>

                {{-- JADWAL PERJALANAN & DURASI (Mendukung Multi-Hari) --}}
                <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-100 p-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        
                        {{-- Keberangkatan --}}
                        <div class="space-y-1">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock text-blue-500"></i>
                                <span>Keberangkatan</span>
                            </span>
                            <div class="text-2xl font-black text-slate-900">
                                {{ $package->formatted_departure_time }}
                            </div>
                            <div class="text-xs font-bold text-slate-600 max-w-[200px]">
                                {{ $package->departure_location }}
                            </div>
                        </div>

                        {{-- Durasi & Garis Perjalanan --}}
                        <div class="flex flex-col items-center justify-center text-center px-2 py-1 w-full sm:w-auto">
                            <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-200 mb-1">
                                {{ $package->formatted_duration }}
                            </span>
                            <div class="flex items-center gap-1.5 w-full max-w-[160px] text-blue-500 my-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 inline-block"></span>
                                <div class="flex-1 h-0.5 bg-blue-300"></div>
                                <i class="fa-solid fa-bus text-xs text-blue-600"></i>
                                <div class="flex-1 h-0.5 bg-blue-300"></div>
                                <i class="fa-solid fa-chevron-right text-xs text-orange-500"></i>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400">Perjalanan Terjadwal</span>
                        </div>

                        {{-- Tiba di Tujuan --}}
                        <div class="space-y-1 text-left sm:text-right">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-400 flex items-center sm:justify-end gap-1.5">
                                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                                <span>Estimasi Tiba</span>
                            </span>
                            <div class="text-2xl font-black text-slate-900 flex items-center sm:justify-end gap-2">
                                <span>{{ date('H:i', strtotime($package->arrival_time)) }}</span>
                                @if($package->arrival_days_offset > 0)
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                                        +{{ $package->arrival_days_offset }} Hari
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs font-bold text-slate-600 max-w-[200px]">
                                {{ $package->arrival_location }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- RUTE DETAIL & TITIK SINGGAH DINAMIS --}}
            <div id="routes" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Rute Detail & Titik Singgah</h2>
                        <p class="text-xs text-slate-500 font-medium">Jalur lintasan perjalanan armada</p>
                    </div>
                </div>

                <div class="space-y-0 pt-2">
                    {{-- Titik Awal --}}
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center">
                            <span class="w-4 h-4 rounded-full bg-blue-600 ring-4 ring-blue-100 shrink-0 shadow-sm"></span>
                            <div class="w-0.5 h-12 bg-slate-200 my-1"></div>
                        </div>
                        <div class="pb-4">
                            <div class="font-extrabold text-sm sm:text-base text-slate-900 leading-tight">
                                {{ $package->departure_location }}
                            </div>
                            <div class="text-xs text-slate-500 font-semibold mt-1">
                                Titik Keberangkatan (Jam {{ $package->formatted_departure_time }})
                            </div>
                        </div>
                    </div>

                    {{-- Titik Singgah Dinamis --}}
                    @if(count($routes) > 0)
                        @foreach($routes as $idx => $stop)
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="w-3.5 h-3.5 rounded-full bg-amber-500 ring-4 ring-amber-100 shrink-0 shadow-sm"></span>
                                    <div class="w-0.5 h-12 bg-slate-200 my-1"></div>
                                </div>
                                <div class="pb-4">
                                    <div class="font-bold text-sm text-slate-800 leading-tight">
                                        {{ $stop }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-semibold mt-1">
                                        Titik Singgah / Transit #{{ $idx + 1 }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Titik Akhir --}}
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center">
                            <span class="w-4 h-4 rounded-full bg-emerald-600 ring-4 ring-emerald-100 shrink-0 shadow-sm"></span>
                        </div>
                        <div>
                            <div class="font-extrabold text-sm sm:text-base text-slate-900 leading-tight">
                                {{ $package->arrival_location }}
                            </div>
                            <div class="text-xs text-slate-500 font-semibold mt-1">
                                Titik Tujuan Akhir (Estimasi {{ date('H:i', strtotime($package->arrival_time)) }}{{ $package->arrival_days_offset > 0 ? ' +' . $package->arrival_days_offset . ' Hari' : '' }})
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FASILITAS ARMADA --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Fasilitas Armada</h2>
                        <p class="text-xs text-slate-500 font-medium">Kenyamanan yang disediakan selama perjalanan</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @forelse((array)$package->facilities as $facKey)
                        @php $facInfo = $facilityLabels[$facKey] ?? ['label' => ucfirst($facKey), 'icon' => 'fa-check']; @endphp
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white text-blue-600 border border-slate-200 flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $facInfo['icon'] }} text-xs"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-800">{{ $facInfo['label'] }}</span>
                        </div>
                    @empty
                        <div class="col-span-full text-xs text-slate-500 italic py-2">
                            Fasilitas standar perjalanan darat tersedia.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- DESKRIPSI & KETENTUAN TIKET --}}
            <div id="terms" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-black text-slate-900 mb-2">Deskripsi & Spesifikasi</h2>
                    <div class="text-sm text-slate-600 leading-relaxed font-medium whitespace-pre-line">
                        {{ $package->description ?: 'Armada transportasi darat dengan standar kenyamanan prima, pengemudi profesional, dan rute terjadwal tepat waktu.' }}
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <h2 class="text-lg font-black text-slate-900 mb-2">Syarat & Ketentuan Tiket</h2>
                    <div class="text-sm text-slate-600 leading-relaxed font-medium whitespace-pre-line">
                        {{ $package->terms_conditions ?: "1. Penumpang diharapkan tiba di titik kumpul paling lambat 15 menit sebelum waktu keberangkatan.\n2. Pembatalan atau penundaan tiket wajib konfirmasi ke pihak travel sebelum armada berangkat.\n3. Setiap tiket berlaku untuk 1 (satu) kursi penumpang." }}
                    </div>
                </div>
            </div>

            {{-- REAL RATINGS & REVIEWS SECTION --}}
            <div id="reviews" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Ulasan & Rating Penumpang</h2>
                            <p class="text-xs text-slate-500 font-medium">Berdasarkan ulasan asli penumpang yang telah menggunakan layanan ini</p>
                        </div>
                    </div>
                </div>

                {{-- Rating Summary --}}
                <div class="flex flex-col sm:flex-row items-center gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="text-center sm:text-left">
                        <div class="text-4xl font-black text-slate-900 tracking-tight">
                            {{ $avgRating ? number_format($avgRating, 1) : '-' }}
                            <span class="text-base text-slate-400 font-bold">/ 5.0</span>
                        </div>
                        <div class="flex items-center gap-1 text-amber-400 mt-1 justify-center sm:justify-start">
                            @for($i = 1; $i <= 5; $i++)
                                @if($avgRating && $avgRating >= $i)
                                    <i class="fa-solid fa-star text-sm"></i>
                                @elseif($avgRating && $avgRating >= ($i - 0.5))
                                    <i class="fa-solid fa-star-half-stroke text-sm"></i>
                                @else
                                    <i class="fa-regular fa-star text-sm text-slate-300"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-xs font-bold text-slate-500 mt-1">
                            {{ $reviewCount }} Ulasan Terverifikasi
                        </div>
                    </div>
                    <div class="h-12 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="flex-1 text-xs text-slate-600 leading-relaxed font-medium">
                        Ulasan dikirimkan langsung oleh pelanggan setelah melakukan pemesanan dan perjalanan melalui Bintang Wisata Holiday.
                    </div>
                </div>

                {{-- List Approved Reviews --}}
                <div class="space-y-4">
                    @forelse($approvedReviews as $review)
                        <div class="p-5 rounded-2xl border border-slate-100 bg-white hover:border-slate-200 transition space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="font-extrabold text-sm text-slate-900">{{ $review->name }}</div>
                                <span class="text-xs text-slate-400">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $review->rating >= $i ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                {{ $review->comment }}
                            </p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs font-semibold">
                            Belum ada ulasan untuk armada ini. Jadilah yang pertama memberikan ulasan!
                        </div>
                    @endforelse
                </div>

                {{-- Form Tulis Ulasan --}}
                <div class="border-t border-slate-100 pt-6">
                    <h3 class="text-base font-extrabold text-slate-900 mb-3">Tulis Ulasan Anda</h3>

                    @if(session('success_review'))
                        <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                            {{ session('success_review') }}
                        </div>
                    @endif

                    <form action="{{ route('review.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="reviewable_type" value="travel_bus">
                        <input type="hidden" name="reviewable_id" value="{{ $package->id }}">
                        <input type="hidden" name="form_started_at" value="{{ time() }}">
                        {{-- Honeypot bot protection --}}
                        <div style="display:none !important;">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Nama Anda *</label>
                                <input type="text" name="name" required placeholder="Nama lengkap" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email Anda *</label>
                                <input type="email" name="email" required placeholder="email@contoh.com" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Rating Penilaian *</label>
                            <select name="rating" required class="w-full sm:w-48 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800">
                                <option value="5">5 Bintang (Luar Biasa)</option>
                                <option value="4">4 Bintang (Sangat Bagus)</option>
                                <option value="3">3 Bintang (Cukup Bagus)</option>
                                <option value="2">2 Bintang (Kurang Puas)</option>
                                <option value="1">1 Bintang (Sangat Buruk)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Ulasan / Komentar *</label>
                            <textarea name="comment" rows="3" required minlength="10" placeholder="Ceritakan pengalaman perjalanan Anda..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800"></textarea>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl font-extrabold text-white text-xs hover:brightness-105 transition" style="background:#0194F3;">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Sticky Booking Sidebar Card --}}
        <div class="lg:col-span-4 sticky top-24 space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-lg space-y-5">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block">Harga Tiket Per Kursi</span>
                    <div class="text-3xl font-black text-[#ea580c] tracking-tight mt-0.5">
                        Rp {{ number_format($package->price, 0, ',', '.') }}
                        <span class="text-xs font-bold text-slate-400">/kursi</span>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600 font-semibold border-y border-slate-100 py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Tipe Layanan:</span>
                        <span class="font-bold text-slate-800">{{ $package->travel_type }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Kendaraan:</span>
                        <span class="font-bold text-slate-800">{{ $package->armada_type }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Jam Berangkat:</span>
                        <span class="font-bold text-slate-800">{{ $package->formatted_departure_time }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Estimasi Durasi:</span>
                        <span class="font-bold text-blue-600">{{ $package->formatted_duration }}</span>
                    </div>
                </div>

                <button type="button"
                        @click="isBookingModalOpen = true"
                        class="w-full py-4 rounded-2xl font-black text-white shadow-md hover:brightness-105 transition flex items-center justify-center gap-2"
                        style="background: #f97316;">
                    <span>Pesan Tiket Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <p class="text-[11px] text-center text-slate-400 font-medium">
                    Tiket resmi langsung terkonfirmasi & bergaransi.
                </p>
            </div>
        </div>

    </div>

    {{-- MODAL POPUP PEMESANAN (Nama, No HP, Seat, Tanggal -> Checkout) --}}
    <div x-show="isBookingModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="isBookingModalOpen = false"></div>

        <div class="min-h-full flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6"
                 @click.stop>
                
                {{-- Header Modal --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Form Pemesanan Tiket</h3>
                        <p class="text-xs text-slate-500 font-semibold">{{ $package->armada_name }} ({{ $package->travel_type }})</p>
                    </div>
                    <button type="button" @click="isBookingModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Alert Error --}}
                <div x-show="bookingError" class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold" x-text="bookingError"></div>

                {{-- Form Fields --}}
                <div class="space-y-4">
                    {{-- Nama Pemesan --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Nama Lengkap Pemesan *</label>
                        <input type="text" x-model="name" placeholder="Sesuai KTP / Paspor" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800">
                    </div>

                    {{-- No HP / WhatsApp & Email --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">No. WhatsApp / HP *</label>
                            <input type="tel" x-model="phone" placeholder="08123456789" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email *</label>
                            <input type="email" x-model="email" placeholder="email@contoh.com" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    {{-- Tanggal Keberangkatan --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Tanggal Keberangkatan *</label>
                        <input type="date" x-model="departureDate" min="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-semibold text-slate-800">
                    </div>

                    {{-- Stepper Jumlah Seat / Penumpang --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Jumlah Kursi / Penumpang *</label>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="if(pax > 1) pax--" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-lg flex items-center justify-center transition">-</button>
                            <span class="w-12 text-center text-lg font-black text-slate-900" x-text="pax"></span>
                            <button type="button" @click="if(pax < 30) pax++" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-lg flex items-center justify-center transition">+</button>
                            <span class="text-xs text-slate-500 font-bold">Kursi (Pax)</span>
                        </div>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4 space-y-1">
                        <div class="flex items-center justify-between text-xs text-slate-600 font-semibold">
                            <span>Harga per kursi:</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-600 font-semibold">
                            <span>Jumlah penumpang:</span>
                            <span class="font-bold text-slate-900" x-text="pax + ' orang'"></span>
                        </div>
                        <div class="border-t border-blue-200/60 pt-2 mt-2 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-800">Total Pembayaran:</span>
                            <span class="text-base font-black text-[#ea580c]" x-text="'Rp ' + (pax * pricePerPax).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                {{-- Action Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="isBookingModalOpen = false" class="px-5 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 text-xs transition">
                        Batal
                    </button>
                    <button type="button"
                            @click="submitBooking()"
                            :disabled="isSubmittingBooking"
                            class="px-6 py-3 rounded-xl font-black text-white text-xs shadow-md hover:brightness-105 transition flex items-center gap-2"
                            style="background: #f97316;">
                        <svg x-show="isSubmittingBooking" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="isSubmittingBooking ? 'Memproses Pesanan...' : 'Lanjutkan ke Pembayaran'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- LIGHTBOX MODAL PREVIEW FOTO --}}
    <div x-show="lightboxOpen"
         x-cloak
         class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="lightboxOpen = false"
         @keydown.arrow-right.window="nextPhoto()"
         @keydown.arrow-left.window="prevPhoto()">
        
        <button type="button"
                @click="lightboxOpen = false"
                class="absolute top-6 right-6 text-white text-2xl w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <button type="button"
                @click="prevPhoto()"
                class="absolute left-6 text-white text-2xl w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="max-w-4xl max-h-[85vh] flex flex-col items-center justify-center">
            <template x-if="photos.length > 0">
                <img :src="photos[lightboxIndex].src" :alt="photos[lightboxIndex].alt" class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl">
            </template>
            <div class="mt-4 text-white text-xs font-bold bg-black/40 px-3 py-1 rounded-full">
                Foto <span x-text="lightboxIndex + 1"></span> dari <span x-text="photos.length"></span>
            </div>
        </div>

        <button type="button"
                @click="nextPhoto()"
                class="absolute right-6 text-white text-2xl w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

</main>
@endsection
