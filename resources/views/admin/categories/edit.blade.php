@extends('layouts.admin')

@section('page-title', 'Edit Kategori')

@section('content')

<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white p-5 shadow rounded">
    @csrf
    @method('PUT')

    <label class="block mb-1">Nama Kategori</label>
    <input type="text" name="name" class="border px-3 py-2 rounded w-full" value="{{ $category->name }}" required>

    <label class="block mt-4 mb-1">Slug</label>
    <input type="text" name="slug" class="border px-3 py-2 rounded w-full" value="{{ $category->slug }}" required>

    <div class="mt-4">
        <button class="px-4 py-2 bg-[#0194F3] text-white rounded">Simpan Perubahan</button>
    </div>
</form>

@endsection