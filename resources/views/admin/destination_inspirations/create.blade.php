@extends('layouts.admin')

@section('title', 'Tambah Inspirasi Destinasi')
@section('page-title', 'Tambah Inspirasi Destinasi')

@section('content')
<div class="card p-5 max-w-2xl">
  <form method="POST" action="{{ route('admin.destination-inspirations.store') }}" enctype="multipart/form-data" class="space-y-4">
  @csrf

  <div>
    <label class="block text-sm font-bold mb-1">Judul</label>
    <input name="title" class="w-full border rounded-xl px-3 py-2" value="{{ old('title') }}" required>
  </div>

  <div>
    <label class="block text-sm font-bold mb-1">Foto</label>
    <input type="file" name="image" class="w-full border rounded-xl px-3 py-2" accept="image/*" required>
    <div class="text-xs text-slate-500 mt-1">Rekomendasi: 900x600 / landscape.</div>
  </div>

  <div>
    <label class="block text-sm font-bold mb-1">Kategori Tour (untuk link)</label>
    <select name="tour_category_id" class="w-full border rounded-xl px-3 py-2">
      <option value="">Semua Paket</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(old('tour_category_id') == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-bold mb-1">Urutan</label>
      <input type="number" name="sort_order" class="w-full border rounded-xl px-3 py-2" value="{{ old('sort_order', 0) }}" min="0">
    </div>

    <div class="flex items-center gap-2 mt-6">
      <input type="checkbox" name="is_active" value="1" class="h-4 w-4" @checked(old('is_active', true))>
      <label class="text-sm font-bold">Aktif</label>
    </div>
  </div>

  <button class="rounded-xl px-4 py-2 font-extrabold text-white" style="background:#0194F3;">Simpan</button>
</form>

</div>
@endsection
