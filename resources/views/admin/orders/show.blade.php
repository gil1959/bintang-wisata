@extends('layouts.admin')

@section('page-title', 'Detail Order')

@section('content')
<div class="p-5">
    <h2 class="text-2xl font-bold mb-4">
        Detail Order {{ $order->invoice_number }}
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header fw-semibold">Info Pesanan</div>
                <div class="card-body">
                    <p><b>Tipe:</b> {{ strtoupper($order->type) }}</p>
                    <p><b>Produk:</b> {{ $order->product_name }}</p>
                    <p><b>Customer:</b> {{ $order->customer_name }} ({{ $order->customer_email }})</p>
                    <p><b>Telepon:</b> {{ $order->customer_phone }}</p>
                    <p><b>Status Pembayaran:</b> {{ $order->payment_status }}</p>
                    <p><b>Status Order:</b> {{ $order->order_status }}</p>
                </div>
            </div>

            @php
                $wa = preg_replace('/\D/', '', $order->customer_phone);
                $waText = urlencode("Halo {$order->customer_name}, terkait pesanan {$order->invoice_number} di Bintang Wisata.");
            @endphp

            @if($wa)
                <a href="https://wa.me/{{ $wa }}?text={{ $waText }}"
                   target="_blank"
                   class="btn btn-success mb-3">
                    WhatsApp Customer
                </a>
            @endif

            <div class="d-flex gap-2 mb-3">
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    <button name="action" value="approve"
                            class="btn btn-primary"
                            onclick="return confirm('Setujui order ini?')">
                        Approve
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    <button name="action" value="reject"
                            class="btn btn-warning"
                            onclick="return confirm('Tolak order ini?')">
                        Tolak
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('admin.orders.destroy', $order) }}"
                      onsubmit="return confirm('Yakin hapus order ini? Tindakan tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header fw-semibold">Ringkasan Pembayaran</div>
                <div class="card-body">
                    <p><b>Subtotal:</b> Rp {{ number_format($order->subtotal,0,',','.') }}</p>
                    <p><b>Diskon:</b> Rp {{ number_format($order->discount,0,',','.') }}</p>
                    <p><b>Total:</b> Rp {{ number_format($order->final_price,0,',','.') }}</p>
                    <p><b>Metode:</b> {{ $order->payment_method }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Riwayat Payment</div>
                <div class="card-body">
                    @forelse($order->payments as $pay)
                        <div class="border rounded p-2 mb-2">
                            <div><b>Metode:</b> {{ $pay->method }}</div>
                            <div><b>Amount:</b> Rp {{ number_format($pay->amount,0,',','.') }}</div>
                            <div><b>Status:</b> {{ $pay->status }}</div>
                            @if($pay->proof_image)
                                <div class="mt-1">
                                    <a href="{{ asset('storage/'.$pay->proof_image) }}"
                                       target="_blank">
                                        Lihat Bukti Transfer
                                    </a>
                                </div>
                            @endif
                            @if($pay->gateway_reference)
                                <div><b>Gateway Ref:</b> {{ $pay->gateway_reference }}</div>
                            @endif
                            <div class="text-muted small">
                                {{ $pay->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada data payment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
