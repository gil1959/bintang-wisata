@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="{
    showLoginModal: false,
    selectedUser: null,
    impersonateUrl: '',
    openImpersonate(user, url) {
        this.selectedUser = user;
        this.impersonateUrl = url;
        this.showLoginModal = true;
    }
}">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">
                Ringkasan data, statistik pengunjung, dan jalan pintas manajemen sistem.
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5">
                <div class="text-xs font-extrabold text-emerald-700">{{ session('success') }}</div>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.system.clear-cache') }}"
              onsubmit="return confirm('Bersihkan cache sekarang?')">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold text-white transition shadow-sm"
                    style="background:#ef4444"
                    onmouseover="this.style.background='#dc2626'"
                    onmouseout="this.style.background='#ef4444'">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Clear Cache
            </button>
        </form>
    </div>

    {{-- TOP STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.20) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Pendapatan</div>
                    <div class="mt-2 text-2xl font-extrabold" style="color:#0194F3;">
                       Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Ringkasan pendapatan seluruh transaksi lunas</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="wallet" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.16) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Total Pesanan</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ number_format($totalOrders, 0, ',', '.') }}
                        <span class="text-sm text-slate-500 font-extrabold">Order</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Jumlah order lunas yang tercatat</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="shopping-bag" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition relative overflow-hidden">
            <div class="absolute -top-14 -right-14 w-40 h-40 rounded-full blur-2xl"
                 style="background: radial-gradient(circle, rgba(1,148,243,0.14) 0%, transparent 65%);"></div>

            <div class="flex items-start justify-between relative">
                <div>
                    <div class="text-xs font-extrabold text-slate-500">Paket Aktif</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">
                        {{ number_format($totalPackages, 0, ',', '.') }}
                        <span class="text-sm text-slate-500 font-extrabold">Paket</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Total paket aktif yang ditampilkan</div>
                </div>

                <div class="h-11 w-11 rounded-2xl grid place-items-center border"
                     style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                    <i data-lucide="map" class="w-6 h-6" style="color:#0194F3;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: TRAFIK PENGUNJUNG (UNIK IP) --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4" style="color:#0194F3;"></i>
                    Trafik Pengunjung (Unik IP)
                </h2>
                <div class="text-xs text-slate-500 mt-0.5">Analitik jumlah pengunjung website berdasarkan alamat IP unik harian</div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold border bg-blue-50 border-blue-200" style="color:#0194F3;">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                Realtime Tracking
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            {{-- 3 KARTU STATISTIK PENGUNJUNG --}}
            <div class="lg:col-span-4 flex flex-col justify-between gap-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                    <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Hari Ini</div>
                    <div class="mt-2 text-2xl font-black text-slate-900">
                        {{ number_format($todayVisitors, 0, ',', '.') }}
                        <span class="text-xs text-slate-500 font-extrabold">Pengunjung</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500">Trafik IP unik per hari</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                    <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">7 Hari Terakhir</div>
                    <div class="mt-2 text-2xl font-black" style="color:#0194F3;">
                        {{ number_format($weekVisitors, 0, ',', '.') }}
                        <span class="text-xs text-slate-500 font-extrabold">Pengunjung</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500">Total akumulasi 7 hari</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                    <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Pengunjung</div>
                    <div class="mt-2 text-2xl font-black text-emerald-600">
                        {{ number_format($totalVisitors, 0, ',', '.') }}
                        <span class="text-xs text-slate-500 font-extrabold">Pengunjung</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-500">Sejak sistem mulai tracking</div>
                </div>
            </div>

            {{-- GRAFIK LINE CHART 7 HARI --}}
            <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="text-xs font-extrabold text-slate-700">Statistik Pengunjung 7 Hari Terakhir</div>
                    <div class="text-[11px] font-bold text-slate-400">Diperbarui realtime</div>
                </div>
                <div class="h-56 w-full">
                    <canvas id="visitorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: AKSI CEPAT & PENGGUNA TERBARU --}}
    <div class="grid grid-cols-1 2xl:grid-cols-12 gap-6 mb-6">
        {{-- AKSI CEPAT --}}
        <div class="2xl:col-span-5 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
                    Aksi Cepat
                </h2>
                <span class="text-xs font-extrabold px-3 py-1 rounded-full border shrink-0"
                      style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                    Admin Tools
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('admin.tour-packages.index') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="map" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Paket Wisata</div>
                            <div class="text-[11px] text-slate-500 truncate">Kelola paket tour</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.rent-car-packages.index') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="car" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Rental Mobil</div>
                            <div class="text-[11px] text-slate-500 truncate">Kelola unit armada</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="shopping-bag" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Pesanan Masuk</div>
                            <div class="text-[11px] text-slate-500 truncate">Pantau pesanan customer</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.google-indexing.index') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="globe" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Google Indexing</div>
                            <div class="text-[11px] text-slate-500 truncate">Submit massal URL</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.settings.general') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="settings" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Pengaturan Web</div>
                            <div class="text-[11px] text-slate-500 truncate">Atur profil dan SEO</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.payments.index') }}"
                   class="rounded-2xl border border-slate-200 p-3.5 hover:shadow-md transition bg-white group">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl grid place-items-center border shrink-0 transition group-hover:scale-105"
                             style="background: rgba(1,148,243,0.10); border-color: rgba(1,148,243,0.22);">
                            <i data-lucide="credit-card" class="w-4 h-4" style="color:#0194F3;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-extrabold text-slate-900 truncate">Pembayaran</div>
                            <div class="text-[11px] text-slate-500 truncate">Rekening dan gateway</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="mt-4 text-xs text-slate-500">
                Pilih salah satu menu aksi cepat untuk mempermudah operasional website harian Anda.
            </div>
        </div>

        {{-- PENGGUNA TERBARU & LOGIN AS --}}
        <div class="2xl:col-span-7 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4" style="color:#0194F3;"></i>
                        Pengguna Terbaru
                    </h2>
                    <div class="text-xs text-slate-500 mt-0.5">Daftar pengguna terdaftar terbaru dan opsi login sebagai pengguna</div>
                </div>

                <a href="{{ route('admin.users.index') }}"
                   class="text-xs font-extrabold hover:underline"
                   style="color:#0194F3;">
                    Semua Pengguna
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider font-extrabold">
                            <th class="py-2.5 px-3">Nama / Email</th>
                            <th class="py-2.5 px-3">Role</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Dibuat</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($latestUsers as $user)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3">
                                    <div class="font-extrabold text-slate-900 truncate max-w-[160px]">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-500 truncate max-w-[160px]">{{ $user->email }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-blue-50 text-[#0194F3] border border-blue-200 uppercase">
                                        {{ $user->getRoleNames()->first() ?? 'user' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if($user->email_verified_at || $user->is_verified)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                            Belum
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                    {{ $user->created_at ? $user->created_at->diffForHumans() : 'Baru saja' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <button type="button"
                                            @click="openImpersonate({{ json_encode(['name' => $user->name, 'email' => $user->email, 'role' => $user->getRoleNames()->first() ?? 'user']) }}, '{{ route('admin.users.impersonate', $user->id) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold text-white transition shadow-sm"
                                            style="background:#0194F3;"
                                            onmouseover="this.style.background='#0186DB'"
                                            onmouseout="this.style.background='#0194F3'">
                                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                                        Login As
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">
                                    Belum ada data pengguna yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL LOGIN AS (IMPERSONATE) --}}
    <div x-show="showLoginModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showLoginModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showLoginModal = false"
                 class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm"></div>

            <div x-show="showLoginModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-slate-200">

                <div class="flex items-center gap-3 mb-4">
                    <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                         style="background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.22);">
                        <i data-lucide="user-check" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Konfirmasi Login Sebagai Pengguna</h3>
                        <div class="text-xs text-slate-500">Fitur penyamaran akun (Impersonate)</div>
                    </div>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mb-4 text-xs text-amber-900">
                    <div class="font-extrabold mb-1">Perhatian Penting</div>
                    <p class="leading-relaxed">
                        Anda akan masuk sementara sebagai pengguna ini untuk memeriksa tampilan atau pesanan. Anda dapat kembali ke panel admin kapan saja melalui tombol kuning di bagian paling atas layar.
                    </p>
                </div>

                <template x-if="selectedUser">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 mb-5">
                        <div class="text-xs font-extrabold text-slate-800" x-text="selectedUser.name"></div>
                        <div class="text-[11px] text-slate-500 font-mono mt-0.5" x-text="selectedUser.email"></div>
                        <div class="mt-2">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-blue-50 text-[#0194F3] border border-blue-200 uppercase"
                                  x-text="selectedUser.role"></span>
                        </div>
                    </div>
                </template>

                <form :action="impersonateUrl" method="POST" class="flex items-center justify-end gap-2">
                    @csrf
                    <button type="button"
                            @click="showLoginModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-extrabold text-white transition shadow-sm"
                            style="background:#0194F3;"
                            onmouseover="this.style.background='#0186DB'"
                            onmouseout="this.style.background='#0194F3'">
                        <i data-lucide="log-in" class="w-3.5 h-3.5"></i>
                        Masuk Sebagai Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('visitorChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pengunjung Unik',
                    data: @json($chartData),
                    borderColor: '#0194F3',
                    backgroundColor: 'rgba(1, 148, 243, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0194F3',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' Pengunjung';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
