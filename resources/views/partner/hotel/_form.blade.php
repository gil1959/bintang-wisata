@csrf

<div class="space-y-8">

    {{-- 1. INFORMASI DASAR --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-sky-500"></i>
                Informasi Dasar Hotel / Vila
            </h3>
            <p class="text-xs text-slate-500">Nama penginapan, jenis properti, label promosi, dan harga per malam mulai dari.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Nama Properti <span class="text-red-500">*</span></label>
                <input type="text" name="title"
                    value="{{ old('title', $package->title ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: Alkasturi Hotel dan Cottage Ciwidey Syariah"
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
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Tipe Penginapan <span class="text-red-500">*</span></label>
                <select name="property_type"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400 font-semibold"
                    required>
                    @php $pType = old('property_type', $package->property_type ?? 'Hotel'); @endphp
                    <option value="Hotel" {{ $pType == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                    <option value="Vila" {{ $pType == 'Vila' ? 'selected' : '' }}>Vila</option>
                    <option value="Cottage" {{ $pType == 'Cottage' ? 'selected' : '' }}>Cottage</option>
                    <option value="Resort" {{ $pType == 'Resort' ? 'selected' : '' }}>Resort</option>
                    <option value="Homestay" {{ $pType == 'Homestay' ? 'selected' : '' }}>Homestay</option>
                    <option value="Glamping" {{ $pType == 'Glamping' ? 'selected' : '' }}>Glamping</option>
                    <option value="Guest House" {{ $pType == 'Guest House' ? 'selected' : '' }}>Guest House</option>
                    <option value="Apartemen" {{ $pType == 'Apartemen' ? 'selected' : '' }}>Apartemen</option>
                </select>
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Label Promosi</label>
                <input type="text" name="label"
                    value="{{ old('label', $package->label ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Opsional (misal: Populer, Rekomendasi)">
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Harga Mulai Dari (Rp/malam) <span class="text-red-500">*</span></label>
                <input type="number" name="price_per_night" min="0"
                    value="{{ old('price_per_night', $package->price_per_night ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: 450000"
                    required>
            </div>
        </div>
    </div>

    {{-- 2. FOTO THUMBNAIL & FOTO GALERI --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-sky-500"></i>
                Foto Thumbnail & Galeri Foto
            </h3>
            <p class="text-xs text-slate-500">Thumbnail utama untuk cover kartu dan foto galeri untuk hero grid foto di halaman depan.</p>
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
                <p class="mt-1 text-xs text-slate-500">Pilih beberapa foto sekaligus untuk galeri foto properti (PNG/JPG/WEBP, maks 3MB/foto).</p>

                @if(isset($package) && $package->photos && $package->photos->count() > 0)
                <div class="mt-3">
                    <div class="text-xs font-bold text-slate-600 mb-1">Galeri Foto Tersimpan ({{ $package->photos->count() }} foto):</div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-52 overflow-y-auto p-1 bg-white rounded-xl border border-slate-200">
                        @foreach($package->photos as $photo)
                        <div class="relative group rounded-lg overflow-hidden border border-slate-200 aspect-video bg-slate-100">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover" alt="Galeri">
                            <button type="button"
                                onclick="window.__bwDeletePhoto('{{ route('partner.hotel-packages.delete-photo', $photo->id) }}')"
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
            <p class="text-xs text-slate-500">Alamat lengkap penginapan, link Google Maps, dan landmark / tempat rekreasi terdekat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: Jl. Raya Ciwidey - Patengan No.KM 5, Ciwidey, Bandung, Jawa Barat">{{ old('address', $package->address ?? '') }}</textarea>
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Link Google Maps (Peta)</label>
                <input type="text" name="maps_url"
                    value="{{ old('maps_url', $package->maps_url ?? '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                    placeholder="Contoh: https://maps.google.com/?q=...">
                <p class="mt-1 text-xs text-slate-400">Link ini akan dibuka ketika pengunjung mengklik tombol "Lihat Peta".</p>
            </div>

            {{-- Landmark / Tempat Terdekat --}}
            <div class="md:col-span-12">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Tempat Terdekat / Landmark Sekitar</label>
                <p class="text-xs text-slate-500 mb-2">Daftar lokasi wisata / landmark di dekat penginapan beserta estimasi jaraknya.</p>

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
                                    placeholder="Jarak (contoh: 1.27 km / 5 Menit)">
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
                <i data-lucide="wifi" class="w-5 h-5 text-sky-500"></i>
                Fasilitas Utama
            </h3>
            <p class="text-xs text-slate-500">Fasilitas umum properti (misal: Kolam Renang, WiFi Gratis, Parkir Luas, Resepsionis 24 Jam, Restoran, AC, dll).</p>
        </div>

        @php
        $facilities = old('facilities', $package->facilities ?? ['WiFi', 'Parkir', 'Resepsionis 24 Jam', 'Restoran']);
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
                @foreach(['WiFi Gratis', 'Parkir Luas', 'Kolam Renang', 'Resepsionis 24 Jam', 'Restoran', 'AC', 'Fasilitas Rapat', 'Lift', 'Kamar Bebas Rokok', 'Pemanas Air', 'Area Bermain Anak', 'Taman Asri'] as $item)
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
                                placeholder="Nama Fasilitas (contoh: Kolam Renang Outdoor)">
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

    {{-- 5. KEUNGGULAN PROPERTI, NOTE & KONTAK CS --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        {{-- Keunggulan Properti --}}
        <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="border-b border-slate-200 pb-2 mb-3">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="award" class="w-4 h-4 text-sky-500"></i>
                    Keunggulan Properti
                </h4>
                <p class="text-xs text-slate-500">Poin daya tarik utama (misal: Suasana sejuk pegunungan, Dekat kawah putih, dll).</p>
            </div>

            @php
            $keunggulan = old('keunggulan', $package->keunggulan ?? ['Suasana sejuk dan asri khas pegunungan', 'Dekat dengan berbagai destinasi wisata populer']);
            if (!is_array($keunggulan)) $keunggulan = [];
            @endphp

            <div x-data="{
                rows: @js($keunggulan),
                addRow(val = '') { this.rows.push(val); },
                removeRow(i) { this.rows.splice(i, 1); },
                quickAdd(val) {
                    if (!this.rows.includes(val)) {
                        this.rows.push(val);
                    }
                }
            }">
                <div class="flex flex-wrap items-center gap-1.5 mb-2">
                    @foreach(['Suasana tenang & asri', 'Pemandangan alam spektakuler', 'Dekat tempat wisata populer', 'Parkir aman & luas', 'Pelayanan ramah 24 jam'] as $item)
                    <button type="button" @click="quickAdd('{{ $item }}')"
                        class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition">
                        + {{ $item }}
                    </button>
                    @endforeach
                </div>

                <div class="space-y-2">
                    <template x-for="(row, idx) in rows" :key="idx">
                        <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200">
                            <div class="flex-1">
                                <input type="text" :name="`keunggulan[${idx}]`" x-model="rows[idx]"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="Keunggulan (contoh: Suasana privat dan tenang)">
                            </div>
                            <button type="button" @click="removeRow(idx)"
                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addRow('')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-extrabold border border-slate-200 transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Tambah Keunggulan
                    </button>
                </div>
            </div>
        </div>

        {{-- Note & CS Contact --}}
        <div class="md:col-span-6 bg-slate-50 p-4 rounded-2xl border border-slate-200">
            <div class="border-b border-slate-200 pb-2 mb-3">
                <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-1.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-sky-500"></i>
                    Catatan Khusus (NOTE) & Kontak CS
                </h4>
                <p class="text-xs text-slate-500">Catatan/informasi penting untuk kartu Note beserta nomor kontak WhatsApp CS Bintang Wisata.</p>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-extrabold text-slate-800 mb-1">Catatan Khusus (Note)</label>
                    <textarea name="note" rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                        placeholder="Contoh: Untuk Informasi ketersediaan dan pertanyaan, hubungi kontak Bintang Wisata.">{{ old('note', $package->note ?? 'Untuk Informasi ketersediaan dan pertanyaan, hubungi kontak Bintang Wisata.') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-800 mb-1">Nomor WhatsApp Kontak CS</label>
                    <input type="text" name="cs_contact"
                        value="{{ old('cs_contact', $package->cs_contact ?? '') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                        placeholder="Contoh: 08123456789 atau 628123456789">
                    <p class="mt-1 text-[11px] text-slate-400">Nomor WhatsApp ini akan dihubungi saat tamu mengklik 'Hubungi Kontak CS' pada kartu Note.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. TIPE KAMAR (ROOM TYPES) --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="bed" class="w-5 h-5 text-sky-500"></i>
                    Tipe Kamar (Room Types)
                </h3>
                <p class="text-xs text-slate-500">Kelola daftar kamar: ukuran (m²), tipe ranjang, shower, WiFi, opsi sarapan (2 orang), harga coret, dan sisa kamar.</p>
            </div>
        </div>

        @php
        $rooms = [];
        if (old('rooms')) {
            foreach (old('rooms') as $r) {
                $pList = [];
                if (!empty($r['existing_photos']) && is_array($r['existing_photos'])) {
                    foreach ($r['existing_photos'] as $p) {
                        if (!empty($p) && is_string($p)) {
                            $pList[] = ['path' => $p, 'url' => asset('storage/' . $p)];
                        }
                    }
                } elseif (!empty($r['existing_photo'])) {
                    $pList[] = ['path' => $r['existing_photo'], 'url' => asset('storage/' . $r['existing_photo'])];
                }
                $rooms[] = [
                    'id' => $r['id'] ?? '',
                    'name' => $r['name'] ?? '',
                    'room_size' => $r['room_size'] ?? '',
                    'bed_type' => $r['bed_type'] ?? '',
                    'max_guests' => $r['max_guests'] ?? 2,
                    'has_shower' => isset($r['has_shower']) && $r['has_shower'] == 1 ? 1 : 0,
                    'has_wifi' => isset($r['has_wifi']) && $r['has_wifi'] == 1 ? 1 : 0,
                    'has_breakfast' => isset($r['has_breakfast']) && $r['has_breakfast'] == 1 ? 1 : 0,
                    'price' => (float)($r['price'] ?? 0),
                    'price_with_breakfast' => !empty($r['price_with_breakfast']) ? (float)$r['price_with_breakfast'] : '',
                    'original_price' => !empty($r['original_price']) ? (float)$r['original_price'] : '',
                    'available_rooms' => $r['available_rooms'] ?? 1,
                    'description' => $r['description'] ?? '',
                    'is_ready' => isset($r['is_ready']) && $r['is_ready'] == 1 ? 1 : 0,
                    'photos' => $pList,
                ];
            }
        } elseif (isset($package) && $package->rooms && $package->rooms->count() > 0) {
            foreach ($package->rooms as $r) {
                $pList = [];
                $all = $r->all_photos;
                foreach ($all as $p) {
                    $pList[] = [
                        'path' => $p,
                        'url' => asset('storage/' . $p),
                    ];
                }
                $rooms[] = [
                    'id' => $r->id,
                    'name' => $r->name,
                    'room_size' => $r->room_size ?? '',
                    'bed_type' => $r->bed_type ?? '',
                    'max_guests' => $r->max_guests ?? 2,
                    'has_shower' => $r->has_shower ? 1 : 0,
                    'has_wifi' => $r->has_wifi ? 1 : 0,
                    'has_breakfast' => $r->has_breakfast ? 1 : 0,
                    'price' => (float)$r->price,
                    'price_with_breakfast' => $r->price_with_breakfast ? (float)$r->price_with_breakfast : '',
                    'original_price' => $r->original_price ? (float)$r->original_price : '',
                    'available_rooms' => $r->available_rooms ?? 1,
                    'description' => $r->description ?? '',
                    'is_ready' => $r->is_ready ? 1 : 0,
                    'photos' => $pList,
                ];
            }
        }
        @endphp

        <div x-data="{
            rooms: @js($rooms),
            addRoom() {
                this.rooms.push({
                    id: '',
                    name: '',
                    room_size: '24.0 m²',
                    bed_type: '1 Double Bed',
                    max_guests: 2,
                    has_shower: 1,
                    has_wifi: 1,
                    has_breakfast: 1,
                    price: '',
                    price_with_breakfast: '',
                    original_price: '',
                    available_rooms: 3,
                    description: '',
                    is_ready: 1,
                    photos: []
                });
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },
            removeRoom(i) {
                this.rooms.splice(i, 1);
            },
            removePhoto(room, pIdx) {
                room.photos.splice(pIdx, 1);
                this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
            },
            validateFiles(event, room) {
                const count = (room.photos ? room.photos.length : 0) + event.target.files.length;
                if (count > 6) {
                    alert('Maksimal total 6 foto per tipe kamar. Hanya ' + (6 - (room.photos ? room.photos.length : 0)) + ' foto pertama yang akan diproses.');
                }
            }
        }" class="space-y-4">

            <template x-for="(room, idx) in rooms" :key="idx">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 shadow-sm space-y-4 relative">
                    <input type="hidden" :name="`rooms[${idx}][id]`" :value="room.id">

                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-sky-500 text-white font-black text-xs flex items-center justify-center" x-text="idx + 1"></span>
                            <span class="font-extrabold text-sm text-slate-800" x-text="room.name ? room.name : 'Tipe Kamar Baru'"></span>
                        </div>

                        <div class="flex items-center gap-3">
                            <select :name="`rooms[${idx}][is_ready]`" x-model="room.is_ready"
                                class="rounded-xl border px-3 py-1.5 text-xs font-bold"
                                :class="room.is_ready == 1 || room.is_ready == '1' ? 'border-emerald-300 text-emerald-700 bg-emerald-50' : 'border-rose-300 text-rose-700 bg-rose-50'">
                                <option value="1">Ready (Tersedia)</option>
                                <option value="0">Tidak Ready (Habis)</option>
                            </select>

                            <button type="button" @click="removeRoom(idx)"
                                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-100 rounded-xl transition"
                                title="Hapus Kamar Ini">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Baris 1: Foto Kamar (Up to 6), Nama, Ukuran, Ranjang, Tamu --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        {{-- Foto Kamar --}}
                        <div class="md:col-span-4">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-extrabold text-slate-700">Foto Kamar (Maks 6)</label>
                                <span class="text-[10px] text-sky-600 font-bold" x-text="`${(room.photos ? room.photos.length : 0)}/6 foto`"></span>
                            </div>

                            {{-- Previews of existing photos --}}
                            <div class="flex flex-wrap gap-2 mb-2" x-show="room.photos && room.photos.length > 0">
                                <template x-for="(ph, pIdx) in room.photos" :key="pIdx">
                                    <div class="relative group w-14 h-14 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex-shrink-0 shadow-sm">
                                        <img :src="ph.url" class="w-full h-full object-cover">
                                        <input type="hidden" :name="`rooms[${idx}][existing_photos][]`" :value="ph.path">
                                        <button type="button" @click="removePhoto(room, pIdx)"
                                            class="absolute inset-0 bg-red-600/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition text-xs"
                                            title="Hapus foto kamar ini">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <input type="file" :name="`rooms[${idx}][photos][]`" accept="image/*" multiple
                                @change="validateFiles($event, room)"
                                :disabled="room.photos && room.photos.length >= 6"
                                class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 disabled:opacity-50">
                            <p class="mt-1 text-[11px] text-slate-400">Pilih hingga 6 foto. Akan berputar otomatis (slider) di halaman detail penginapan.</p>
                        </div>

                        {{-- Info Kamar --}}
                        <div class="md:col-span-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-extrabold text-slate-700 mb-1">Nama / Judul Kamar <span class="text-red-500">*</span></label>
                                <input type="text" :name="`rooms[${idx}][name]`" x-model="room.name" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 font-bold"
                                    placeholder="Contoh: Standard Double Room, Deluxe Cottage">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 mb-1">Ukuran Kamar (m²)</label>
                                <input type="text" :name="`rooms[${idx}][room_size]`" x-model="room.room_size"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="Contoh: 24.0 m²">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 mb-1">Tipe Ranjang</label>
                                <input type="text" :name="`rooms[${idx}][bed_type]`" x-model="room.bed_type"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="Contoh: 1 double bed / 2 single bed">
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 mb-1">Kapasitas Tamu</label>
                                <input type="number" :name="`rooms[${idx}][max_guests]`" x-model="room.max_guests" min="1"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                    placeholder="2">
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Fasilitas Kamar (Shower, WiFi, Sarapan) Checkbox/Toggle --}}
                    <div class="bg-white p-3 rounded-xl border border-slate-200 flex flex-wrap items-center gap-6 text-xs font-extrabold text-slate-800">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" :name="`rooms[${idx}][has_shower]`" value="1"
                                :checked="room.has_shower == 1 || room.has_shower == '1'"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span>Shower Panas/Dingin</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" :name="`rooms[${idx}][has_wifi]`" value="1"
                                :checked="room.has_wifi == 1 || room.has_wifi == '1'"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span>Free WiFi</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" :name="`rooms[${idx}][has_breakfast]`" value="1"
                                :checked="room.has_breakfast == 1 || room.has_breakfast == '1'"
                                class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span>Termasuk Sarapan</span>
                        </label>
                    </div>

                    {{-- Baris 3: Harga Kamar, Harga Sarapan, Harga Coret, Ketersediaan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-sky-50/50 p-4 rounded-xl border border-sky-100">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                Harga Kamar / Malam <span class="text-red-500">*</span>
                            </label>
                            <input type="number" :name="`rooms[${idx}][price]`" x-model="room.price" min="0" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 font-black text-sky-600"
                                placeholder="450000">
                            <p class="text-[10px] text-slate-400 mt-0.5">Tarif dasar kamar per malam</p>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 mb-1">Harga + Sarapan (Opsional)</label>
                            <input type="number" :name="`rooms[${idx}][price_with_breakfast]`" x-model="room.price_with_breakfast" min="0"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                placeholder="550000">
                            <p class="text-[10px] text-slate-400 mt-0.5">Kosongkan jika tidak ada opsi sarapan</p>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 mb-1">Harga Coret (Diskon)</label>
                            <input type="number" :name="`rooms[${idx}][original_price]`" x-model="room.original_price" min="0"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                                placeholder="600000">
                            <p class="text-[10px] text-slate-400 mt-0.5">Tampil sebagai harga sebelum diskon</p>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 mb-1">Sisa Kamar Tersedia</label>
                            <input type="number" :name="`rooms[${idx}][available_rooms]`" x-model="room.available_rooms" min="0"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400 font-bold text-amber-700"
                                placeholder="3">
                            <p class="text-[10px] text-slate-400 mt-0.5">Misal: Sisa 3 kamar!</p>
                        </div>
                    </div>

                    {{-- Baris 4: Deskripsi Kamar --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1">
                            Catatan / Deskripsi Kamar <span class="text-[11px] text-slate-400 font-normal">(tampil di bawah harga kamar di frontend)</span>
                        </label>
                        <textarea :name="`rooms[${idx}][description]`" x-model="room.description" rows="2"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-sky-400"
                            placeholder="Contoh: Bebas asap rokok, balkon dengan pemandangan kebun, ketel listrik, kamar mandi dalam..."></textarea>
                    </div>
                </div>
            </template>

            <button type="button" @click="addRoom()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-extrabold text-white transition shadow-sm w-full sm:w-auto"
                style="background:#0088f8;"
                onmouseover="this.style.background='#0073d1'"
                onmouseout="this.style.background='#0088f8'">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Tipe Kamar
            </button>
        </div>
    </div>

    {{-- 7. DESKRIPSI LENGKAP PENGINAPAN --}}
    <div>
        <div class="border-b border-slate-200 pb-3 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-sky-500"></i>
                Deskripsi Lengkap Properti
            </h3>
            <p class="text-xs text-slate-500">Uraian detail mengenai daya tarik, fasilitas, dan pengalaman menginap.</p>
        </div>

        <textarea name="long_description"
            rows="10"
            class="wysiwyg w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
            placeholder="Deskripsi detail penginapan...">{{ old('long_description', $package->long_description ?? '') }}</textarea>
    </div>

    {{-- 8. SEO FORM --}}
    @include('partials._seo_form', ['model' => $package ?? null])

    {{-- ACTIONS --}}
    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <a href="{{ route('partner.hotel-packages.index') }}"
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