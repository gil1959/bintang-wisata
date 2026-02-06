@extends('user.layouts.app')

@section('title', 'Tabungan Umrah')
@section('page-title', 'Tabungan Umrah')
@section('page-subtitle', 'Status pengajuan tabungan umrah')

@section('content')
    <div class="card p-5 relative overflow-hidden max-w-3xl">
        <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
             style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

        <div class="flex items-start justify-between gap-3 relative">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Status Pengajuan</h2>
                <p class="text-sm text-slate-500 mt-1">Pengajuan kamu sedang diproses admin.</p>
            </div>
            <span class="pill pill-azure shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Verifikasi
            </span>
        </div>

        @if($account->status === 'pending')
            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 relative">
                <div class="font-extrabold text-amber-700">Menunggu Verifikasi Admin</div>
                <div class="text-sm mt-1 text-amber-700">
                    Setelah admin memverifikasi atau menolak, sistem akan mengirim email berisi detailnya.
                </div>
            </div>
        @elseif($account->status === 'suspended')
            <div class="mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 relative">
                <div class="font-extrabold text-rose-700">Akun Disuspend</div>
                <div class="text-sm mt-1 text-rose-700">
                    Silakan hubungi admin untuk informasi lanjutan.
                </div>
            </div>
        @endif

        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4 relative">
            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Nama</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $account->full_name }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">WhatsApp</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">{{ $account->whatsapp }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white">
                <div class="text-xs font-semibold text-slate-500">Jenis</div>
                <div class="mt-1 text-sm font-semibold text-slate-900">
                    {{ $account->saving_type === 'haji_furoda' ? 'Haji Furoda' : 'Umroh Reguler' }}
                </div>
            </div>
        </div>

        <div class="mt-5">
            <a href="{{ route('user.dashboard') }}" class="btn btn-ghost">
                <i data-lucide="arrow-left" class="w-4 h-4" style="color:#0194F3;"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
