@extends('partner.layouts.app')

@section('title', 'Paket Travel & Bus')
@section('page-title', 'Paket Travel & Bus')

@section('content')
<div class="space-y-5">
    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Paket Travel & Bus Saya</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola jadwal keberangkatan, armada, dan harga tiket travel Anda.</p>
        </div>

        <a href="{{ route('partner.travel-bus.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition"
           style="background:#0194F3;">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Jadwal Baru
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3 w-[120px]">Thumbnail</th>
                    <th class="px-5 py-3">Armada & Rute</th>
                    <th class="px-5 py-3 w-[140px]">Jadwal</th>
                    <th class="px-5 py-3 w-[140px]">Harga</th>
                    <th class="px-5 py-3 w-[120px]">Status</th>
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
                                         alt="{{ $p->armada_name }}">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-slate-400">
                                        <i data-lucide="bus" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900">{{ $p->armada_name }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700">
                                    {{ $p->travel_type }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 font-medium">{{ $p->armada_type }}</div>
                            <div class="text-xs text-slate-600 mt-1 flex items-center gap-1 font-semibold">
                                <span>{{ $p->departure_location }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400"></i>
                                <span>{{ $p->arrival_location }}</span>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-800">
                                {{ $p->formatted_departure_time }} - {{ $p->formatted_arrival_time }}
                            </div>
                            <div class="text-xs text-slate-500">{{ $p->formatted_duration }}</div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-amber-600">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-400">per pax</div>
                        </td>

                        <td class="px-5 py-4">
                            @if($p->partner_review_status === 'approved')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Disetujui
                                </span>
                            @elseif($p->partner_review_status === 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu Review
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('partner.travel-bus.edit', $p) }}"
                                   class="px-3 py-1.5 rounded-xl border border-slate-200 font-extrabold text-slate-700 hover:bg-slate-50">
                                    Edit
                                </a>
                                <form action="{{ route('partner.travel-bus.destroy', $p) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus paket travel/bus ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-xl font-extrabold text-white"
                                            style="background:#ef4444;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                            Belum ada paket Travel & Bus yang dibuat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200">
            {{ $packages->links() }}
        </div>
    </div>
</div>
@endsection
