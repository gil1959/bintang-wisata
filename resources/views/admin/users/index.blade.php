@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users')

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

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Users</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola akun user (search, lihat detail, edit, hapus)</p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-auto">
            <div class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-80">
                    <input type="text"
                           name="q"
                           value="{{ $q }}"
                           placeholder="Cari nama / email / no hp..."
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:ring-0 focus:outline-none">
                </div>
                <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
                        style="background:#0194F3;">
                    Cari
                </button>
            </div>
        </form>
        <a href="{{ route('admin.users.create') }}"
   class="px-4 py-2.5 rounded-2xl font-extrabold text-white"
   style="background:#0194F3;">
   Tambah User
</a>

    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-extrabold text-slate-600 uppercase">
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">No HP</th>
                        <th class="px-5 py-3">Verified</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="text-sm text-slate-700 hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->email }}</div>
                            </td>
                            <td class="px-5 py-4">
                                {{ $u->phone ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($u->email_verified_at)
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.25); color:#059669;">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border"
                                          style="background: rgba(244,63,94,0.08); border-color: rgba(244,63,94,0.25); color:#e11d48;">
                                        Not Verified
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-slate-700 border border-slate-200 hover:bg-slate-50">
                                        Detail
                                    </a>

                                    {{-- Login as user (hidden form + popup trigger) --}}
                                    <form id="impersonate-form-{{ $u->id }}"
                                          action="{{ route('admin.users.impersonate', $u) }}"
                                          method="POST" class="hidden">
                                        @csrf
                                    </form>
                                    <button @click="openModal('login', '{{ addslashes($u->name) }}', 'impersonate-form-{{ $u->id }}')"
                                            class="px-3 py-2 rounded-xl font-extrabold text-white"
                                            style="background:#f59e0b;">
                                        Login
                                    </button>

                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="px-3 py-2 rounded-xl font-extrabold text-white"
                                       style="background:#0194F3;">
                                        Edit
                                    </a>

                                    {{-- Delete (hidden form + popup trigger) --}}
                                    <form id="delete-form-{{ $u->id }}"
                                          action="{{ route('admin.users.destroy', $u) }}"
                                          method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button @click="openModal('delete', '{{ addslashes($u->name) }}', 'delete-form-{{ $u->id }}')"
                                            class="px-3 py-2 rounded-xl font-extrabold text-white"
                                            style="background:#ef4444;">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                Tidak ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>

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

