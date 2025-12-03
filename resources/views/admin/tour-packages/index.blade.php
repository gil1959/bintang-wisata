@extends('layouts.admin')

@section('title', 'Paket Wisata')
@section('page-title', 'Paket Wisata')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Cari paket / destinasi..."
                   class="border rounded px-3 py-1 text-sm">
            <button class="px-3 py-1 rounded bg-emerald-500 text-white text-sm">
                Cari
            </button>
        </form>

       <a href="{{ route('admin.tour-packages.create') }}"
   class="inline-block px-4 py-2 rounded bg-emerald-500 text-white text-sm">
    + Tambah Paket
</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b bg-gray-50">
                <th class="px-4 py-2 text-left">Paket</th>
                <th class="px-4 py-2 text-left">Kategori</th>
                <th class="px-4 py-2 text-left">Destinasi</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($packages as $package)
                <tr class="border-b last:border-0">
                    <td class="px-4 py-2">
                        <div class="font-semibold">{{ $package->title }}</div>
                        <div class="text-xs text-gray-500">{{ $package->duration_text }}</div>
                    </td>
                    <td class="px-4 py-2">
                        {{ $package->category === 'domestic' ? 'Domestik' : 'Internasional' }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $package->destination ?? '-' }}
                    </td>
                    <td class="px-4 py-2">
                        @if($package->is_active)
                            <span class="text-xs px-2 py-1 rounded bg-emerald-50 text-emerald-700">
                                Aktif
                            </span>
                        @else
                            <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('admin.tour-packages.edit', $package) }}"
                           class="text-xs px-3 py-1 rounded bg-blue-500 text-white">
                            Edit
                        </a>

                        <form action="{{ route('admin.tour-packages.destroy', $package) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('Hapus paket ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs px-3 py-1 rounded bg-red-500 text-white">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                        Belum ada paket wisata.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $packages->withQueryString()->links() }}
        </div>
    </div>
@endsection
