@extends('layouts.admin')

@section('title', 'Paket Wisata')
@section('page-title', 'Paket Wisata')

@section('content')

<div class="flex justify-between mb-4">
    <h2 class="text-xl font-semibold">Daftar Paket Wisata</h2>

    <a href="{{ route('admin.tour-packages.create') }}"
       class="px-4 py-2 bg-[#0194F3] text-white rounded hover:bg-blue-600">
        + Tambah Paket
    </a>
</div>

<div class="bg-white p-4 rounded shadow">
    <table class="w-full text-left">
        <thead>
        <tr class="border-b">
            <th class="py-2">Judul</th>
            <th class="py-2">Kategori</th>
            <th class="py-2">Durasi</th>
            <th class="py-2">Status</th>
            <th class="py-2 text-right">Aksi</th>
        </tr>
        </thead>

        <tbody>
        @foreach($packages as $p)
            <tr class="border-b">
                <td class="py-2">{{ $p->title }}</td>
                <td class="py-2">{{ $p->category?->name ?? '-' }}</td>
                <td class="py-2">{{ $p->duration_text }}</td>
                <td class="py-2">
                    @if($p->is_active)
                        <span class="text-green-600 font-medium">Aktif</span>
                    @else
                        <span class="text-red-600 font-medium">Nonaktif</span>
                    @endif
                </td>

                <td class="py-2 text-right">
                    <a href="{{ route('admin.tour-packages.edit', $p->id) }}"
                       class="px-3 py-1 text-sm bg-yellow-400 text-black rounded hover:bg-yellow-500">
                        Edit
                    </a>

                    <form action="{{ route('admin.tour-packages.destroy', $p->id) }}"
                          method="POST" class="inline-block"
                          onsubmit="return confirm('Yakin hapus paket ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
