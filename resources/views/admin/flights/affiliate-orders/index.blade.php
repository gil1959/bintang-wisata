@extends('layouts.admin')

@section('title', 'Affiliate Tiket Pesawat')
@section('page-title', 'Affiliate Tiket Pesawat')

@section('content')
<div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Affiliate Order Tiket Pesawat</h1>
            <p class="mt-1 text-sm text-slate-600">List order flight yang datang dari affiliate. Komisi bisa di-set dari sini juga.</p>
        </div>

        <form method="GET" class="flex flex-wrap gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari invoice/customer/ref"
                class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold">
            <input name="origin" value="{{ $origin }}" placeholder="Asal"
                class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold w-28">
            <input name="destination" value="{{ $destination }}" placeholder="Tujuan"
                class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold w-28">
            <select name="status" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold">
                <option value="">All Status</option>
                @foreach(['pending','approved','paid','cancelled'] as $st)
                <option value="{{ $st }}" @selected($status===$st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2 rounded-2xl font-extrabold text-white" style="background:#0194F3;">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Invoice</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Route</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Customer</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Ref</th>
                    <th class="text-left px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Commission</th>
                    <th class="text-right px-4 py-3 font-extrabold text-slate-600 uppercase text-xs">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($orders as $o)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-bold text-slate-900">{{ $o->invoice_number }}</div>
                        <div class="text-xs text-slate-500">{{ $o->product_name }}</div>
                    </td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $o->product_name }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-slate-800">{{ $o->customer_name }}</div>
                        <div class="text-xs text-slate-600">{{ $o->customer_email }}</div>
                    </td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $o->affiliate_ref ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">
                        Rp {{ number_format((float) ($o->affiliate_commission_amount ?? 0), 0, ',', '.') }}
                        <div class="text-xs text-slate-600">{{ $o->affiliate_commission_status ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.flights.affiliate-orders.show', $o->id) }}"
                            class="px-3 py-2 rounded-2xl border border-slate-200 font-extrabold hover:bg-slate-50">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-slate-600">Tidak ada order affiliate tiket pesawat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection