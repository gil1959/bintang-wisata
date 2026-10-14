@extends('layouts.admin')

@section('content')
<div class="space-y-5" x-data="{
    showModal: false,
    modalType: '',
    modalName: '',
    modalFormId: '',
    openModal(type, name, formId) {
        this.modalType = type;
        this.modalName = name;
        this.modalFormId = formId;
        this.showModal = true;
    },
    confirmAction() {
        document.getElementById(this.modalFormId).submit();
        this.showModal = false;
    }
}">
    @if(session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
        <div class="font-extrabold">Sukses</div>
        <div class="text-sm mt-1">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
        <div class="font-extrabold">Gagal</div>
        <div class="text-sm mt-1">{{ session('error') }}</div>
    </div>
@endif

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Users Partner</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola akun partner: pajak, suspend, dan edit akun.</p>
        </div>

        <form class="flex gap-2" method="GET">
            <input name="q" value="{{ $q }}" placeholder="Cari nama/email/telepon"
                   class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 bg-white">
            <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Partner</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Pajak</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Status</th>
                    <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3">
                            <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                            <div class="text-sm text-slate-700 font-semibold">{{ $u->email }}</div>
                            <div class="text-xs text-slate-500">{{ $u->phone ?? '-' }}</div>
                        </td>

                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.partners.users.tax', $u->id) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" step="0.01" name="tax_percent"
                                       value="{{ (float)$u->partner_tax_percent }}"
                                       class="w-28 rounded-2xl border border-slate-200 px-3 py-2 text-sm font-extrabold text-slate-800 bg-white">
                                <button class="px-3 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">
                                    Set
                                </button>
                            </form>
                            <div class="mt-1 text-xs text-slate-500">Dalam persen (%).</div>
                        </td>

                        <td class="px-4 py-3">
                            @if($u->is_suspended)
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-red-100 text-red-800">SUSPENDED</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-green-100 text-green-800">AKTIF</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-right space-y-2">
                            <div class="flex justify-end gap-2 flex-wrap">
                                @if($u->is_suspended)
                                    <form method="POST" action="{{ route('admin.partners.users.unsuspend', $u->id) }}">
                                        @csrf
                                        <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#16a34a;">
                                            Unsuspend
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.partners.users.suspend', $u->id) }}">
                                        @csrf
                                        <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#dc2626;">
                                            Suspend
                                        </button>
                                    </form>
                                @endif
<form id="impersonate-form-{{ $u->id }}" action="{{ route('admin.users.impersonate', $u) }}" method="POST" class="hidden">
    @csrf
</form>
<button @click="openModal('login', '{{ addslashes($u->name) }}', 'impersonate-form-{{ $u->id }}')"
        class="px-3 py-2 rounded-2xl font-extrabold text-white"
        style="background:#f59e0b;">
    Login As
</button>

<form method="POST" action="{{ route('admin.partners.users.destroy', $u->id) }}"
      onsubmit="return confirm('Yakin hapus user partner ini? Aksi ini tidak bisa dibatalkan.');">
    @csrf
    @method('DELETE')
    <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#111827;">
        Hapus
    </button>
</form>
<a href="{{ route('admin.partners.users.show', $u->id) }}"
   class="px-3 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
   Detail
</a>

                                {{-- mengikuti pola admin existing: edit user pakai menu Users yg sudah ada --}}
                                <a href="{{ route('admin.partners.users.edit', $u->id) }}"
                                   class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold text-slate-800 hover:bg-slate-50">
                                    Edit Akun
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-600" colspan="4">
                            Tidak ada data partner.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
    {{-- POPUP MODAL --}}
    <div x-show="showModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         @keydown.escape.window="showModal = false">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>

        {{-- Modal Card --}}
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
             @click.stop>

            {{-- Icon --}}
            <div class="mx-auto w-14 h-14 rounded-2xl grid place-items-center mb-4"
                 :style="modalType === 'login'
                    ? 'background: rgba(245,158,11,0.12); border: 1.5px solid rgba(245,158,11,0.25);'
                    : 'background: rgba(239,68,68,0.10); border: 1.5px solid rgba(239,68,68,0.25);'">
                <template x-if="modalType === 'login'">
                    <i data-lucide="log-in" style="width:24px; height:24px; color:#f59e0b;"></i>
                </template>
                <template x-if="modalType === 'delete'">
                    <i data-lucide="trash-2" style="width:24px; height:24px; color:#ef4444;"></i>
                </template>
            </div>

            {{-- Title --}}
            <h3 class="text-lg font-extrabold text-slate-900 text-center"
                x-text="modalType === 'login' ? 'Login sebagai User' : 'Hapus User'"></h3>

            {{-- Description --}}
            <p class="mt-2 text-sm text-slate-600 text-center">
                <template x-if="modalType === 'login'">
                    <span>Anda akan masuk ke akun <strong x-text="modalName" class="text-slate-900"></strong>. Lanjutkan?</span>
                </template>
                <template x-if="modalType === 'delete'">
                    <span>User <strong x-text="modalName" class="text-slate-900"></strong> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</span>
                </template>
            </p>

            {{-- Buttons --}}
            <div class="mt-6 flex items-center justify-center gap-3">
                <button @click="showModal = false"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button @click="confirmAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-white transition"
                        :style="modalType === 'login' ? 'background:#f59e0b;' : 'background:#ef4444;'"
                        x-text="modalType === 'login' ? 'Ya, Login' : 'Ya, Hapus'">
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
