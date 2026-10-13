@php
    $isEdit = isset($package);
    $facilitiesList = [
        'tv' => 'TV / Audio',
        'reclining_seat' => 'Reclining Seat',
        'wifi' => 'Free WiFi',
        'baggage' => 'Bagasi',
        'usb' => 'USB Charger',
        'ac' => 'AC Penuh',
        'snack' => 'Snack & Minuman',
        'toilet' => 'Toilet',
    ];
    $selectedFacilities = old('facilities', $package->facilities ?? []);
    $initialRoutes = old('route_details', $package->route_details ?? []);
    if (!is_array($initialRoutes)) {
        $initialRoutes = [];
    }
@endphp

<div x-data="{
    routes: {{ json_encode(array_values($initialRoutes)) }},
    isSubmitting: false,
    addRoute() {
        this.routes.push('');
    },
    removeRoute(index) {
        this.routes.splice(index, 1);
    }
}" @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true" class="space-y-6">

    {{-- Card: Informasi Armada & Rute --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">
            Informasi Armada & Rute
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Partner Selector (Admin only) --}}
            @if(isset($partners))
                <div class="md:col-span-2">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Pilih Partner (Opsional)</label>
                    <select name="created_by_partner_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800">
                        <option value="">-- Milik Admin Sendiri --</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ old('created_by_partner_id', $package->created_by_partner_id ?? '') == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }} ({{ $partner->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Nama Travel / PO --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Nama Travel / PO *</label>
                <input type="text" name="armada_name" value="{{ old('armada_name', $package->armada_name ?? '') }}"
                       placeholder="Contoh: Pasteur Trans, DayTrans, SAN Bus" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            {{-- Tipe Armada --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Tipe Kendaraan / Kursi *</label>
                <input type="text" name="armada_type" value="{{ old('armada_type', $package->armada_type ?? '') }}"
                       placeholder="Contoh: Toyota HiAce Premio Luxury (10 Seats) / Bus Sleeper (22 Seats)" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            {{-- Kelas Travel --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Kelas Layanan *</label>
                <select name="travel_type" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800">
                    @foreach(['BUSINESS', 'EXECUTIVE', 'VIP', 'SLEEPER', 'ECONOMY'] as $class)
                        <option value="{{ $class }}" {{ old('travel_type', $package->travel_type ?? 'BUSINESS') == $class ? 'selected' : '' }}>
                            {{ $class }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Harga --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Harga per Pax (Rp) *</label>
                <input type="number" name="price" value="{{ old('price', $package->price ?? '') }}"
                       placeholder="Contoh: 250000" required min="0"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            {{-- Titik Berangkat --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Titik Keberangkatan (Asal) *</label>
                <input type="text" name="departure_location" value="{{ old('departure_location', $package->departure_location ?? '') }}"
                       placeholder="Contoh: Pool Dipatiukur Bandung / Terminal Bengkulu" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            {{-- Titik Tiba --}}
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Titik Tiba (Tujuan) *</label>
                <input type="text" name="arrival_location" value="{{ old('arrival_location', $package->arrival_location ?? '') }}"
                       placeholder="Contoh: Mall FX Sudirman Jakarta / Terminal Pulo Gebang" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>
        </div>

        {{-- Dynamic Route Repeater --}}
        <div class="mt-6 border-t border-slate-100 pt-5">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <label class="block text-xs font-extrabold text-slate-800 uppercase">
                        Rute Detail & Titik Singgah / Transit (Opsional)
                    </label>
                    <span class="text-xs text-slate-500">Tambahkan titik rute yang dilalui armada secara dinamis.</span>
                </div>
                <button type="button" @click="addRoute()" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-bold flex items-center gap-1 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Titik Singgah</span>
                </button>
            </div>

            <div class="space-y-2 mt-3">
                <template x-for="(route, idx) in routes" :key="idx">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 text-xs font-black flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                        <input type="text" name="route_details[]" x-model="routes[idx]" placeholder="Contoh: Rest Area KM 57 / Penyeberangan Bakauheni-Merak / Terminal Rawamangun" class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
                        <button type="button" @click="removeRoute(idx)" class="w-8 h-8 rounded-xl text-rose-500 hover:bg-rose-50 flex items-center justify-center shrink-0 transition" title="Hapus titik">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
                <div x-show="routes.length === 0" class="text-xs text-slate-400 italic py-2">
                    Belum ada titik persinggahan tambahan. Klik "Tambah Titik Singgah" di atas jika ada transit/titik pemberhentian rute.
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Jadwal & Durasi (Mendukung Perjalanan > 24 Jam) --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">
            Jadwal Keberangkatan & Durasi Perjalanan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Jam Berangkat *</label>
                <input type="time" name="departure_time" value="{{ old('departure_time', isset($package) ? date('H:i', strtotime($package->departure_time)) : '08:00') }}" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Estimasi Jam Tiba *</label>
                <input type="time" name="arrival_time" value="{{ old('arrival_time', isset($package) ? date('H:i', strtotime($package->arrival_time)) : '14:00') }}" required
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Hari Tiba (Perjalanan Berhari-hari) *</label>
                <select name="arrival_days_offset" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800">
                    <option value="0" {{ old('arrival_days_offset', $package->arrival_days_offset ?? 0) == 0 ? 'selected' : '' }}>Hari yang sama (0 Hari)</option>
                    <option value="1" {{ old('arrival_days_offset', $package->arrival_days_offset ?? 0) == 1 ? 'selected' : '' }}>Keesokan Harinya (+1 Hari)</option>
                    <option value="2" {{ old('arrival_days_offset', $package->arrival_days_offset ?? 0) == 2 ? 'selected' : '' }}>Lusa (+2 Hari)</option>
                    <option value="3" {{ old('arrival_days_offset', $package->arrival_days_offset ?? 0) == 3 ? 'selected' : '' }}>3 Hari Kemudian (+3 Hari)</option>
                    <option value="4" {{ old('arrival_days_offset', $package->arrival_days_offset ?? 0) == 4 ? 'selected' : '' }}>4 Hari Kemudian (+4 Hari)</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Teks Format Durasi Kustom (Opsional)</label>
            <input type="text" name="duration_text" value="{{ old('duration_text', $package->duration_text ?? '') }}"
                   placeholder="Kosongkan untuk otomatis dihitung, atau tulis misal: 28 Jam / 1 Hari 6 Jam"
                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">
            <span class="text-[11px] text-slate-500 mt-1 block">Jika dikosongkan, durasi dihitung otomatis dari jam berangkat, jam tiba, dan hari tiba.</span>
        </div>
    </div>

    {{-- Card: Fasilitas Armada --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">
            Fasilitas Armada
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($facilitiesList as $key => $label)
                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="facilities[]" value="{{ $key }}"
                           {{ in_array($key, (array)$selectedFacilities) ? 'checked' : '' }}
                           class="rounded text-blue-600 focus:ring-0">
                    <span class="text-sm font-bold text-slate-800">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Card: Media & Foto --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">
            Foto Armada
        </h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Foto Utama (Cover Thumbnail)</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @if($isEdit && $package->thumbnail_path)
                    <div class="mt-2 h-20 w-32 rounded-xl overflow-hidden border border-slate-200">
                        <img src="{{ asset('storage/' . $package->thumbnail_path) }}" class="h-full w-full object-cover">
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Galeri Foto Tambahan</label>
                <input type="file" name="gallery[]" multiple accept="image/*"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                
                @if($isEdit && $package->photos->count())
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($package->photos as $photo)
                            <div class="relative h-20 w-24 rounded-xl overflow-hidden border border-slate-200">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card: Deskripsi & Ketentuan --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 mb-4">
            Deskripsi & Ketentuan Tiket
        </h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Deskripsi Tambahan</label>
                <textarea name="description" rows="3"
                          placeholder="Jelaskan detail armada, fasilitas khusus, atau informasi penting lainnya..."
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">{{ old('description', $package->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Ketentuan Tiket / Bagasi</label>
                <textarea name="terms_conditions" rows="3"
                          placeholder="Contoh: Penumpang diharapkan hadir 15 menit sebelum keberangkatan, maksimal bagasi 15kg..."
                          class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-800">{{ old('terms_conditions', $package->terms_conditions ?? '') }}</textarea>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}
                           class="rounded text-blue-600 focus:ring-0">
                    <span class="text-sm font-bold text-slate-800">Aktifkan paket untuk pemesanan umum</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.travel-bus.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 font-extrabold text-slate-700 hover:bg-slate-50 transition">
            Batal
        </a>
        <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 rounded-xl font-extrabold text-white flex items-center gap-2 transition" style="background:#0194F3;">
            <svg x-show="isSubmitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span x-text="isSubmitting ? 'Menyimpan...' : '{{ $isEdit ? 'Perbarui Paket' : 'Simpan Paket' }}'"></span>
        </button>
    </div>
</div>
