@extends('layouts.admin')

@section('title', 'Home - Promo Tours')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Home Section: Promo Paket Tour</h1>
           
        </div>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800">
            <div class="font-extrabold">Sukses</div>
            <div class="text-sm mt-1">{{ session('success') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.home-sections.promo-tours.update') }}" class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm">
        @csrf

        <div class="px-6 py-5 border-b border-slate-200">
            <div class="text-lg font-extrabold text-slate-900">Pengaturan</div>
            <div class="text-sm text-slate-600 mt-1">Pastikan paket yang mau tampil sudah punya label PROMO.</div>
        </div>

        <div class="p-6 grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2 flex items-center gap-3">
                <input
                    type="checkbox"
                    name="home_promo_enabled"
                    value="1"
                    class="rounded border-slate-300"
                    {{ old('home_promo_enabled', ($settings['home_promo_enabled'] ?? '1')) == '1' ? 'checked' : '' }}
                />
                <div class="text-sm text-slate-900 font-semibold">Aktifkan section Promo</div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">Badge</label>
                <input
                    name="home_promo_badge"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    value="{{ old('home_promo_badge', $settings['home_promo_badge'] ?? 'PROMO') }}"
                    placeholder="PROMO"
                />
                @error('home_promo_badge') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2">Mode</label>
                @php $mode = old('home_promo_mode', $settings['home_promo_mode'] ?? 'auto'); @endphp
                <select name="home_promo_mode" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900">
                    <option value="auto" {{ $mode === 'auto' ? 'selected' : '' }}>Auto (ambil label PROMO)</option>
                    <option value="custom" {{ $mode === 'custom' ? 'selected' : '' }}>Custom (pilih paket)</option>
                </select>
                @error('home_promo_mode') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-900 mb-2">Judul</label>
                <input
                    name="home_promo_title"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    value="{{ old('home_promo_title', $settings['home_promo_title'] ?? 'Paket Tour Promo') }}"
                    placeholder="Paket Tour Promo"
                />
                @error('home_promo_title') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-900 mb-2">Deskripsi</label>
                <textarea
                    name="home_promo_desc"
                    rows="3"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900"
                    placeholder="Tampilkan promo terbaik minggu ini..."
                >{{ old('home_promo_desc', $settings['home_promo_desc'] ?? '') }}</textarea>
                @error('home_promo_desc') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <div class="flex items-center justify-between gap-3 mb-2">
                    
                    <div class="text-xs text-slate-500">Dipakai hanya kalau mode = Custom</div>
                </div>

                <div class="rounded-2xl border border-slate-200 p-4 max-h-80 overflow-auto">
                    @if($promoCandidates->count() > 0)
                        <div class="grid gap-2">
                            @foreach($promoCandidates as $p)
                                <label class="flex items-center gap-3 text-sm">
                                    <input
                                        type="checkbox"
                                        name="home_promo_custom_ids[]"
                                        value="{{ $p->id }}"
                                        class="rounded border-slate-300"
                                        {{ in_array((int)$p->id, old('home_promo_custom_ids', $selectedIds)) ? 'checked' : '' }}
                                    />
                                    <span class="text-slate-900 font-semibold">#{{ $p->id }}</span>
                                    <span class="text-slate-700">{{ $p->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-slate-500">
                            Belum ada paket berlabel PROMO (atau belum aktif).
                        </div>
                    @endif
                </div>

                @error('home_promo_custom_ids') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
                @error('home_promo_custom_ids.*') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror

                
            </div>
        </div>

        <div class="px-6 py-5 border-t border-slate-200 flex items-center justify-end gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
@endsection
