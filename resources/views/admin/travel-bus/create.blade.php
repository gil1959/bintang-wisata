@extends('layouts.admin')

@section('title', 'Tambah Paket Travel & Bus')
@section('page-title', 'Tambah Paket Travel & Bus')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-5">
        <a href="{{ route('admin.travel-bus.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-slate-800">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
        <h2 class="text-xl font-extrabold text-slate-900 mt-2">Tambah Paket Travel & Bus</h2>
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

    <form action="{{ route('admin.travel-bus.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.travel-bus._form')
    </form>
</div>
@endsection
