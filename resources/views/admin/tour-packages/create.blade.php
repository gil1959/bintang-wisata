@extends('layouts.admin')

@section('title', 'Tambah Paket Wisata')
@section('page-title', 'Tambah Paket Wisata')

@section('content')

    {{-- BLOK ERROR GLOBAL – TARUH DI SINI --}}
    @if ($errors->any())
        <div class="bg-red-500 text-white p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tour-packages.store') }}"
          method="POST" enctype="multipart/form-data">

        @csrf

        @include('admin.tour-packages._form')

    </form>

@endsection
