@csrf

<div class="space-y-8">

    {{-- 1. INFORMASI DASAR --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-sky-500"></i>
                Informasi Dasar Restoran
            </h3>
            <p class="text-xs text-slate-500">Nama restoran, label promosi, harga dasar per porsi/peserta.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama Restoran <span class="text-red-500">*</span></label>
                <input type="text" name="title"
                    value="{{ old('title', $package->title ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: Joglo Panglipuran Borobudur"
                    required>
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Slug (URL)</label>
                <input type="text" name="slug"
                    value="{{ old('slug', $package->slug ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Dibuat otomatis jika dikosongkan">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Label</label>
                <input type="text" name="label"
                    value="{{ old('label', $package->label ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Opsional (misal: Populer, Rekomendasi)">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Harga per Pax (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="price_per_pax" min="0"
                    value="{{ old('price_per_pax', $package->price_per_pax ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: 25000"
                    required>
            </div>

            @role('admin')
            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Status Publikasi</label>
                <select name="is_active"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                    <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                    <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Draft)</option>
                </select>
            </div>
            @endrole
        </div>
    </div>

    {{-- 2. FOTO THUMBNAIL & FOTO BIASA (GALERI) --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-sky-500"></i>
                Foto Thumbnail & Galeri Foto
            </h3>
            <p class="text-xs text-slate-500">Thumbnail utama untuk cover kartu dan foto galeri untuk hero slider/grid.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            {{-- Foto Thumbnail --}}
            <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Foto Thumbnail (Cover Utama)</label>
                <input type="file" name="thumbnail" accept="image/*"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">PNG/JPG/WEBP, maksimal 2MB</p>

                @isset($package)
                    @if(!empty($package->thumbnail_path))
                    <div class="mt-3">
                        <div class="text-xs font-bold text-slate-600 mb-1">Thumbnail Saat Ini:</div>
                        <div class="h-28 w-44 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                            <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
                                class="h-full w-full object-cover"
                                alt="Thumbnail">
                        </div>
                    </div>
                    @endif
                @endisset
            </div>

            {{-- Foto Biasa (Galeri) --}}
            <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Foto Biasa (Upload Galeri Baru)</label>
                <input type="file" name="gallery[]" multiple accept="image/*"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-slate-500">Pilih beberapa foto sekaligus untuk galeri restoran (PNG/JPG/WEBP, maks 3MB/foto).</p>

                @if(isset($package) && $package->photos && $package->photos->count() > 0)
                <div class="mt-3">
                    <div class="text-xs font-bold text-slate-600 mb-1">Galeri Foto Tersimpan ({{ $package->photos->count() }} foto):</div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-52 overflow-y-auto p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($package->photos as $photo)
                        <div class="relative group rounded-lg overflow-hidden border border-slate-200 aspect-video bg-slate-100">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover" alt="Galeri">
                            <button type="button"
                                onclick="if(confirm('Hapus foto galeri ini?')) { document.getElementById('delete-photo-{{ $photo->id }}').submit(); }"
                                class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white rounded p-1 shadow transition opacity-80 hover:opacity-100"
                                title="Hapus foto ini">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. AREA AKOMODASI & MAPS --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-sky-500"></i>
                Area Akomodasi & Maps
            </h3>
            <p class="text-xs text-slate-500">Informasi alamat lengkap restoran, link peta Google Maps, dan tempat rekreasi terdekat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: Jl. Babakan lampit, rt 01 rw 09, desa panundaan kecamatan ciwidey, Bandung, Jawa Barat">{{ old('address', $package->address ?? '') }}</textarea>
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Link Google Maps (Peta)</label>
                <input type="text" name="maps_url"
                    value="{{ old('maps_url', $package->maps_url ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: https://maps.google.com/?q=...">
                <p class="mt-1 text-xs text-slate-400">Link ini akan dibuka ketika pengunjung mengklik tombol "Lihat Peta".</p>
            </div>

            {{-- Tempat Rekreasi Terdekat --}}
            <div class="md:col-span-12">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Tempat Terdekat / Landmark Sekitar</label>
                <p class="text-xs text-slate-500 mb-2">Daftar lokasi wisata / landmark di dekat restoran beserta estimasi jaraknya.</p>

                @php
                $nearby = old('nearby_places', $package->nearby_places ?? []);
                if (!is_array($nearby)) $nearby = [];
                @endphp

                <div x-data="{
                    rows: @js($nearby),
                    addRow() { this.rows.push({name: '', distance: ''}); },
                    removeRow(i) { this.rows.splice(i, 1); }
                }" class="space-y-2">
                    <template x-for="(row, idx) in rows" :key="idx">
                        <div class="grid grid-cols-12 gap-2 items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <div class="col-span-7 sm:col-span-8">
                                <input type="text" :name="`nearby_places[${idx}][name]`" x-model="row.name"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="Nama Tempat (contoh: Taman Kelinci Ciwidey)">
                            </div>
                            <div class="col-span-4 sm:col-span-3">
                                <input type="text" :name="`nearby_places[${idx}][distance]`" x-model="row.distance"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="Jarak (contoh: 1.27 km / 450 m)">
                            </div>
                            <div class="col-span-1 text-right">
                                <button type="button" @click="removeRow(idx)"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addRow()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Tambah Lokasi Sekitar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. FASILITAS UTAMA --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="coffee" class="w-5 h-5 text-sky-500"></i>
                Fasilitas Utama
            </h3>
            <p class="text-xs text-slate-500">Fasilitas yang disediakan oleh restoran (misal: Restoran, Parkir, WiFi, AC, Musholla, dll).</p>
        </div>

        @php
        $facilities = old('facilities', $package->facilities ?? ['Restoran', 'Parkir', 'WiFi']);
        if (!is_array($facilities)) $facilities = [];
        @endphp

        <div x-data="{
            rows: @js($facilities),
            addRow(val = '') { this.rows.push(val); },
            removeRow(i) { this.rows.splice(i, 1); },
            quickAdd(val) {
                if (!this.rows.includes(val)) {
                    this.rows.push(val);
                }
            }
        }">
            {{-- Quick chips --}}
            <div class="flex flex-wrap items-center gap-1.5 mb-3">
                <span class="text-xs text-slate-500 font-bold mr-1">Klik untuk tambah cepat:</span>
                @foreach(['Restoran', 'Parkir', 'WiFi', 'AC', 'Musholla', 'Toilet Bersih', 'Area Outdoor', 'VIP Room', 'Live Music', 'Playground'] as $item)
                <button type="button" @click="quickAdd('{{ $item }}')"
                    class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 transition">
                    + {{ $item }}
                </button>
                @endforeach
            </div>

            <div class="space-y-2">
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                        <div class="flex-1">
                            <input type="text" :name="`facilities[${idx}]`" x-model="rows[idx]"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs focus:ring-2 focus:ring-sky-400"
                                placeholder="Nama Fasilitas (contoh: WiFi, Parkir Luas)">
                        </div>
                        <button type="button" @click="removeRow(idx)"
                            class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>

                <button type="button" @click="addRow('')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Fasilitas
                </button>
            </div>
        </div>
    </div>

    {{-- 5. KEUNGGULAN RESTO & NOTE --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Keunggulan Resto --}}
        <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="border-b border-slate-200 pb-2 mb-3">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-amber-500"></i>
                    Keunggulan Resto
                </h4>
                <p class="text-xs text-slate-500">Poin-poin yang menjadi nilai lebih restoran ini.</p>
            </div>

            @php
            $keunggulan = old('keunggulan', $package->keunggulan ?? ['Harga terbaik di kelasnya', 'Kualitas layanan terjamin']);
            if (!is_array($keunggulan)) $keunggulan = [];
            @endphp

            <div x-data="{
                rows: @js($keunggulan),
                addRow() { this.rows.push(''); },
                removeRow(i) { this.rows.splice(i, 1); }
            }" class="space-y-2">
                <template x-for="(row, idx) in rows" :key="idx">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`keunggulan[${idx}]`" x-model="rows[idx]"
                            class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                            placeholder="Contoh: Harga terbaik di kelasnya">
                        <button type="button" @click="removeRow(idx)"
                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>

                <button type="button" @click="addRow()"
                    class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Keunggulan
                </button>
            </div>
        </div>

        {{-- Note Resto & Kontak CS --}}
        <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="border-b border-slate-200 pb-2 mb-3">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-sky-500"></i>
                    Catatan Khusus (NOTE) & Kontak CS
                </h4>
                <p class="text-xs text-slate-500">Catatan/informasi penting untuk ditampilkan pada kartu Note beserta nomor kontak WhatsApp CS.</p>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-extrabold text-slate-800 mb-1">Catatan (Note)</label>
                    <textarea name="note" rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                        placeholder="Contoh: Untuk Informasi anda bisa menghubungi kontak Bintang Wisata.">{{ old('note', $package->note ?? 'Untuk Informasi anda bisa menghubungi kontak Bintang Wisata.') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-800 mb-1">Nomor WhatsApp Kontak CS</label>
                    <input type="text" name="cs_contact"
                        value="{{ old('cs_contact', $package->cs_contact ?? '') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                        placeholder="Contoh: 08123456789 atau 628123456789">
                    <p class="mt-1 text-[11px] text-slate-400">Nomor WhatsApp ini akan dihubungi ketika pengunjung mengklik 'Hubungi Kontak CS' pada kartu Note di halaman detail restoran.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. MENU RESTO (DINAMIS DENGAN STATUS READY / TIDAK READY) --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="utensils" class="w-5 h-5 text-sky-500"></i>
                    Menu Resto (Tersedia / Habis)
                </h3>
                <p class="text-xs text-slate-500">Daftar hidangan/menu restoran. Anda dapat mengatur status ketersediaan (Ready atau Tidak Ready).</p>
            </div>
        </div>

        @php
        $menus = [];
        if (old('menus')) {
            $menus = old('menus');
        } elseif (isset($package) && $package->menus && $package->menus->count() > 0) {
            foreach ($package->menus as $m) {
                $menus[] = [
                    'id' => $m->id,
                    'name' => $m->name,
                    'category' => $m->category ?? '',
                    'price' => (float)$m->price,
                    'is_ready' => $m->is_ready ? 1 : 0,
                    'thumbnail_url' => $m->thumbnail_path ? asset('storage/' . $m->thumbnail_path) : '',
                    'existing_thumbnail' => $m->thumbnail_path ?? '',
                ];
            }
        }
        @endphp

        <div x-data="{
            rows: @js($menus),
            addRow() {
                this.rows.push({
                    id: '',
                    name: '',
                    category: '',
                    price: '',
                    is_ready: 1,
                    thumbnail_url: '',
                    existing_thumbnail: ''
                });
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },
            removeRow(i) {
                this.rows.splice(i, 1);
            }
        }" class="space-y-3">

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                <table class="min-w-[850px] w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr class="text-xs font-extrabold text-slate-600">
                            <th class="px-4 py-3 w-[150px]">Foto Thumbnail</th>
                            <th class="px-4 py-3">Nama Menu <span class="text-red-500">*</span></th>
                            <th class="px-4 py-3 w-[160px]">Kategori</th>
                            <th class="px-4 py-3 w-[150px]">Harga (Rp)</th>
                            <th class="px-4 py-3 w-[160px] text-center">Status</th>
                            <th class="px-4 py-3 w-[60px] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(row, idx) in rows" :key="idx">
                            <tr class="text-xs hover:bg-slate-50/70 transition">
                                {{-- Thumbnail --}}
                                <td class="px-4 py-3">
                                    <input type="hidden" :name="`menus[${idx}][id]`" :value="row.id">
                                    <input type="hidden" :name="`menus[${idx}][existing_thumbnail]`" :value="row.existing_thumbnail">
                                    <div class="flex items-center gap-2">
                                        <template x-if="row.thumbnail_url">
                                            <div class="w-12 h-10 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0">
                                                <img :src="row.thumbnail_url" class="w-full h-full object-cover">
                                            </div>
                                        </template>
                                        <input type="file" :name="`menus[${idx}][thumbnail]`" accept="image/*"
                                            class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                    </div>
                                </td>

                                {{-- Nama Menu --}}
                                <td class="px-4 py-3">
                                    <input type="text" :name="`menus[${idx}][name]`" x-model="row.name" required
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                        placeholder="Contoh: Nasi Tempong">
                                </td>

                                {{-- Kategori --}}
                                <td class="px-4 py-3">
                                    <input type="text" :name="`menus[${idx}][category]`" x-model="row.category"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                        placeholder="Makanan / Minuman">
                                </td>

                                {{-- Harga --}}
                                <td class="px-4 py-3">
                                    <input type="number" :name="`menus[${idx}][price]`" x-model="row.price" min="0"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                        placeholder="25000">
                                </td>

                                {{-- Status Ketersediaan --}}
                                <td class="px-4 py-3 text-center">
                                    <select :name="`menus[${idx}][is_ready]`" x-model="row.is_ready"
                                        class="rounded-xl border px-3 py-1.5 text-xs font-bold"
                                        :class="row.is_ready == 1 || row.is_ready == '1' ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-rose-300 text-rose-700 bg-rose-50'">
                                        <option value="1">Ready (Tersedia)</option>
                                        <option value="0">Tidak Ready (Habis)</option>
                                    </select>
                                </td>

                                {{-- Aksi Hapus --}}
                                <td class="px-4 py-3 text-right">
                                    <button type="button" @click="removeRow(idx)"
                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition"
                                        title="Hapus baris menu">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <template x-if="rows.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">
                                    Belum ada menu restoran yang ditambahkan. Klik tombol "+ Tambah Menu Resto" di bawah.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="button" @click="addRow()"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold text-white transition shadow-sm"
                style="background:#0194F3;"
                onmouseover="this.style.background='#0186DB'"
                onmouseout="this.style.background='#0194F3'">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Tambah Menu Resto
            </button>
        </div>
    </div>

    {{-- 7. DESKRIPSI LENGKAP --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-sky-500"></i>
                Deskripsi Lengkap Restoran
            </h3>
            <p class="text-xs text-slate-500">Tuliskan informasi suasana resto, sejarah, menu andalan, dan ketentuan reservasi.</p>
        </div>

        <textarea name="long_description"
            rows="10"
            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
            placeholder="Deskripsi restoran...">{{ old('long_description', $package->long_description ?? '') }}</textarea>
    </div>

    {{-- SEO SETTINGS --}}
    @include('partials._seo_form', ['model' => $package ?? null])

    {{-- ACTIONS --}}
    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <a href="{{ route('admin.restoran-packages.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
            Kembali
        </a>

        <button type="submit"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 text-sm font-extrabold text-white transition shadow"
            style="background:#0194F3;"
            onmouseover="this.style.background='#0186DB'"
            onmouseout="this.style.background='#0194F3'">
            <i data-lucide="save" class="w-4 h-4"></i>
            {{ $buttonText ?? 'Simpan Paket' }}
        </button>
    </div>

</div>

{{-- Hidden Delete Forms for Gallery Photos --}}
@if(isset($package) && $package->photos && $package->photos->count() > 0)
    @foreach($package->photos as $photo)
    <form id="delete-photo-{{ $photo->id }}" action="{{ route('admin.restoran-packages.delete-photo', $photo->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
@endif