@extends('layouts.admin')

@section('title', 'Tambah Review')
@section('page-title', 'Tambah Review')

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <div class="text-sm font-extrabold text-slate-900">Tambah Review (Manual dari Admin)</div>
            <div class="mt-1 text-xs text-slate-500">Review ini akan langsung berstatus <b>approved</b>.</div>
        </div>

        <form method="POST" action="{{ route('admin.reviews.store') }}" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Pilih Paket Tour</label>
                <select name="tour_package_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    <option value="">-- Pilih Paket --</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" {{ old('tour_package_id') == $p->id ? 'selected' : '' }}>
                            #{{ $p->id }} - {{ $p->title }}
                        </option>
                    @endforeach
                </select>
                @error('tour_package_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Jumlah Bintang (1 - 5)</label>
                <select name="rating" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm" required>
                    @for($i=1; $i<=5; $i++)
                        <option value="{{ $i }}" {{ (int)old('rating', 5) === $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
                @error('rating') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-1">Ulasan</label>
                <textarea name="comment" rows="5"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm"
                    placeholder="Tulis ulasan..." required>{{ old('comment') }}</textarea>
                @error('comment') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <a href="{{ route('admin.reviews.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-[#0194F3] px-4 py-2 text-sm font-bold text-white hover:opacity-95">
                    Simpan Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
