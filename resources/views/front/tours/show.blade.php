@extends('layouts.front')

@section('title', $tourPackage->title)

@section('content')
<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Breadcrumb / judul --}}
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    {{ $tourPackage->category === 'domestic' ? 'Domestik (WNI)' : 'Internasional' }}
                    @if($tourPackage->destination)
                        • {{ $tourPackage->destination }}
                    @endif
                </p>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">
                    {{ $tourPackage->title }}
                </h1>
                @if($tourPackage->duration_text)
                    <p class="text-sm text-gray-500 mt-1">
                        Durasi: {{ $tourPackage->duration_text }}
                    </p>
                @endif
            </div>

            @if($tourPackage->include_flight_option)
                <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">
                    Opsi dengan tiket pesawat tersedia
                </div>
            @endif
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Kolom kiri: detail + harga --}}
            <div class="md:col-span-2 space-y-6">

                {{-- Deskripsi singkat --}}
                @if($tourPackage->short_description)
                    <div class="bg-white shadow rounded-lg p-5">
                        <h2 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan Paket</h2>
                        <p class="text-sm text-gray-600">
                            {{ $tourPackage->short_description }}
                        </p>
                    </div>
                @endif

                {{-- Harga bertingkat DOMESTIK / WNA --}}
                <div class="space-y-4">

                    {{-- Harga Domestik --}}
                    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-semibold text-emerald-700 tracking-wide uppercase">
                                    Harga Bertingkat (Domestik / WNI)
                                </p>
                                <p class="text-xs text-emerald-600">
                                    Per orang, berdasarkan jumlah peserta.
                                </p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-4 gap-3 text-sm">
                            @foreach($tourPackage->priceTiers->where('audience_type', 'domestic') as $tier)
                                <div class="bg-white rounded-md border border-emerald-100 px-3 py-2">
                                    <p class="text-xs text-gray-500">
                                        {{ $tier->min_pax }}–{{ $tier->max_pax }} Pax
                                    </p>
                                    <p class="mt-1 font-semibold text-emerald-700">
                                        Rp {{ number_format($tier->price_per_pax, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endforeach

                            @if($tourPackage->priceTiers->where('audience_type', 'domestic')->isEmpty())
                                <p class="text-xs text-gray-500">
                                    Belum ada data harga domestik. Harga akan dicek manual oleh admin.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Harga WNA --}}
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs font-semibold text-orange-700 tracking-wide uppercase">
                                    Harga Bertingkat (Asing / WNA)
                                </p>
                                <p class="text-xs text-orange-600">
                                    Per orang, berdasarkan jumlah peserta.
                                </p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-4 gap-3 text-sm">
                            @foreach($tourPackage->priceTiers->where('audience_type', 'wna') as $tier)
                                <div class="bg-white rounded-md border border-orange-100 px-3 py-2">
                                    <p class="text-xs text-gray-500">
                                        {{ $tier->min_pax }}–{{ $tier->max_pax }} Pax
                                    </p>
                                    <p class="mt-1 font-semibold text-orange-700">
                                        Rp {{ number_format($tier->price_per_pax, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endforeach

                            @if($tourPackage->priceTiers->where('audience_type', 'wna')->isEmpty())
                                <p class="text-xs text-gray-500">
                                    Belum ada data harga WNA. Harga akan dicek manual oleh admin.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Info tiket pesawat --}}
                    <div class="bg-white shadow rounded-lg p-4 text-xs text-gray-600">
                        <p class="font-semibold mb-1">Info tiket pesawat</p>
                        @if($tourPackage->flight_surcharge_per_pax ?? false)
                            <p>
                                Tambahan harga tiket pesawat:
                                <span class="font-semibold">
                                    Rp {{ number_format($tourPackage->flight_surcharge_per_pax, 0, ',', '.') }}
                                    / orang
                                </span>.
                                Centang opsi "dengan tiket pesawat" di form bila ingin sekalian.
                            </p>
                        @else
                            <p>
                                Opsi tiket pesawat tersedia, namun harga dapat berubah.
                                Jika perlu, centang "dengan tiket pesawat" – admin bisa konfirmasi ulang.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Kolom kanan: Form Booking --}}
            <div>
                <div class="bg-white shadow rounded-lg p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">
                        Form Pemesanan
                    </h2>

                    <form method="POST" action="{{ route('tour.book', $tourPackage) }}" class="space-y-3">
                        @csrf

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Nama Lengkap
                            </label>
                            <input type="text" name="customer_name"
                                   class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Email
                            </label>
                            <input type="email" name="customer_email"
                                   class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   required>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                No. WhatsApp
                            </label>
                            <input type="text" name="customer_phone"
                                   class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Contoh: 6281234567890"
                                   required>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Jumlah Peserta
                                </label>
                                <input type="number" name="pax" min="1" value="2"
                                       class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                       required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                    Tipe Peserta
                                </label>
                                <select name="audience_type"
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="domestic">Domestik (WNI)</option>
                                    <option value="wna">Asing / WNA</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Tanggal Berangkat
                            </label>
                            <input type="date" name="start_date"
                                   class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                   required>
                        </div>

                        <div class="flex items-start gap-2 mt-1">
                            <input type="checkbox" name="with_flight" value="1"
                                   class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-gray-700">
                                Saya ingin paket <span class="font-semibold">dengan tiket pesawat</span>.
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                Catatan Tambahan
                            </label>
                            <textarea name="notes" rows="3"
                                      class="w-full border rounded-md px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500"
                                      placeholder="Contoh: jadwal prefer, jumlah anak, permintaan khusus, dll."></textarea>
                        </div>

                        <button type="submit"
                                class="w-full mt-2 inline-flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Pesan Sekarang
                        </button>

                        <p class="mt-1 text-[11px] text-gray-500">
                            Setelah klik <strong>“Pesan Sekarang”</strong>, sistem akan membuat data pesanan
                            dan mengarahkan Anda ke halaman instruksi pembayaran.
                        </p>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
