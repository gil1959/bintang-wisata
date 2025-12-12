@extends('layouts.front')

@section('title', 'Menunggu Verifikasi Pembayaran')

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white shadow rounded-xl p-6 text-center space-y-4">
        <h1 class="text-2xl font-bold">Pembayaran Sedang Dicek</h1>

        <p class="text-gray-600">
            Terima kasih. Bukti transfer untuk invoice
            <b>{{ $order->invoice_number }}</b> sudah kami terima.
        </p>

        <p class="text-sm text-gray-500">
            Admin akan memverifikasi pembayaran kamu. Status pesanan saat ini:
            <b>{{ $order->payment_status }}</b>.
        </p>
    </div>
</div>
@endsection
