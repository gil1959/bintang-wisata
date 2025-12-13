@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">General Settings</h1>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" enctype="multipart/form-data" class="bg-white border rounded-2xl p-6">
    @csrf

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-2">Hero Title</label>
      <input name="hero_title" class="w-full rounded-xl border-slate-200"
        value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" />
    </div>

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-2">Hero Subtitle</label>
      <textarea name="hero_subtitle" rows="3" class="w-full rounded-xl border-slate-200">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-semibold mb-2">Hero Image</label>
      <input type="file" name="hero_image" class="w-full" />
      @if(!empty($settings['hero_image']))
        <div class="mt-3">
          <img src="{{ $settings['hero_image'] }}" class="h-32 rounded-2xl object-cover border" alt="Hero" />
        </div>
      @endif
    </div>

    <button class="btn btn-primary" type="submit">Simpan</button>
  </form>
</div>
@endsection
