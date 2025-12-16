@extends('layouts.front')

@section('title', 'Pembayaran Online ' . $order->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 space-y-6">

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded-xl p-5 space-y-2">
        <h1 class="text-xl font-bold mb-1">Ringkasan Pesanan</h1>
        <p class="text-sm text-gray-600">Invoice: <b>{{ $order->invoice_number }}</b></p>
        <p class="text-sm">{{ strtoupper($order->type) }} - {{ $order->product_name }}</p>
        <p class="text-sm">Atas nama: <b>{{ $order->customer_name }}</b> ({{ $order->customer_email }})</p>
        <div class="flex justify-between mt-3 border-t pt-2">
            <span class="font-semibold">Total yang harus dibayar</span>
            <span class="font-bold text-lg">Rp {{ number_format($order->final_price,0,',','.') }}</span>
        </div>
    </div>

    <div class="bg-white shadow rounded-xl p-5 space-y-4">
        <h2 class="text-lg font-semibold">Pembayaran Online ({{ $gateway->label ?? ucfirst($gateway->name) }})</h2>

        <p class="text-sm text-gray-600">
            Metode dipilih: <b>{{ $gatewayMethodLabel }}</b>
        </p>

        <form method="POST" action="{{ route('payment.gateway.start', $order) }}">
            @csrf
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                Lanjut Bayar
            </button>
        </form>
    </div>

</div>
@endsection
