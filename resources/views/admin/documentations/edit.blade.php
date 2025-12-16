@extends('layouts.admin')

@section('title', 'Edit Dokumentasi')
@section('page-title', 'Edit Dokumentasi')

@section('content')
<div class="max-w-3xl space-y-5">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                 style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                <i data-lucide="pencil" class="w-5 h-5" style="color:#0194F3;"></i>
            </div>
            <div>
                <div class="font-extrabold text-slate-900">Edit Dokumentasi</div>
                <div class="text-xs text-slate-500">Ubah judul, status, urutan, atau ganti file.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.documentations.update', $documentation) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="sm:col-span-8">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Judul</label>
                    <input name="title"
                           value="{{ old('title', $documentation->title) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                           placeholder="Judul dokumentasi">
                </div>

                <div class="sm:col-span-4">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Urutan</label>
                    <input type="number"
                           name="sort_order"
                           value="{{ old('sort_order', $documentation->sort_order) }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                </div>

                <div class="sm:col-span-12 flex items-center gap-3">
                    <input id="is_active"
                           type="checkbox"
                           name="is_active"
                           value="1"
                           class="h-5 w-5 rounded border-slate-300"
                           @checked(old('is_active', $documentation->is_active))>
                    <label for="is_active" class="text-sm font-extrabold text-slate-800">
                        Tampilkan di frontend
                    </label>
                </div>

                <div class="sm:col-span-12">
                    <label class="block text-sm font-extrabold text-slate-800 mb-1">Ganti file (opsional)</label>
                    <input type="file"
                           name="replace_file"
                           accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/ogg"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">

                    <div class="mt-2 text-xs text-slate-500">
                        Format: JPG/PNG/WEBP atau MP4/WEBM/OGG.
                    </div>
                </div>

                {{-- Preview current --}}
                <div class="sm:col-span-12">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-xs font-extrabold text-slate-600">Preview saat ini</div>
                        <div class="mt-2">
                            @if($documentation->type === 'photo')
                                <img src="{{ $documentation->url }}"
                                     alt="{{ $documentation->title ?? 'Dokumentasi' }}"
                                     class="max-w-full h-auto rounded-xl border border-slate-200 object-cover bg-white">
                            @else
                                <a href="{{ $documentation->url }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="external-link" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Buka Video
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="{{ route('admin.documentations.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                    Kembali
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#0194F3;"
                        onmouseover="this.style.background='#0186DB'"
                        onmouseout="this.style.background='#0194F3'">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
