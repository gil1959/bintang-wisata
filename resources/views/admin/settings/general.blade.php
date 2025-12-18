@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-6">General Settings</h1>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
      {{ session('success') }}
    </div>
  @endif

 <form method="POST" action="{{ route('admin.settings.general.save') }}" enctype="multipart/form-data" class="bg-white border rounded-2xl p-6">

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

    <hr class="my-8">

<h2 class="text-lg font-semibold mb-4">Footer - Kontak</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Alamat</label>
        <textarea name="footer_address" class="w-full border rounded-lg px-3 py-2" rows="3">{{ old('footer_address', $settings['footer_address'] ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Telepon</label>
        <input type="text" name="footer_phone" class="w-full border rounded-lg px-3 py-2"
               value="{{ old('footer_phone', $settings['footer_phone'] ?? '') }}">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="footer_email" class="w-full border rounded-lg px-3 py-2"
               value="{{ old('footer_email', $settings['footer_email'] ?? '') }}">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">WhatsApp (format: 628xxxx)</label>
        <input type="text" name="footer_whatsapp" class="w-full border rounded-lg px-3 py-2"
               value="{{ old('footer_whatsapp', $settings['footer_whatsapp'] ?? '') }}">
        <p class="text-xs text-gray-500 mt-1">Tanpa tanda +, tanpa spasi.</p>
    </div>
    <h2 class="text-lg font-semibold mb-4">Email Notifikasi</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="md:col-span-2">
   <label class="block text-sm font-medium mb-1">Email Admin (kirim invoice)</label>
    <input type="email" name="invoice_admin_email" class="w-full border rounded-lg px-3 py-2"
value="{{ old('invoice_admin_email', $settings['invoice_admin_email'] ?? '') }}">
    <p class="text-xs text-gray-500 mt-1">Kalau kosong, sistem akan pakai email di Footer.</p>
  </div>
</div>
<hr class="my-8">

<h2 class="text-lg font-semibold mb-4">About Page</h2>

<div class="mb-4">
  <label class="block text-sm font-semibold mb-2">Meta Title</label>
  <input name="about_meta_title" class="w-full rounded-xl border-slate-200"
    value="{{ old('about_meta_title', $settings['about_meta_title'] ?? '') }}" />
  @error('about_meta_title') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
</div>

{{-- HERO ABOUT --}}
<div class="grid grid-cols-1 gap-4">
  <div>
    <label class="block text-sm font-semibold mb-2">Hero Badge</label>
    <input name="about_hero_badge" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_hero_badge', $settings['about_hero_badge'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Hero Title</label>
    <input name="about_hero_title" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_hero_title', $settings['about_hero_title'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Hero Description</label>
    <textarea name="about_hero_desc" rows="4" class="w-full rounded-xl border-slate-200">{{ old('about_hero_desc', $settings['about_hero_desc'] ?? '') }}</textarea>
  </div>
</div>

<hr class="my-8">

{{-- NILAI KAMI --}}
<h3 class="text-base font-semibold mb-3">Section: Nilai Kami</h3>

<div class="grid grid-cols-1 gap-4">
  <div>
    <label class="block text-sm font-semibold mb-2">Label</label>
    <input name="about_values_label" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_values_label', $settings['about_values_label'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Title</label>
    <input name="about_values_title" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_values_title', $settings['about_values_title'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Description</label>
    <textarea name="about_values_desc" rows="3" class="w-full rounded-xl border-slate-200">{{ old('about_values_desc', $settings['about_values_desc'] ?? '') }}</textarea>
  </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
  @for($i=1;$i<=4;$i++)
    <div class="border rounded-2xl p-4">
      <div class="text-sm font-semibold mb-3">Value Card {{ $i }}</div>
      <label class="block text-xs font-semibold mb-1">Title</label>
      <input name="about_value{{ $i }}_title" class="w-full rounded-xl border-slate-200"
        value="{{ old('about_value'.$i.'_title', $settings['about_value'.$i.'_title'] ?? '') }}" />

      <label class="block text-xs font-semibold mt-3 mb-1">Description</label>
      <textarea name="about_value{{ $i }}_desc" rows="2" class="w-full rounded-xl border-slate-200">{{ old('about_value'.$i.'_desc', $settings['about_value'.$i.'_desc'] ?? '') }}</textarea>
    </div>
  @endfor
</div>

<hr class="my-8">

{{-- ALUR LAYANAN --}}
<h3 class="text-base font-semibold mb-3">Section: Alur Layanan</h3>

<div class="grid grid-cols-1 gap-4">
  <div>
    <label class="block text-sm font-semibold mb-2">Label</label>
    <input name="about_flow_label" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_flow_label', $settings['about_flow_label'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Title</label>
    <input name="about_flow_title" class="w-full rounded-xl border-slate-200"
      value="{{ old('about_flow_title', $settings['about_flow_title'] ?? '') }}" />
  </div>

  <div>
    <label class="block text-sm font-semibold mb-2">Description</label>
    <textarea name="about_flow_desc" rows="3" class="w-full rounded-xl border-slate-200">{{ old('about_flow_desc', $settings['about_flow_desc'] ?? '') }}</textarea>
  </div>
</div>

<div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
  @for($i=1;$i<=4;$i++)
    <div class="border rounded-2xl p-4">
      <div class="text-sm font-semibold mb-3">Step {{ $i }}</div>
      <label class="block text-xs font-semibold mb-1">Title</label>
      <input name="about_step{{ $i }}_title" class="w-full rounded-xl border-slate-200"
        value="{{ old('about_step'.$i.'_title', $settings['about_step'.$i.'_title'] ?? '') }}" />

      <label class="block text-xs font-semibold mt-3 mb-1">Description</label>
      <textarea name="about_step{{ $i }}_desc" rows="2" class="w-full rounded-xl border-slate-200">{{ old('about_step'.$i.'_desc', $settings['about_step'.$i.'_desc'] ?? '') }}</textarea>
    </div>
  @endfor
</div>

</div>



    <button class="btn btn-primary" type="submit">Simpan</button>
  </form>
</div>
@endsection
