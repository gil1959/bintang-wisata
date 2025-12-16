@csrf

@if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
        <div class="font-extrabold text-red-700 mb-2">Ada error validasi:</div>
        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-4">

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Judul</label>
        <input type="text"
               name="title"
               value="{{ old('title', $article->title ?? '') }}"
               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
               required>
    </div>

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Ringkasan</label>
        <textarea name="excerpt"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                  rows="3">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
        <div class="mt-1 text-xs text-slate-500">Opsional, tampil di listing/preview.</div>
    </div>

    <div>
        <label class="block text-sm font-extrabold text-slate-800 mb-1">Konten</label>
        <textarea name="content"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                  rows="10"
                  required>{{ old('content', $article->content ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-start">
        <div class="sm:col-span-7">
            <label class="block text-sm font-extrabold text-slate-800 mb-1">Cover</label>
            <input type="file"
                   name="cover_image"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <div class="mt-1 text-xs text-slate-500">Opsional. Gunakan JPG/PNG/WEBP.</div>
        </div>

        <div class="sm:col-span-5 flex items-center gap-3 pt-6">
    <input id="is_published"
           type="checkbox"
           name="is_published"
           value="1"
           class="h-5 w-5 rounded border-slate-300"
           {{ old('is_published', $article->is_published ?? false) ? 'checked' : '' }}>
    <label for="is_published" class="text-sm font-extrabold text-slate-800">
        Publish
    </label>
</div>

    </div>

    <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-end">
        <a href="{{ route('admin.articles.index') }}"
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
            Simpan
        </button>
    </div>

</div>
