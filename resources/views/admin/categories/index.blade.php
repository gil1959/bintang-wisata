@extends('layouts.admin')

@section('page-title', 'Kategori Tour')

@section('content')

<div class="flex justify-between mb-4">
    <h2 class="text-lg font-semibold">Kategori Tour</h2>
    <a href="{{ route('admin.categories.create') }}"
       class="px-4 py-2 bg-[#0194F3] text-white rounded">
       + Tambah Kategori
    </a>
</div>

<div class="bg-white p-4 rounded shadow">
    <table class="w-full">
        <thead>
            <tr class="border-b">
                <th class="py-2 text-left">Nama</th>
                <th class="py-2 text-left">Slug</th>
                <th class="py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $c)
                <tr class="border-b">
                    <td class="py-2">{{ $c->name }}</td>
                    <td>{{ $c->slug }}</td>
                    <td class="text-right">
                        <a href="{{ route('admin.categories.edit', $c) }}"
                           class="text-blue-500">Edit</a>

                        <form action="{{ route('admin.categories.destroy', $c) }}"
                              method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus kategori?')"
                                    class="text-red-500">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection