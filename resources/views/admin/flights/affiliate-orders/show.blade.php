@extends('layouts.admin')

@section('title', 'Detail Affiliate Tiket Pesawat')
@section('page-title', 'Detail Affiliate Tiket Pesawat')

@php
$meta = is_array($order->meta ?? null) ? $order->meta : [];
$pricing = (array) ($meta['pricing'] ?? []);
$search = (array) ($meta['search'] ?? []);
@endphp

@section('content')
@if(session('success'))
<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
    {{ session('error') }}
</div>
@endif

<div class="max-w-5xl space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Detail Affiliate Tiket Pesawat</h1>
            <p class="mt-1 text-sm text-slate-600">Invoice: {{ $order->invoice_number }}</p>
        </div>
        <a href="{{ route('admin.flights.affiliate-orders.index') }}"
            class="px-4 py-2 rounded-2xl border border-slate-200 font-extrabold hover:bg-slate-50">
            Kembali
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Customer</div>
                <div class="mt-1 font-bold text-slate-900">{{ $order->customer_name }}</div>
                <div class="text-sm text-slate-700">{{ $order->customer_email }}</div>
                <div class="text-sm text-slate-700">{{ $order->customer_phone }}</div>
            </div>

            <div>
                <div class="text-xs font-extrabold text-slate-600 uppercase">Produk</div>
                <div class="mt-1 font-bold text-slate-900">{{ $order->product_name }}</div>
                <div class="text-sm text-slate-700">Type: {{ $order->type }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Harga Supplier</div>
                <div class="mt-2 text-lg font-extrabold text-slate-900">Rp {{ number_format((float) ($pricing['supplier_price'] ?? 0), 0, ',', '.') }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4">
                <div class="text-xs font-extrabold text-slate-600 uppercase">Harga Final Order</div>
                <div class="mt-2 text-lg font-extrabold text-rose-600">Rp {{ number_format((float) $order->final_price, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ !empty($pricing['is_manual']) ? 'Override manual aktif' : 'Default supplier' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <h2 class="font-extrabold text-slate-900">Set Commission</h2>

        <form method="POST" action="{{ route('admin.flights.affiliate-orders.commission', $order->id) }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            @csrf

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Commission Type</label>
                @php($ct = old('affiliate_commission_type', $order->affiliate_commission_type ?? 'fixed'))
                <select name="affiliate_commission_type"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
                    <option value="fixed" {{ $ct === 'fixed' ? 'selected' : '' }}>Fixed (Nominal)</option>
                    <option value="percent" {{ $ct === 'percent' ? 'selected' : '' }}>Percent (%)</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Commission Value</label>
                <input name="affiliate_commission_value" type="number" step="0.01" min="0" required
                    value="{{ old('affiliate_commission_value', (float) ($order->affiliate_commission_value ?? ($order->affiliate_commission_amount ?? 0))) }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
            </div>

            <div>
                <label class="text-xs font-extrabold text-slate-600 uppercase">Status</label>
                <select name="affiliate_commission_status"
                    class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold">
                    @foreach(['pending','approved','paid','cancelled'] as $st)
                    <option value="{{ $st }}" {{ old('affiliate_commission_status', $order->affiliate_commission_status) === $st ? 'selected' : '' }}>
                        {{ ucfirst($st) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button class="px-4 py-2.5 rounded-2xl font-extrabold text-white w-full" style="background:#0194F3;">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection