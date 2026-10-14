@extends('partner.layouts.app')

@section('title', 'Edit Paket Travel & Bus')
@section('page-title', 'Edit Paket Travel & Bus')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('partner.travel-bus.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-slate-800">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
        <h2 class="text-xl font-extrabold text-slate-900 mt-2">Edit Jadwal: {{ $package->armada_name }}</h2>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 font-bold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('partner.travel-bus.update', $package) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('partner.travel-bus._form')
    </form>

    {{-- Hidden form khusus delete photo (di luar form utama agar tidak terjadi nested form) --}}
    <form id="deletePhotoForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        window.__bwDeletePhoto = function(actionUrl) {
            if (!confirm('Hapus foto galeri ini?')) return;
            const f = document.getElementById('deletePhotoForm');
            f.action = actionUrl;
            f.submit();
        }
    </script>
</div>
@endsection
