@extends('layouts.admin')

@section('title', 'Tambah Dokumentasi')
@section('page-title', 'Tambah Dokumentasi')

@section('content')
<div class="max-w-3xl space-y-5">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                 style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="upload" class="w-5 h-5" style="color:#0194F3;"></i>
            </div>
            <div>
                <div class="font-extrabold text-slate-900">Tambah Dokumentasi</div>
                <div class="text-xs text-slate-500">Upload foto/video, atur judul, status, dan urutan.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.documentations.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Tipe</label>
                    <select name="type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                            required>
                        <option value="photo" @selected(old('type')==='photo')>Foto</option>
                        <option value="video" @selected(old('type')==='video')>Video</option>
                    </select>
                    <div class="mt-1 text-xs text-slate-500">Pilih tipe sesuai file.</div>
                </div>

                <div class="sm:col-span-8">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Judul (opsional)</label>
                    <input name="title"
                           value="{{ old('title') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="Contoh: Dokumentasi Tour Bali">
                </div>

                <div class="sm:col-span-12">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Upload File (bisa banyak)</label>
                    <input type="file"
                           name="files[]"
                           multiple
                           required
                           accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <div class="mt-1 text-xs text-slate-500">
                        Foto: JPG/PNG/WEBP. Video: MP4/WEBM/OGG. Maks 50MB per file.
                    </div>
                </div>

                <div class="sm:col-span-6">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Urutan (opsional)</label>
                    <input type="number"
                           name="sort_order"
                           value="{{ old('sort_order') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="Contoh: 1">
                    <div class="mt-1 text-xs text-slate-500">Semakin kecil, semakin atas.</div>
                </div>

                <div class="sm:col-span-6 flex items-center gap-3 pt-6">
                    <input id="is_active"
                           type="checkbox"
                           name="is_active"
                           value="1"
                           class="h-5 w-5 rounded border-slate-300"
                           @checked(old('is_active', true))>
                    <label for="is_active" class="text-sm font-extrabold text-slate-800">
                        Tampilkan di frontend
                    </label>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('admin.documentations.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                    Batal
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
