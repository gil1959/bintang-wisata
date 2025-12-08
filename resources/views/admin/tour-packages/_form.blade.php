

{{-- ========================== --}}
{{-- SECTION: INFORMASI DASAR --}}
{{-- ========================== --}}
<div x-data="{ open: true }" class="mb-4 border rounded shadow bg-white">

    <button type="button"
        @click="open = !open"
        class="w-full px-4 py-3 text-left font-semibold bg-[#0194F3] text-white">
        Informasi Dasar Paket
    </button>

    <div x-show="open" class="p-4 space-y-4">

        <div>
            <label class="block font-medium mb-1">Judul Paket</label>
            <input type="text" name="title"
                value="{{ old('title', $package->title ?? '') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Slug (URL)</label>
            <input type="text" name="slug"
                value="{{ old('slug', $package->slug ?? '') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Durasi Paket</label>
            <input type="text" name="duration_text"
                value="{{ old('duration_text', $package->duration_text ?? '') }}"
                class="w-full border rounded px-3 py-2"
                placeholder="contoh: 3D2N">
        </div>

        <div>
            <label class="block font-medium mb-1">Destinasi</label>
            <input type="text" name="destination"
                value="{{ old('destination', $package->destination ?? '') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Kategori</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $package->category_id ?? '') == $cat->id ? 'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

{{-- ========================== --}}
{{-- SECTION: FOTO --}}
{{-- ========================== --}}
<div x-data="{ open: true }" class="mb-4 border rounded shadow bg-white">

    <button type="button"
        @click="open = !open"
        class="w-full px-4 py-3 bg-[#0194F3] text-white font-semibold text-left">
        Foto Paket Wisata
    </button>

    <div x-show="open" class="p-4 space-y-4">

        {{-- Thumbnail --}}
        <div>
            <label class="block font-medium mb-1">Thumbnail</label>
            <input type="file"
                name="thumbnail"
                accept="image/*"
                class="border rounded px-3 py-2 w-full">
        </div>

        {{-- Gallery --}}
        <div>
            <label class="block font-medium mb-1">Gallery Foto (multi upload)</label>
            <input type="file"
                name="gallery[]"
                accept="image/*"
                multiple
                class="border rounded px-3 py-2 w-full">
        </div>

    </div>
</div>


{{-- ========================== --}}
{{-- SECTION: DESKRIPSI --}}
{{-- ========================== --}}
<div x-data="{ open: false }" class="mb-4 border rounded shadow bg-white">

    <button type="button" 
        @click="open = !open"
        class="w-full px-4 py-3 text-left font-semibold bg-[#0194F3] text-white">
        Deskripsi Paket
    </button>

    <div x-show="open" class="p-4">
        <label class="block font-medium mb-1">Deskripsi Lengkap</label>
        <textarea name="long_description" rows="6"
            class="w-full border rounded px-3 py-2">{{ old('long_description', $package->long_description ?? '') }}</textarea>
    </div>
</div>

{{-- ========================== --}}
{{-- SECTION: ITINERARY --}}
{{-- ========================== --}}
<div x-data="{ open: false }" class="mb-4 border rounded shadow bg-white">

    <button type="button"
        @click="open = !open"
        class="w-full px-4 py-3 bg-[#0194F3] text-white font-semibold text-left">
        Itinerary Perjalanan
    </button>

    <div x-show="open" class="p-4">

        <div x-data='{
            "items": @json(old("itineraries", $package->itineraries ?? []))
        }'>

            <template x-for="(row, index) in items" :key="index">
                <div class="mb-3 p-3 border rounded bg-gray-50">

                    <div class="grid grid-cols-12 gap-2">

                        <div class="col-span-3">
                            <label class="text-sm">Waktu (HH:MM)</label>
                            <input type="time"
                                x-model="row.time"
                                :name="`itineraries[${index}][time]`"
                                class="border rounded px-2 py-1 w-full">
                        </div>

                        <div class="col-span-8">
                            <label class="text-sm">Judul</label>
                            <input type="text"
                                x-model="row.title"
                                :name="`itineraries[${index}][title]`"
                                class="border rounded px-2 py-1 w-full">
                        </div>

                        <div class="col-span-1 flex items-end">
                            <button type="button"
                                @click="items.splice(index, 1)"
                                class="px-3 py-1 bg-red-500 text-white rounded">
                                X
                            </button>
                        </div>

                    </div>
                </div>
            </template>

            <button type="button"
                @click="items.push({ time: '', title: '' })"
                class="px-4 py-2 bg-green-600 text-white rounded">
                + Tambah Itinerary
            </button>

        </div>
    </div>
</div>

{{-- ========================== --}}
{{-- SECTION: INCLUDE / EXCLUDE --}}
{{-- ========================== --}}
<div x-data="{ open: false }" class="mb-4 border rounded shadow bg-white">

    <button type="button"
        @click="open = !open"
        class="w-full px-4 py-3 bg-[#0194F3] text-white text-left font-semibold">
        Include & Exclude
    </button>

    <div x-show="open" class="p-4 grid grid-cols-2 gap-4">

        {{-- INCLUDES --}}
        <div x-data='{"list": @json(old("includes", $package->includes ?? [])) }'>
            <label class="font-medium">Includes</label>

            <template x-for="(item, index) in list" :key="index">
                <div class="flex gap-2 mb-2">

                    <input type="text"
                        x-model="item"
                        :name="`includes[${index}]`"
                        class="border rounded px-3 py-1 w-full">

                    <button type="button"
                        @click="list.splice(index,1)"
                        class="px-3 bg-red-500 text-white rounded">
                        X
                    </button>
                </div>
            </template>

            <button type="button"
                @click="list.push('')"
                class="px-3 py-1 bg-green-600 text-white rounded">
                + Add Include
            </button>
        </div>

        {{-- EXCLUDES --}}
        <div x-data='{"list": @json(old("excludes", $package->excludes ?? [])) }'>
            <label class="font-medium">Excludes</label>

            <template x-for="(item, index) in list" :key="index">
                <div class="flex gap-2 mb-2">

                    <input type="text"
                        x-model="item"
                        :name="`excludes[${index}]`"
                        class="border rounded px-3 py-1 w-full">

                    <button type="button"
                        @click="list.splice(index,1)"
                        class="px-3 bg-red-500 text-white rounded">
                        X
                    </button>
                </div>
            </template>

            <button type="button"
                @click="list.push('')"
                class="px-3 py-1 bg-green-600 text-white rounded">
                + Add Exclude
            </button>
        </div>

    </div>
</div>

{{-- ========================== --}}
{{-- SECTION: TIER --}}
{{-- ========================== --}}
@include('admin.tour-packages._tier_section', [
    'type' => 'domestic',
    'label' => 'Harga Domestik'
])

@include('admin.tour-packages._tier_section', [
    'type' => 'international',
    'label' => 'Harga WNA'
])

{{-- ========================== --}}
{{-- SECTION: FLIGHT INFO --}}
{{-- ========================== --}}
<div x-data="{ open: false }" class="mb-4 border rounded shadow bg-white">

    <button type="button" @click="open = !open"
        class="w-full px-4 py-3 bg-[#0194F3] text-white text-left font-semibold">
        Pengaturan Tiket Pesawat
    </button>

    <div x-show="open" class="p-4 space-y-3">

        <label class="font-medium block mb-1">Paket Termasuk Tiket Pesawat?</label>

        <select name="flight_info" class="border rounded px-3 py-2 w-full">
            <option value="not_included" 
                {{ old('flight_info', $package->flight_info ?? '') === 'not_included' ? 'selected':'' }}>
                Tidak termasuk tiket pesawat
            </option>

            <option value="included"
                {{ old('flight_info', $package->flight_info ?? '') === 'included' ? 'selected':'' }}>
                Termasuk tiket pesawat
            </option>
        </select>

    </div>
</div>

{{-- ========================== --}}
{{-- SUBMIT --}}
{{-- ========================== --}}
<div class="mt-6">
    <button class="px-6 py-3 bg-[#0194F3] text-white rounded shadow font-semibold">
        Simpan Paket
    </button>
</div>
