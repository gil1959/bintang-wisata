@php
  $pkg = $package ?? null;
@endphp

{{-- INFORMASI DASAR --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Informasi Dasar Paket</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Judul Paket</label>
                <input type="text" name="title"
                       value="{{ old('title', $pkg->title ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>
            <div>
  <label class="block text-sm font-bold text-slate-800 mb-1">Label (opsional)</label>
  <input
    type="text"
    name="label"
    value="{{ old('label', $pkg?->label) }}"
    placeholder="Contoh: PROMO, DISKON, TERLARIS"
    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
  >
  <p class="mt-1 text-xs text-slate-500">Maks 30 karakter. Kosongkan jika tidak perlu.</p>
  @error('label') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
</div>


            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Slug (URL)</label>
                <input type="text" name="slug"
                       value="{{ old('slug', $pkg->slug ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       required>
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Durasi</label>
                <input type="text" name="duration_text"
                       value="{{ old('duration_text', $pkg->duration_text ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                       placeholder="contoh: 3D2N">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Destinasi</label>
                <input type="text" name="destination"
                       value="{{ old('destination', $pkg->destination ?? '') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-800 mb-1">Kategori</label>
                <select name="category_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $pkg->category_id ?? '') == $cat->id ? 'selected':'' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

{{-- FOTO --}}
<div x-data="{ open: true }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Foto Paket Wisata</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5 space-y-4">

        @if(!empty($pkg?->thumbnail_path))
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="text-sm font-extrabold text-slate-900">Thumbnail Saat Ini</div>
                <img src="{{ asset('storage/' . $pkg->thumbnail_path) }}"
                     class="mt-3 h-28 w-auto rounded-xl object-cover border border-slate-200">
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Upload Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <div class="text-xs text-slate-500 mt-1">PNG/JPG/WEBP disarankan.</div>
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-800 mb-1">Tambah Gallery (multi upload)</label>
                <input type="file" name="gallery[]" accept="image/*" multiple
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                <div class="text-xs text-slate-500 mt-1">Boleh lebih dari 1 foto.</div>
            </div>
        </div>

        {{-- Gallery existing (edit only) --}}
        @if($pkg && method_exists($pkg, 'photos') && $pkg->photos->count())
            <div class="pt-2">
                <div class="text-sm font-extrabold text-slate-900 mb-3">Galeri Foto</div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($pkg->photos as $photo)
                        <div class="rounded-2xl border border-slate-200 bg-white p-3">
                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                 class="h-24 w-full object-cover rounded-xl border border-slate-200">

                            <div class="mt-3">
    <button type="button"
            class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
            style="background:#ef4444"
            onmouseover="this.style.background='#dc2626'"
            onmouseout="this.style.background='#ef4444'"
            onclick="window.__bwDeletePhoto('{{ route('admin.tour-packages.delete-photo', $photo->id) }}')">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Hapus
    </button>
</div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- DESKRIPSI --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Deskripsi Paket</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <label class="block text-sm font-bold text-slate-800 mb-1">Deskripsi Lengkap</label>
        <textarea name="long_description" rows="7"
                  class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">{{ old('long_description', $pkg->long_description ?? '') }}</textarea>
    </div>
</div>

{{-- ITINERARY --}}
@php
    // NORMALISASI DATA ITINERARY (EDIT: HH:MM:SS -> HH:MM)
    $itinerariesForForm = old('itineraries') ?? collect($pkg->itineraries ?? [])
        ->map(function ($i) {
            return [
             'id'    => $i->id,
             'title' => $i->title ?? '',
            ];

        })
        ->values()
        ->toArray();
@endphp

<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Itinerary Perjalanan</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <div x-data='{ items: @json($itinerariesForForm) }' class="space-y-3">

            <template x-for="(row, index) in items" :key="(row.id ?? index)">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                        {{-- TITLE --}}
                        <div class="sm:col-span-8">
                            <label class="block text-sm font-bold text-slate-800 mb-1">Judul</label>
                            <input type="text"
                                   x-model="row.title"
                                   :name="`itineraries[${index}][title]`"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                                   placeholder="Contoh: Check-in hotel, makan malam, dll">
                        </div>
                        

                        {{-- DELETE --}}
                        <div class="sm:col-span-1 flex sm:items-end">
                            <button type="button"
                                    @click="items.splice(index, 1)"
                                    class="w-full inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-xs font-extrabold text-white transition"
                                    style="background:#ef4444"
                                    onmouseover="this.style.background='#dc2626'"
                                    onmouseout="this.style.background='#ef4444'">
                                X
                            </button>
                        </div>

                        {{-- HIDDEN ID (PENTING BUAT EDIT) --}}
                        <input type="hidden"
                               x-model="row.id"
                               :name="`itineraries[${index}][id]`">
                    </div>
                </div>
            </template>

            {{-- ADD --}}
            <button type="button"
                    @click="items.push({ id: null, time: '', title: '' })"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                    style="background:#16a34a"
                    onmouseover="this.style.background='#15803d'"
                    onmouseout="this.style.background='#16a34a'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Itinerary
            </button>

        </div>
    </div>
</div>



{{-- INCLUDE / EXCLUDE --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Include & Exclude</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Includes --}}
            <div x-data='{ list: @json(old("includes", $pkg->includes ?? [])) }' class="space-y-3">
                <div class="font-extrabold text-slate-900">Includes</div>

                <template x-for="(item, index) in list" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="list[index]"
                               name="includes[]"
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                               placeholder="Contoh: Hotel, Makan, Transport">

                        <button type="button"
                                @click="list.splice(index, 1)"
                                class="inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-xs font-extrabold text-white transition"
                                style="background:#ef4444"
                                onmouseover="this.style.background='#dc2626'"
                                onmouseout="this.style.background='#ef4444'">
                            X
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="list.push('')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#16a34a"
                        onmouseover="this.style.background='#15803d'"
                        onmouseout="this.style.background='#16a34a'">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Include
                </button>
            </div>

            {{-- Excludes --}}
            <div x-data='{ list: @json(old("excludes", $pkg->excludes ?? [])) }' class="space-y-3">
                <div class="font-extrabold text-slate-900">Excludes</div>

                <template x-for="(item, index) in list" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text"
                               x-model="list[index]"
                               name="excludes[]"
                               class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                               placeholder="Contoh: Tiket pesawat, Tips guide">

                        <button type="button"
                                @click="list.splice(index, 1)"
                                class="inline-flex items-center justify-center rounded-xl px-3 py-2.5 text-xs font-extrabold text-white transition"
                                style="background:#ef4444"
                                onmouseover="this.style.background='#dc2626'"
                                onmouseout="this.style.background='#ef4444'">
                            X
                        </button>
                    </div>
                </template>

                <button type="button"
                        @click="list.push('')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                        style="background:#16a34a"
                        onmouseover="this.style.background='#15803d'"
                        onmouseout="this.style.background='#16a34a'">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Exclude
                </button>
            </div>

        </div>
    </div>
</div>

{{-- TIERS --}}
@include('admin.tour-packages._tier_section', [
    'type' => 'domestic',
    'label' => 'Harga Domestik',
    'package' => $pkg
])

@include('admin.tour-packages._tier_section', [
    'type' => 'international',
    'label' => 'Harga WNA',
    'package' => $pkg
])

{{-- FLIGHT INFO --}}
<div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <button type="button"
        @click="open = !open"
        class="w-full px-5 py-4 text-left font-extrabold text-white flex items-center justify-between"
        style="background:#0194F3;">
        <span>Pengaturan Tiket Pesawat</span>
        <span class="text-white/90 text-sm" x-text="open ? 'Tutup' : 'Buka'"></span>
    </button>

    <div x-show="open" x-cloak class="p-5">
        <label class="block text-sm font-bold text-slate-800 mb-1">Paket Termasuk Tiket Pesawat?</label>
        <select name="flight_info"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <option value="not_included"
                {{ old('flight_info', $pkg->flight_info ?? '') === 'not_included' ? 'selected':'' }}>
                Tidak termasuk tiket pesawat
            </option>
            <option value="included"
                {{ old('flight_info', $pkg->flight_info ?? '') === 'included' ? 'selected':'' }}>
                Termasuk tiket pesawat
            </option>
        </select>
    </div>
</div>
