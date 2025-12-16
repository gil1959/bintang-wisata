@extends('layouts.admin')

@section('title', 'Dokumentasi')
@section('page-title', 'Dokumentasi')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Dokumentasi</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola foto dan video yang tampil di halaman Dokumentasi.</p>
        </div>

        <a href="{{ route('admin.documentations.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah
        </a>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-sm font-extrabold text-slate-800 mb-1">Tipe</label>
                <select name="type"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Semua</option>
                    <option value="photo" @selected(request('type')==='photo')>Foto</option>
                    <option value="video" @selected(request('type')==='video')>Video</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                    <i data-lucide="filter" class="w-4 h-4" style="color:#0194F3;"></i>
                    Filter
                </button>
            </div>

            @if(request()->filled('type'))
                <div class="sm:col-span-3">
                    <a href="{{ url()->current() }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
                       style="background:#ef4444"
                       onmouseover="this.style.background='#dc2626'"
                       onmouseout="this.style.background='#ef4444'">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-extrabold text-slate-600">
                        <th class="px-5 py-3 w-[180px]">Preview</th>
                        <th class="px-5 py-3">Judul</th>
                        <th class="px-5 py-3 w-[140px]">Tipe</th>
                        <th class="px-5 py-3 w-[140px]">Status</th>
                        <th class="px-5 py-3 w-[110px]">Urutan</th>
                        <th class="px-5 py-3 text-right w-[200px]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $d)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                @if($d->type === 'photo')
                                    <img src="{{ $d->url }}"
                                         alt="{{ $d->title ?? 'Dokumentasi' }}"
                                         class="h-[72px] w-[132px] rounded-xl border border-slate-200 object-cover bg-slate-100">
                                @else
                                    <div class="h-[72px] w-[132px] rounded-xl border border-slate-200 bg-slate-50 grid place-items-center">
                                        <span class="inline-flex items-center gap-2 text-xs font-extrabold text-slate-600">
                                            <i data-lucide="video" class="w-4 h-4" style="color:#0194F3;"></i>
                                            VIDEO
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $d->title ?? '-' }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ $d->url }}</div>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                      style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                                    <i data-lucide="{{ $d->type === 'photo' ? 'image' : 'video' }}" class="w-4 h-4"></i>
                                    {{ strtoupper($d->type) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                @if($d->is_active)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#065f46;">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(239,68,68,0.10); border-color: rgba(239,68,68,0.25); color:#7f1d1d;">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 font-extrabold text-slate-900">
                                {{ $d->sort_order }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.documentations.edit', $d) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.documentations.destroy', $d) }}"
                                          onsubmit="return confirm('Hapus item ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                                style="background:#ef4444"
                                                onmouseover="this.style.background='#dc2626'"
                                                onmouseout="this.style.background='#ef4444'">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                                Belum ada data dokumentasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div>
        {{ $items->links() }}
    </div>

</div>
@endsection
