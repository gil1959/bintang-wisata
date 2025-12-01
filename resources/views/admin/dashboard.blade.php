@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Total Pendapatan</div>
            <div class="mt-2 text-2xl font-bold text-emerald-600">
                Rp 0
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Total Pesanan</div>
            <div class="mt-2 text-2xl font-bold text-indigo-600">
                0 Order
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <div class="text-sm text-gray-500">Paket Aktif</div>
            <div class="mt-2 text-2xl font-bold text-teal-600">
                0 Paket
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
        <h2 class="text-base font-semibold mb-2">Data Order Masuk</h2>
        <p class="text-sm text-gray-500">Belum ada pesanan masuk.</p>
    </div>
@endsection
