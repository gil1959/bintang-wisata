@extends('layouts.admin')

@section('title', 'Edit Inspirasi Destinasi')
@section('page-title', 'Edit Inspirasi Destinasi')

@section('content')
<div class="card p-5 max-w-2xl">
  <form method="POST" action="{{ route('admin.destination-inspirations.update', $item) }}" enctype="multipart/form-data" class="space-y-4">
  @csrf
  @method('PUT')

  <div>
    <label class="block text-sm font-bold mb-1">Judul</label>
    <input name="title" class="w-full border rounded-xl px-3 py-2" value="{{ old('title', $item->title) }}" required>
  </div>

  <div>
    <label class="block text-sm font-bold mb-1">Foto (kosongkan kalau tidak ganti)</label>
    <input type="file" name="image" class="w-full border rounded-xl px-3 py-2" accept="image/*">
    @if($item->image_path)
      <img src="{{ asset('storage/'.$item->image_path) }}" class="mt-2 rounded-xl border max-w-sm">
    @endif
  </div>

  <div>
    <label class="block text-sm font-bold mb-1">Kategori Tour (untuk link)</label>
    <select name="tour_category_id" class="w-full border rounded-xl px-3 py-2">
      <option value="">Semua Paket</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(old('tour_category_id', $item->tour_category_id) == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>

  <button class="rounded-xl px-4 py-2 font-extrabold text-white" style="background:#0194F3;">Update</button>
</form>

</div>
@endsection
