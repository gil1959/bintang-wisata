@extends('layouts.admin')

@section('title', 'Edit Paket Wisata')
@section('page-title', 'Edit Paket Wisata')

@section('content')

{{-- ERROR GLOBAL --}}
@if ($errors->any())
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('admin.tour-packages.update', $package->id) }}"
      method="POST" enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <!-- ============================ -->
    <!-- SECTION 1: INFORMASI DASAR -->
    <!-- ============================ -->
    <div class="bg-white p-5 rounded shadow mb-6">

        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">
            Informasi Dasar
        </h3>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="block text-sm font-medium mb-1">Judul Paket</label>
                <input type="text" name="title" value="{{ $package->title }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Slug</label>
                <input type="text" name="slug" value="{{ $package->slug }}"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategori Tour</label>
                <select name="category_id" class="w-full border px-3 py-2 rounded" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" 
                                {{ $c->id == $package->category_id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Durasi</label>
                <input type="text" name="duration_text" value="{{ $package->duration_text }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Destinasi</label>
                <input type="text" name="destination" value="{{ $package->destination }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Deskripsi / Tentang Paket</label>
                <textarea name="long_description" rows="5"
                          class="w-full border rounded px-3 py-2">{{ $package->long_description }}</textarea>
            </div>

        </div>
    </div>

    <!-- ============================ -->
    <!-- SECTION 2: FOTO -->
    <!-- ============================ -->

    <div class="bg-white p-5 rounded shadow mb-6">
        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">Foto Paket</h3>

        {{-- THUMBNAIL --}}
        @if($package->thumbnail_path)
            <p class="font-semibold mb-2">Thumbnail Saat Ini:</p>
            <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                 class="h-24 rounded mb-4 object-cover">
        @endif

        <label class="font-medium">Upload Thumbnail Baru</label>
        <input type="file" name="thumbnail" class="border p-2 w-full mb-6">


        {{-- GALLERY --}}
        <h4 class="font-semibold mb-2">Galeri Foto</h4>

        <div class="grid grid-cols-4 gap-4 mb-4">
            @foreach($package->photos as $photo)
                <div class="border p-2 rounded">
                    <img src="{{ asset('storage/' . $photo->file_path) }}"
                         class="h-24 w-full object-cover rounded mb-2">

                    <form action="{{ route('admin.tour-packages.delete-photo', $photo->id) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white text-xs px-2 py-1 rounded">
                            Hapus
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <label class="font-medium">Tambah Foto Baru</label>
        <input type="file" name="gallery[]" multiple class="border p-2 w-full">
    </div>



    <!-- ============================ -->
    <!-- SECTION 3: INCLUDE / EXCLUDE -->
    <!-- ============================ -->

    <div class="bg-white p-5 rounded shadow mb-6">
        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">
            Include / Exclude
        </h3>

        <div class="grid grid-cols-2 gap-6">

            {{-- INCLUDE --}}
            <div>
                <label class="block font-medium mb-2">Include</label>

                <div id="include-list">
                    @foreach($package->includes ?? [] as $inc)
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="includes[]"
                                   value="{{ $inc }}"
                                   class="flex-1 border px-2 py-1 rounded">
                            <button type="button" onclick="this.parentElement.remove()">❌</button>
                        </div>
                    @endforeach
                </div>

                <button type="button"
                        onclick="addInclude()"
                        class="px-3 py-1 bg-[#0194F3] text-white rounded mt-2">
                    + Tambah Include
                </button>
            </div>


            {{-- EXCLUDE --}}
            <div>
                <label class="block font-medium mb-2">Exclude</label>

                <div id="exclude-list">
                    @foreach($package->excludes ?? [] as $exc)
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="excludes[]"
                                   value="{{ $exc }}"
                                   class="flex-1 border px-2 py-1 rounded">
                            <button type="button" onclick="this.parentElement.remove()">❌</button>
                        </div>
                    @endforeach
                </div>

                <button type="button"
                        onclick="addExclude()"
                        class="px-3 py-1 bg-[#0194F3] text-white rounded mt-2">
                    + Tambah Exclude
                </button>
            </div>

        </div>
    </div>




    <!-- ============================ -->
    <!-- SECTION 4: ITINERARY -->
    <!-- ============================ -->

    <div class="bg-white p-5 rounded shadow mb-6">

        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">
            Itinerary Perjalanan
        </h3>

        <div id="itinerary-list">

            @foreach($package->itineraries as $index => $it)
                <div class="grid grid-cols-3 gap-3 mb-3 itinerary-row">

                    <input type="hidden"
                           name="itineraries[{{ $index }}][id]"
                           value="{{ $it->id }}">

                    <input type="time"
                           name="itineraries[{{ $index }}][time]"
                           value="{{ substr($it->time, 0, 5) }}"
                           class="border px-2 py-1 rounded">

                    <input type="text"
                           name="itineraries[{{ $index }}][title]"
                           value="{{ $it->title }}"
                           class="border px-2 py-1 rounded">

                    <button type="button" onclick="removeItinerary(this)">❌</button>


                </div>
            @endforeach

        </div>

        <button type="button"
                onclick="addItinerary()"
                class="px-3 py-1 bg-[#0194F3] text-white rounded mt-2">
            + Tambah Itinerary
        </button>

    </div>




    <!-- ============================ -->
    <!-- SECTION 5: TIERS -->
    <!-- ============================ -->
    <div class="bg-white p-5 rounded shadow mb-6">

        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">
            Harga Tier (Domestik & WNA)
        </h3>

        {{-- DOMESTIK --}}
        <h4 class="font-semibold mb-2">Domestik</h4>

        <div id="tier-domestic">

            @foreach($package->tiers->where('type','domestic') as $index => $t)
                <div class="grid grid-cols-6 gap-2 mb-3 border p-3 rounded tier-row">

                    <input type="hidden"
                           name="tiers[domestic][{{ $index }}][id]"
                           value="{{ $t->id }}">

                    <input type="hidden"
                           name="tiers[domestic][{{ $index }}][type]"
                           value="domestic">

                    <input type="hidden"
                           name="tiers[domestic][{{ $index }}][is_custom]"
                           value="0">

                    <div class="flex items-center gap-2">
                        <input type="checkbox"
                               name="tiers[domestic][{{ $index }}][is_custom]"
                               value="1"
                               {{ $t->is_custom ? 'checked' : '' }}>
                        <span class="text-sm">Custom</span>
                    </div>

                    <input type="number" name="tiers[domestic][{{ $index }}][min_people]"
                           value="{{ $t->min_people }}"
                           class="border px-2 py-1 rounded">

                    <input type="number" name="tiers[domestic][{{ $index }}][max_people]"
                           value="{{ $t->max_people }}"
                           class="border px-2 py-1 rounded">

                    <input type="number" name="tiers[domestic][{{ $index }}][price]"
                           value="{{ $t->price }}"
                           class="border px-2 py-1 rounded">

                    <button type="button" onclick="removeTier(this)">❌</button>

                </div>
            @endforeach

        </div>

        <button type="button"
                onclick="addTier('domestic')"
                class="px-3 py-1 bg-[#0194F3] text-white rounded mb-6">
            + Tambah Tier Domestik
        </button>



        {{-- WNA --}}
        <h4 class="font-semibold mb-2">WNA</h4>

        <div id="tier-international">

            @foreach($package->tiers->where('type','international') as $index => $t)
                <div class="grid grid-cols-6 gap-2 mb-3 border p-3 rounded tier-row">

                    <input type="hidden"
                           name="tiers[international][{{ $index }}][id]"
                           value="{{ $t->id }}">

                    <input type="hidden"
                           name="tiers[international][{{ $index }}][type]"
                           value="international">

                    <input type="hidden"
                           name="tiers[international][{{ $index }}][is_custom]"
                           value="0">

                    <div class="flex items-center gap-2">
                        <input type="checkbox"
                               name="tiers[international][{{ $index }}][is_custom]"
                               value="1"
                               {{ $t->is_custom ? 'checked' : '' }}>
                        <span class="text-sm">Custom</span>
                    </div>

                    <input type="number" name="tiers[international][{{ $index }}][min_people]"
                           value="{{ $t->min_people }}"
                           class="border px-2 py-1 rounded">

                    <input type="number" name="tiers[international][{{ $index }}][max_people]"
                           value="{{ $t->max_people }}"
                           class="border px-2 py-1 rounded">

                    <input type="number" name="tiers[international][{{ $index }}][price]"
                           value="{{ $t->price }}"
                           class="border px-2 py-1 rounded">

                    <button type="button" onclick="removeTier(this)">❌</button>

                </div>
            @endforeach

        </div>

        <button type="button"
                onclick="addTier('international')"
                class="px-3 py-1 bg-[#0194F3] text-white rounded">
            + Tambah Tier WNA
        </button>
    </div>




    <!-- ============================ -->
    <!-- SECTION 6: Flight Info -->
    <!-- ============================ -->

    <div class="bg-white p-5 rounded shadow mb-6">
        <h3 class="font-semibold text-lg border-b pb-2 mb-4 text-[#0194F3]">
            Pengaturan Tiket Pesawat
        </h3>

        <select name="flight_info" class="w-full border px-3 py-2 rounded">
            <option value="not_included" 
                {{ $package->flight_info == 'not_included' ? 'selected' : '' }}>
                Tidak termasuk tiket pesawat
            </option>

            <option value="included"
                {{ $package->flight_info == 'included' ? 'selected' : '' }}>
                Sudah termasuk tiket pesawat
            </option>
        </select>
    </div>

    <!-- SUBMIT -->
    <div class="flex justify-end">
        <button class="px-5 py-2 bg-[#0194F3] text-white rounded-lg hover:bg-blue-600">
            Update Paket
        </button>
    </div>

</form>




{{-- ============================ --}}
{{-- JAVASCRIPT FIX RE-INDEX      --}}
{{-- ============================ --}}
<script>

function reindexRows() {
    // Reindex itinerary rows
    document.querySelectorAll("#itinerary-list .itinerary-row").forEach((row, i) => {
        row.querySelectorAll("input").forEach(input => {
            input.name = input.name.replace(/\[\d+]/, `[${i}]`);
        });
    });

    // Reindex tier rows (domestic)
    document.querySelectorAll("#tier-domestic .tier-row").forEach((row, i) => {
        row.querySelectorAll("input").forEach(input => {
            input.name = input.name.replace(/\[domestic]\[\d+]/, `[domestic][${i}]`);
        });
    });

    // Reindex tier rows (international)
    document.querySelectorAll("#tier-international .tier-row").forEach((row, i) => {
        row.querySelectorAll("input").forEach(input => {
            input.name = input.name.replace(/\[international]\[\d+]/, `[international][${i}]`);
        });
    });
}

function removeItinerary(btn) {
    const row = btn.parentElement;

    // Hapus semua input di dalam row agar TIDAK terkirim ke server
    row.querySelectorAll("input").forEach(i => i.remove());

    // Baru hilangkan dari DOM
    row.remove();
}

function removeTier(btn) {
    btn.parentElement.remove();
    reindexRows();
}

function addItinerary() {
    let index = document.querySelectorAll("#itinerary-list .itinerary-row").length;

    let el = `
        <div class="grid grid-cols-3 gap-3 mb-3 itinerary-row">
            <input type="hidden" name="itineraries[${index}][id]" value="">
            <input type="time" name="itineraries[${index}][time]" class="border px-2 py-1 rounded">
            <input type="text" name="itineraries[${index}][title]" class="border px-2 py-1 rounded">
            <button type="button" onclick="removeItinerary(this)">❌</button>
        </div>`;

    document.getElementById("itinerary-list").insertAdjacentHTML("beforeend", el);
}

function addTier(type) {
    let container = document.getElementById(`tier-${type}`);
    let index = container.querySelectorAll(".tier-row").length;

    let el = `
        <div class="grid grid-cols-6 gap-2 mb-3 border p-3 rounded tier-row">

            <input type="hidden" name="tiers[${type}][${index}][id]" value="">
            <input type="hidden" name="tiers[${type}][${index}][type]" value="${type}">
            <input type="hidden" name="tiers[${type}][${index}][is_custom]" value="0">

            <div class="flex items-center gap-2">
                <input type="checkbox" name="tiers[${type}][${index}][is_custom]" value="1">
                <span class="text-sm">Custom</span>
            </div>

            <input type="number" name="tiers[${type}][${index}][min_people]"
                   placeholder="Min" class="border px-2 py-1 rounded">

            <input type="number" name="tiers[${type}][${index}][max_people]"
                   placeholder="Max" class="border px-2 py-1 rounded">

            <input type="number" name="tiers[${type}][${index}][price]"
                   placeholder="Harga" class="border px-2 py-1 rounded">

            <button type="button" onclick="removeTier(this)">❌</button>

        </div>
    `;

    container.insertAdjacentHTML("beforeend", el);
}

</script>

@endsection
