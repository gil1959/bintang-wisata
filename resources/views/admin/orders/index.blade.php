@extends('layouts.admin')

@section('page-title', 'Orders')

@section('content')
<div class="p-5">

    <h2 class="text-2xl font-bold mb-4">Orders</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @php
        $q = request('q');
        $queryParams = array_filter(['q' => $q]);
    @endphp

    {{-- FILTER TABS --}}
    <div class="mb-3">
        <form class="mb-3" method="GET" action="{{ url()->current() }}">
    <div class="input-group" style="max-width: 520px;">
        <input
            type="text"
            name="q"
            class="form-control"
            placeholder="Cari berdasarkan nama customer atau invoice..."
            value="{{ $q }}"
            autocomplete="off"
        />

        <button class="btn btn-outline-secondary" type="submit">Cari</button>

        @if(!empty($q))
            <a class="btn btn-outline-danger" href="{{ url()->current() }}">Reset</a>
        @endif
    </div>
</form>

    <a href="{{ route('admin.orders.index', $queryParams) }}"
       class="btn btn-sm {{ ($currentFilter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
        Semua
    </a>

    <a href="{{ route('admin.orders.approved', $queryParams) }}"
       class="btn btn-sm {{ ($currentFilter ?? '') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
        Approved
    </a>

    <a href="{{ route('admin.orders.rejected', $queryParams) }}"
       class="btn btn-sm {{ ($currentFilter ?? '') === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
        Rejected
    </a>
</div>

    <table class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Tipe</th>
                <th>Produk</th>
                <th>Total</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Dibuat</th>
                <th style="width: 170px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->invoice_number }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ strtoupper($order->type) }}</td>
                <td>{{ $order->product_name }}</td>
                <td>Rp {{ number_format($order->final_price,0,',','.') }}</td>
                <td>{{ $order->payment_status }}</td>
                <td>{{ $order->order_status }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}"
                       class="btn btn-sm btn-primary">
                        Detail
                    </a>

                    <form action="{{ route('admin.orders.destroy', $order) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Yakin hapus order ini? Tindakan tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">Belum ada order.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
