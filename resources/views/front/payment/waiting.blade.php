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
                    @php
                $msg = "Halo admin, saya mau konfirmasi pembayaran.\n"
                    . "Invoice: {$order->invoice_number}\n"
                    . "Status: {$order->payment_status}\n"
                    . "Terima kasih.";
                $waLink = (!empty($wa)) ? ("https://wa.me/" . $wa . "?text=" . urlencode($msg)) : null;
            @endphp

            @if($waLink)
                <a href="{{ $waLink }}" target="_blank"
                class="inline-flex items-center justify-center w-full rounded-xl px-4 py-3 font-extrabold text-white transition"
                style="background:#0194F3;"
                onmouseover="this.style.background='#47a4e2'"
                onmouseout="this.style.background='#1476b8'">
                    Konfirmasi via WhatsApp
                </a>
            @else
                <p class="text-xs text-gray-400">
                    Nomor WhatsApp admin belum diset di Pengaturan (Settings).
                </p>
            @endif

    </div>
</div>
@endsection
