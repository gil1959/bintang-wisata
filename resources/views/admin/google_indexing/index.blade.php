@extends('layouts.admin')

@section('title', 'Google Indexing API')
@section('page-title', 'Google Indexing API')

@section('content')
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="globe" class="w-6 h-6" style="color:#0194F3;"></i>
                Google Indexing API Bulk Manager
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Input puluhan hingga ratusan URL sekaligus untuk dipublikasikan atau dihapus dari indeks pencarian Google secara instan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold border"
                  style="background:rgba(1,148,243,0.08); border-color:rgba(1,148,243,0.22); color:#0194F3;">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                Official Google API
            </span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
        <div class="font-extrabold text-emerald-700">Sukses</div>
        <div class="text-sm mt-1 text-emerald-700">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
        <div class="font-extrabold text-rose-700">Perhatian</div>
        <div class="text-sm mt-1 text-rose-700">{{ session('error') }}</div>
    </div>
@endif

{{-- SECTION 1: STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Disubmit --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total URL Disubmit</div>
        <div class="mt-2 text-3xl font-extrabold text-slate-900">
            {{ number_format($stats['total'] ?? 0, 0, ',', '.') }}
        </div>
        <div class="mt-2 inline-flex items-center gap-1 text-xs font-extrabold px-2 py-0.5 rounded-lg bg-blue-50" style="color:#0194F3;">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
            Akumulasi Pengiriman
        </div>
    </div>

    {{-- Sukses --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="absolute top-4 right-4 h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
        </div>
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Sukses (200 OK)</div>
        <div class="mt-2 text-3xl font-extrabold text-emerald-600">
            {{ number_format($stats['success'] ?? 0, 0, ',', '.') }}
        </div>
        <div class="mt-2 text-xs text-slate-500">
            Tingkat keberhasilan: <span class="font-extrabold text-emerald-600">{{ $stats['success_rate'] ?? 0 }}%</span>
        </div>
    </div>

    {{-- Gagal --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="absolute top-4 right-4 h-10 w-10 rounded-xl bg-rose-50 border border-rose-100 grid place-items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
        </div>
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Gagal / Ditolak</div>
        <div class="mt-2 text-3xl font-extrabold text-rose-600">
            {{ number_format($stats['failed'] ?? 0, 0, ',', '.') }}
        </div>
        <div class="mt-2 text-xs text-slate-500">
            Status 403, 429, atau kendala izin
        </div>
    </div>

    {{-- Status Mode --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="absolute top-4 right-4 h-10 w-10 rounded-xl grid place-items-center"
             style="background:rgba(1,148,243,0.08); border: 1px solid rgba(1,148,243,0.2);">
            <i data-lucide="key" class="w-5 h-5" style="color:#0194F3;"></i>
        </div>
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Metode Otentikasi</div>
        <div class="mt-2 text-xl font-extrabold text-slate-900">
            @if($authMode === 'oauth')
                <span style="color:#0194F3;">OAuth 2.0</span>
            @else
                <span class="text-indigo-600">Service Account</span>
            @endif
        </div>
        <div class="mt-2 text-xs text-slate-500">
            {{ $hasJson ? 'Kredensial aktif di server' : 'Belum ada kredensial' }}
        </div>
    </div>
</div>

{{-- SECTION 2: CHART 7 HARI --}}
<div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-4 h-4" style="color:#0194F3;"></i>
                Statistik Pengiriman URL 7 Hari Terakhir
            </h2>
            <div class="text-xs text-slate-500 mt-0.5">Grafik perbandingan pengiriman URL yang berhasil dan gagal</div>
        </div>
        <span class="text-xs font-extrabold px-3 py-1 rounded-full border bg-slate-50 text-slate-600 border-slate-200">
            Mingguan
        </span>
    </div>
    <div class="h-64 w-full">
        <canvas id="indexingChart"></canvas>
    </div>
</div>

{{-- SECTION 3: INPUT AREA & RUNNER --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    {{-- KOLOM KIRI (70%): Konfigurasi & Form URLs --}}
    <div class="lg:col-span-8 space-y-6">
        {{-- Auth Mode Tabs --}}
        <div x-data="{ authTab: '{{ $authMode }}' }" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex border-b border-slate-200 bg-slate-50">
                <button type="button"
                        @click="authTab = 'json'"
                        :class="authTab === 'json' ? 'border-b-2 font-extrabold bg-white text-indigo-700 border-indigo-600' : 'text-slate-600 hover:text-slate-900 font-bold'"
                        class="px-5 py-3 text-xs transition">
                    Mode Service Account JSON (Rekomendasi)
                </button>
                <button type="button"
                        @click="authTab = 'oauth'"
                        :class="authTab === 'oauth' ? 'border-b-2 font-extrabold bg-white text-[#0194F3] border-[#0194F3]' : 'text-slate-600 hover:text-slate-900 font-bold'"
                        class="px-5 py-3 text-xs transition">
                    Mode OAuth 2.0
                </button>
            </div>

            <div class="p-5">
                {{-- JSON Mode Content --}}
                <div x-show="authTab === 'json'">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                <i data-lucide="file-code" class="w-4 h-4 text-amber-500"></i>
                                File Kunci Privat JSON Google Cloud
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Digunakan untuk koneksi server ke server Google Indexing API tanpa perlu login manual berulang kali.
                            </p>
                        </div>
                        @if($hasJson)
                            <span class="inline-flex items-center gap-1 text-xs font-extrabold px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                JSON Aktif
                            </span>
                        @endif
                    </div>

                    <form action="{{ route('admin.google-indexing.upload_json') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="file" name="json_file" accept=".json" required
                               class="flex-1 text-xs text-slate-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-50 file:text-[#0194F3] hover:file:bg-blue-100 rounded-xl border border-slate-200 bg-slate-50 p-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold text-white transition shrink-0"
                                style="background:#0194F3;"
                                onmouseover="this.style.background='#0186DB'"
                                onmouseout="this.style.background='#0194F3'">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            Unggah JSON
                        </button>
                    </form>

                    @if($hasJson && $serviceAccountEmail)
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
                            <div class="font-bold text-slate-800">Email Service Account Terdeteksi:</div>
                            <div class="mt-1 font-mono text-slate-700 select-all bg-white p-2 rounded-lg border border-slate-200 truncate">
                                {{ $serviceAccountEmail }}
                            </div>
                            <div class="mt-2 text-slate-500">
                                Pastikan email di atas telah ditambahkan sebagai <span class="font-bold text-slate-800">Pemilik (Owner)</span> pada Google Search Console domain ini.
                            </div>
                        </div>
                    @endif
                </div>

                {{-- OAuth Mode Content --}}
                <div x-show="authTab === 'oauth'" x-cloak>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 mb-2">
                        <i data-lucide="user-check" class="w-4 h-4" style="color:#0194F3;"></i>
                        Hubungkan Akun Google Search Console via OAuth 2.0
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">
                        Opsi alternatif jika Anda tidak ingin menggunakan Service Account JSON.
                    </p>
                    <a href="{{ route('admin.google-indexing.oauth') }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-extrabold text-white transition"
                       style="background:#0194F3;">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Masuk dengan Google Search Console
                    </a>
                </div>
            </div>
        </div>

        {{-- Bulk URL Form --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i data-lucide="list-plus" class="w-4 h-4" style="color:#0194F3;"></i>
                        Daftar URL yang Ingin Dikirim
                    </h3>
                    <div class="text-xs text-slate-500 mt-0.5">Satu baris untuk setiap satu alamat URL lengkap</div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 cursor-pointer transition">
                        <i data-lucide="file-text" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                        Import TXT atau CSV
                        <input type="file" id="importTxtBtn" accept=".txt,.csv" class="hidden">
                    </label>
                    <button type="button" id="btnClearUrls" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-600 transition">
                        Bersihkan
                    </button>
                </div>
            </div>

            <textarea id="urlsToSubmit" rows="8"
                      class="w-full rounded-2xl border border-slate-200 p-3.5 text-xs font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3] transition"
                      placeholder="https://bintangwisataholiday.com/paket/tour-raja-ampat&#10;https://bintangwisataholiday.com/paket/tour-labuan-bajo&#10;https://bintangwisataholiday.com/artikel/panduan-liburan-bali"></textarea>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4 pt-4 border-t border-slate-100">
                <div class="text-xs font-bold text-slate-500">
                    Total URL terdeteksi: <span id="urlCount" class="text-xl font-black" style="color:#0194F3;">0</span>
                </div>

                <div class="flex items-center gap-2">
                    <label for="actionType" class="text-xs font-bold text-slate-600 shrink-0">Tipe Aksi:</label>
                    <select id="actionType" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-extrabold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0194F3]/20 focus:border-[#0194F3]">
                        <option value="URL_UPDATED">Publikasikan atau Update Indeks (URL_UPDATED)</option>
                        <option value="URL_DELETED">Hapus Dari Indeks (URL_DELETED)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-center gap-3 mb-2">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <div class="text-xs font-extrabold text-amber-900">
                        Jeda Antar Request API (Mencegah Batasan Server 429): <span id="delayValueLabel" class="text-amber-700">500ms</span>
                    </div>
                </div>
                <input type="range" id="delaySlider" min="100" max="2000" step="100" value="500"
                       class="w-full h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer">
            </div>

            <div class="mt-5">
                <button type="button" id="btnStartBulk"
                        class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-2xl text-base font-extrabold text-white shadow-lg shadow-blue-500/25 transition"
                        style="background: linear-gradient(135deg, #0194F3 0%, #0077c5 100%);"
                        onmouseover="this.style.opacity='0.92'"
                        onmouseout="this.style.opacity='1'">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    JALANKAN INDEXING MASSAL SEKARANG
                </button>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN (30%): Progress Runner & Kuota --}}
    <div class="lg:col-span-4 space-y-6">
        {{-- Status Eksekusi Batch --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-slate-100" id="progressTrack">
                <div class="h-full w-0 transition-all duration-300" id="progressBar" style="background:#0194F3;"></div>
            </div>

            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 mb-4">
                <i data-lucide="activity" class="w-4 h-4" style="color:#0194F3;" id="statusIcon"></i>
                Status Eksekusi Batch
            </h3>

            <div class="flex items-center justify-between text-xs font-extrabold text-slate-500 mb-2">
                <span id="statusText">Menunggu Antrean...</span>
                <span id="progressPercentage" style="color:#0194F3;">0%</span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-center">
                    <div class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider">Proses Sukses</div>
                    <div class="mt-1 text-2xl font-black text-emerald-600" id="countSuccess">0</div>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-3 text-center">
                    <div class="text-[10px] font-extrabold text-rose-700 uppercase tracking-wider">Proses Gagal</div>
                    <div class="mt-1 text-2xl font-black text-rose-600" id="countFail">0</div>
                </div>
            </div>

            <button type="button" id="btnStopBulk" disabled
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-extrabold border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed transition">
                <i data-lucide="square" class="w-3.5 h-3.5"></i>
                Hentikan Proses
            </button>
        </div>

        {{-- Kuota & Keamanan Card --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 mb-3">
                <i data-lucide="shield" class="w-4 h-4 text-emerald-600"></i>
                Kuota dan Keamanan
            </h3>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 mb-4">
                <div class="text-xs font-bold text-slate-600 mb-1">Sisa Kuota Hari Ini:</div>
                @php
                    $used = $stats['quota_used'] ?? 0;
                    $limit = $stats['quota_limit'] ?? 200;
                    $remaining = max(0, $limit - $used);
                    $percentage = $limit > 0 ? min(100, ($used / $limit) * 100) : 0;
                @endphp
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl font-black {{ $remaining < 20 ? 'text-rose-600' : 'text-emerald-600' }}" id="remainingQuotaDisplay">
                        {{ $remaining }}
                    </span>
                    <span class="text-xs font-extrabold text-slate-400">/ {{ $limit }} URL</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2 mb-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-300 {{ $remaining < 20 ? 'bg-rose-500' : 'bg-emerald-500' }}"
                         id="quotaProgressBar"
                         style="width: {{ $percentage }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Google membatasi maksimal 200 request URL per hari untuk setiap akun atau proyek. Kuota direset otomatis oleh Google setiap pergantian hari.
                </p>
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed text-justify">
                Seluruh otentikasi dan pengiriman URL diproses secara aman langsung melalui server menuju Google API Endpoint resmi tanpa mengekspos kredensial rahasia ke publik.
            </p>
        </div>
    </div>
</div>

{{-- SECTION 4: LOG TABLE --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
        <div>
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4" style="color:#0194F3;"></i>
                Riwayat Pengiriman URL Terakhir
            </h3>
            <div class="text-xs text-slate-500 mt-0.5">Daftar riwayat respon dari Google Indexing API</div>
        </div>
        <button type="button" onclick="window.location.reload()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-extrabold transition"
                style="color:#0194F3;">
            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
            Segarkan Data
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="border-b border-slate-200 bg-white text-slate-500 uppercase tracking-wider font-extrabold">
                    <th class="py-3 px-4">URL</th>
                    <th class="py-3 px-4">Aksi</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Pesan Respon</th>
                </tr>
            </thead>
            <tbody id="logTableBody" class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono max-w-xs truncate text-slate-800" title="{{ $log->url }}">
                            {{ $log->url }}
                        </td>
                        <td class="py-3 px-4 font-bold">
                            @if($log->action_type === 'URL_DELETED')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">HAPUS</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-50 text-[#0194F3] border border-blue-200">UPDATE</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($log->status_code == 200)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">200 OK</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">{{ $log->status_code ?? 500 }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                            {{ $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 max-w-sm truncate" title="{{ $log->response_message }}">
                            {{ $log->response_message }}
                        </td>
                    </tr>
                @empty
                    <tr id="emptyLogRow">
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            Belum ada riwayat pengiriman URL.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==== CHART.JS 7 HARI TERAKHIR ====
    const ctx = document.getElementById('indexingChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Sukses (200 OK)',
                        data: @json($chartSuccess),
                        backgroundColor: '#10B981',
                        borderRadius: 6
                    },
                    {
                        label: 'Gagal / Ditolak',
                        data: @json($chartFailed),
                        backgroundColor: '#EF4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Nunito' } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11, family: 'Nunito' }, stepSize: 1, beginAtZero: true }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 12, font: { size: 12, family: 'Nunito', weight: 'bold' } }
                    }
                }
            }
        });
    }

    // ==== BULK MANAGER RUNNER ====
    const textareaUrls = document.getElementById('urlsToSubmit');
    const urlCountDisplay = document.getElementById('urlCount');
    const delaySlider = document.getElementById('delaySlider');
    const delayValueLabel = document.getElementById('delayValueLabel');
    const importBtn = document.getElementById('importTxtBtn');
    const clearBtn = document.getElementById('btnClearUrls');
    const btnStart = document.getElementById('btnStartBulk');
    const btnStop = document.getElementById('btnStopBulk');
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    const statusText = document.getElementById('statusText');
    const countSuccess = document.getElementById('countSuccess');
    const countFail = document.getElementById('countFail');
    const actionType = document.getElementById('actionType');
    const logTableBody = document.getElementById('logTableBody');
    const emptyLogRow = document.getElementById('emptyLogRow');
    const remainingQuotaDisplay = document.getElementById('remainingQuotaDisplay');
    const quotaProgressBar = document.getElementById('quotaProgressBar');

    let stopFlag = false;
    let remainingQuota = {{ (int)$stats['quota_remaining'] }};

    function updateUrlCount() {
        const text = textareaUrls.value;
        const urls = text.split('\n').map(u => u.trim()).filter(u => u.length > 5);
        urlCountDisplay.innerText = urls.length;
    }

    textareaUrls.addEventListener('input', updateUrlCount);

    delaySlider.addEventListener('input', function () {
        delayValueLabel.innerText = this.value + 'ms';
    });

    clearBtn.addEventListener('click', function () {
        textareaUrls.value = '';
        updateUrlCount();
    });

    importBtn.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (evt) {
            const current = textareaUrls.value.trim();
            textareaUrls.value = current ? current + '\n' + evt.target.result : evt.target.result;
            updateUrlCount();
        };
        reader.readAsText(file);
        this.value = '';
    });

    const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

    btnStop.addEventListener('click', function () {
        stopFlag = true;
        statusText.innerText = 'Menghentikan proses...';
    });

    btnStart.addEventListener('click', async function () {
        if (remainingQuota <= 0) {
            alert('Batas kuota harian 200 request telah tercapai untuk hari ini. Silakan coba kembali besok.');
            return;
        }

        const rawText = textareaUrls.value;
        const urls = rawText.split('\n').map(u => u.trim()).filter(u => u.length > 5);

        if (urls.length === 0) {
            alert('Tidak ada URL yang dimasukkan. Silakan masukkan minimal satu baris URL.');
            return;
        }

        if (urls.length > remainingQuota) {
            if (!confirm('Jumlah URL (' + urls.length + ') melebihi sisa kuota hari ini (' + remainingQuota + '). Tetap lanjutkan hingga kuota habis?')) {
                return;
            }
        } else {
            if (!confirm('Jalankan proses pengiriman ' + urls.length + ' URL sekarang?')) {
                return;
            }
        }

        stopFlag = false;
        btnStart.disabled = true;
        btnStart.classList.add('opacity-60', 'cursor-not-allowed');
        btnStop.disabled = false;
        btnStop.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
        btnStop.classList.add('bg-rose-600', 'text-white', 'hover:bg-rose-700');
        textareaUrls.disabled = true;

        let success = 0;
        let fail = 0;
        const total = urls.length;
        const delay = parseInt(delaySlider.value);
        const action = actionType.value;

        for (let i = 0; i < total; i++) {
            if (stopFlag) {
                statusText.innerText = 'Dihentikan oleh pengguna.';
                break;
            }

            const currentUrl = urls[i];
            statusText.innerText = 'Memproses [' + (i + 1) + '/' + total + ']: ' + currentUrl.substring(0, 32) + '...';

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('urls', currentUrl);
                formData.append('action_type', action);

                const res = await fetch('{{ route("admin.google-indexing.submit") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    success++;
                    countSuccess.innerText = success;
                } else {
                    fail++;
                    countFail.innerText = fail;
                }

                if (typeof data.quota_remaining !== 'undefined') {
                    remainingQuota = data.quota_remaining;
                    remainingQuotaDisplay.innerText = remainingQuota;
                    const used = 200 - remainingQuota;
                    quotaProgressBar.style.width = Math.min(100, (used / 200) * 100) + '%';
                }

                // Tambahkan baris baru ke tabel log
                if (emptyLogRow) emptyLogRow.remove();
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition bg-emerald-50/30';
                tr.innerHTML = `
                    <td class="py-3 px-4 font-mono max-w-xs truncate text-slate-800" title="${currentUrl}">${currentUrl}</td>
                    <td class="py-3 px-4 font-bold">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold ${action === 'URL_DELETED' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-[#0194F3] border border-blue-200'}">
                            ${action === 'URL_DELETED' ? 'HAPUS' : 'UPDATE'}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold ${data.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'}">
                            ${data.status_code || (data.success ? '200 OK' : 'ERR')}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">Baru saja</td>
                    <td class="py-3 px-4 text-slate-600 max-w-sm truncate" title="${data.message || ''}">${data.message || ''}</td>
                `;
                logTableBody.insertBefore(tr, logTableBody.firstChild);

            } catch (err) {
                fail++;
                countFail.innerText = fail;
            }

            const percent = Math.round(((i + 1) / total) * 100);
            progressBar.style.width = percent + '%';
            progressPercentage.innerText = percent + '%';

            if (i < total - 1 && !stopFlag) {
                await sleep(delay);
            }
        }

        if (!stopFlag) {
            statusText.innerText = 'Selesai: ' + success + ' sukses, ' + fail + ' gagal.';
        }

        btnStart.disabled = false;
        btnStart.classList.remove('opacity-60', 'cursor-not-allowed');
        btnStop.disabled = true;
        btnStop.classList.remove('bg-rose-600', 'text-white', 'hover:bg-rose-700');
        btnStop.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
        textareaUrls.disabled = false;
    });
});
</script>
@endpush
