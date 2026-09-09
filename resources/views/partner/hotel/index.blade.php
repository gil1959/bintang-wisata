@extends('partner.layouts.app')

@section('title', 'Hotel & Vila')
@section('page-title', 'Hotel & Vila')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Paket Hotel & Vila</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola paket penginapan: tipe penginapan, galeri foto, tipe kamar, dan fasilitas.</p>
        </div>

        <a href="{{ route('partner.hotel-packages.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition shadow-sm"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Penginapan
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[1050px] w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3 w-[120px]">Thumbnail</th>
                    <th class="px-5 py-3">Nama & Tipe</th>
                    <th class="px-5 py-3 w-[160px]">Harga / Malam</th>
                    <th class="px-5 py-3 w-[160px]">Kamar & Galeri</th>
                    <th class="px-5 py-3 w-[140px]">Status Review</th>
                    <th class="px-5 py-3 text-right w-[180px]">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                @forelse($packages as $p)
                    <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                        <td class="px-5 py-4">
                            <div class="h-16 w-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                                @if(!empty($p->thumbnail_path))
                                    <img src="{{ asset('storage/' . $p->thumbnail_path) }}"
                                         class="h-full w-full object-cover"
                                         alt="{{ $p->title }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <i data-lucide="image" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900 text-sm">{{ $p->title }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200">
                                    {{ $p->property_type ?? 'Hotel' }}
                                </span>
                                @if(!empty($p->label))
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $p->label }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-black text-sky-600 text-sm">
                                Rp {{ number_format($p->price_per_night, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-400">mulai dari / malam</div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1 text-xs">
                                <span class="inline-flex items-center gap-1 font-bold text-slate-700">
                                    <i data-lucide="bed" class="w-3.5 h-3.5 text-sky-500"></i>
                                    {{ $p->rooms ? $p->rooms->count() : 0 }} Tipe Kamar
                                </span>
                                <span class="inline-flex items-center gap-1 font-semibold text-slate-500 text-[11px]">
                                    <i data-lucide="images" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $p->photos ? $p->photos->count() : 0 }} Foto Galeri
                                </span>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            @if($p->partner_review_status === 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-extrabold border bg-amber-50 border-amber-200 text-amber-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Menunggu Review
                                </span>
                            @elseif($p->partner_review_status === 'approved' && $p->is_active)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-extrabold border bg-emerald-50 border-emerald-200 text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif Disetujui
                                </span>
                            @elseif($p->partner_review_status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-extrabold border bg-red-50 border-red-200 text-red-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-extrabold border bg-slate-100 border-slate-200 text-slate-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('partner.hotel-packages.edit', $p->id) }}"
                                   class="inline-flex items-center justify-center gap-1 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 text-sky-600 transition">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    Edit
                                </a>

                                <form action="{{ route('partner.hotel-packages.destroy', $p->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Hapus paket penginapan ini beserta seluruh kamar dan fotonya?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center p-2 rounded-xl text-red-500 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 transition"
                                            title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center bg-sky-50 border-sky-200">
                                <i data-lucide="building" class="w-6 h-6 text-sky-500"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada paket hotel/vila</div>
                            <div class="mt-1 text-sm text-slate-600">Klik "Tambah Penginapan" untuk membuat data baru.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($packages, 'links'))
        <div>
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
