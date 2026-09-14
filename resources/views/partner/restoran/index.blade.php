@extends('partner.layouts.app')

@section('title', 'Restoran')
@section('page-title', 'Restoran')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Restoran</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola daftar cabang restoran dan menu.</p>
        </div>

        <a href="{{ route('partner.restoran-packages.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-extrabold text-white transition shadow-sm"
           style="background:#0194F3;"
           onmouseover="this.style.background='#0186DB'"
           onmouseout="this.style.background='#0194F3'">
            <i data-lucide="plus" class="w-4 h-4"></i>
            + Cabang
        </a>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full text-left">
                <thead class="bg-slate-50">
                <tr class="text-xs font-extrabold text-slate-600">
                    <th class="px-5 py-3 w-[140px]">Thumbnail</th>
                    <th class="px-5 py-3">Title &amp; Cabang</th>
                    <th class="px-5 py-3 w-[180px]">Kontak CS</th>
                    <th class="px-5 py-3 w-[180px]">Status</th>
                    <th class="px-5 py-3 text-right w-[190px]">Actions</th>
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
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <div class="font-extrabold text-slate-900">{{ $p->title }}</div>
                            <div class="text-xs text-slate-500">/{{ $p->slug }}</div>
                        </td>

                        <td class="px-5 py-4">
                            @if(!empty($p->cs_contact))
                                <span class="font-semibold text-slate-800 inline-flex items-center gap-1.5">
                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-sky-500"></i>
                                    {{ $p->cs_contact }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">Default</span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if($p->partner_review_status === 'pending')
                                @php
                                    $adminWaRaw = \App\Models\Setting::getValue('whatsapp_number', \App\Models\Setting::getValue('footer_whatsapp', '628111111752'));
                                    $adminWa = preg_replace('/\D+/', '', (string)$adminWaRaw);
                                    if (!empty($adminWa) && str_starts_with($adminWa, '0')) {
                                        $adminWa = '62' . substr($adminWa, 1);
                                    }
                                    $partnerName = auth()->user()->name ?? 'Partner';
                                    $waText = "Halo Admin Bintang Wisata, saya dari partner {$partnerName}. Saya ingin konfirmasi pendaftaran cabang restoran \"{$p->title}\" (ID: #{$p->id}) yang saat ini berstatus Menunggu Review agar dapat diperiksa dan disetujui (ACC). Terima kasih!";
                                    $waLink = !empty($adminWa) ? "https://wa.me/{$adminWa}?text=" . urlencode($waText) : null;
                                @endphp
                                <div>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-extrabold border bg-amber-50 border-amber-200 text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Review
                                    </span>
                                    @if($waLink)
                                        <div class="mt-2">
                                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100 hover:border-emerald-400 transition shadow-sm"
                                               title="Hubungi Admin untuk konfirmasi ACC cabang restoran ini">
                                                <i data-lucide="message-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                <span>Hubungi Admin (ACC)</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
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
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('partner.restoran-packages.edit', $p->id) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold border border-slate-200 bg-white hover:bg-slate-50 transition">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:#0194F3;"></i>
                                    Edit
                                </a>

                                <form action="{{ route('partner.restoran-packages.destroy', $p->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this package?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold text-white transition"
                                            style="background:#ef4444"
                                            onmouseover="this.style.background='#dc2626'"
                                            onmouseout="this.style.background='#ef4444'">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl border grid place-items-center"
                                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22);">
                                 <i data-lucide="utensils" class="w-6 h-6" style="color:#0194F3;"></i>
                            </div>
                            <div class="mt-3 font-extrabold text-slate-900">Belum ada cabang restoran</div>
                            <div class="mt-1 text-sm text-slate-600">Klik “+ Cabang” untuk mulai menambahkan cabang restoran baru.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination kalau paginate --}}
    @if(method_exists($packages, 'links'))
        <div>
            {{ $packages->links() }}
        </div>
    @endif

</div>
@endsection
