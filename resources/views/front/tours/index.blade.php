{{-- resources/views/front/tours/index.blade.php --}}
@extends('layouts.front')
{{-- kalau kamu pakai layout lain, ganti di sini --}}

@section('title', 'Paket Wisata - Bintang Wisata')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-4">Paket Wisata</h1>

        {{-- form search sederhana --}}
        <form method="GET" class="mb-6 flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari paket / destinasi..."
                class="border rounded px-3 py-2 text-sm flex-1"
            >
            <button
                class="px-4 py-2 rounded bg-emerald-500 text-white text-sm"
            >
                Cari
            </button>
        </form>

        @if($packages->count() === 0)
            <p class="text-gray-500">Belum ada paket wisata tersedia.</p>
        @else
            <div class="grid gap-4 md:grid-cols-3">
                @foreach($packages as $package)
                    <a href="{{ route('tour.show', $package) }}"
                       class="border rounded-lg p-4 bg-white hover:shadow transition">
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $package->category === 'domestic' ? 'Domestik' : 'Internasional' }}
                            @if($package->destination)
                                • {{ $package->destination }}
                            @endif
                        </div>
                        <h2 class="font-semibold text-lg mb-1">
                            {{ $package->title }}
                        </h2>
                        @if($package->duration_text)
                            <div class="text-xs text-gray-500 mb-2">
                                Durasi: {{ $package->duration_text }}
                            </div>
                        @endif
                        @if($package->short_description)
                            <p class="text-sm text-gray-600 line-clamp-3">
                                {{ $package->short_description }}
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $packages->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
