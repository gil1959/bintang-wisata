@extends('layouts.admin')

@section('page-title', 'Tambah Kategori')

@section('content')

<form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white p-5 shadow rounded">
    @csrf

    <label class="block mb-1">Nama Kategori</label>
    <input type="text" name="name" class="border px-3 py-2 rounded w-full" required>

    <label class="block mt-4 mb-1">Slug</label>
    <input type="text" name="slug" class="border px-3 py-2 rounded w-full" required>

    <div class="mt-4">
        <button class="px-4 py-2 bg-[#0194F3] text-white rounded">Simpan</button>
    </div>
</form>

@endsection