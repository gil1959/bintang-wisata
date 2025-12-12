@extends('layouts.front')

@section('title', 'Checkout')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- LEFT FORM --}}
    <form method="POST"
          action="{{ route('checkout.process', $order->id) }}"
          class="md:col-span-2 space-y-6 bg-white p-6 rounded-xl shadow">
        @csrf

        {{-- ERROR GLOBAL --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 text-sm p-3 rounded">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- BILLING --}}
        <h2 class="text-xl font-bold mb-2">Billing Address</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">First Name</label>
                <input type="text" name="billing_first_name"
                       class="form-input w-full"
                       value="{{ old('billing_first_name', $order->billing_first_name) }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Last Name</label>
                <input type="text" name="billing_last_name"
                       class="form-input w-full"
                       value="{{ old('billing_last_name', $order->billing_last_name) }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Country</label>
            <input type="text" name="billing_country"
                   class="form-input w-full"
                   value="{{ old('billing_country', $order->billing_country ?? 'Indonesia') }}">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Address</label>
            <textarea name="billing_address"
                      class="form-input w-full"
                      rows="2">{{ old('billing_address', $order->billing_address) }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">City</label>
                <input type="text" name="billing_city"
                       class="form-input w-full"
                       value="{{ old('billing_city', $order->billing_city) }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">State</label>
                <input type="text" name="billing_state"
                       class="form-input w-full"
                       value="{{ old('billing_state', $order->billing_state) }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Postal</label>
                <input type="text" name="billing_postal"
                       class="form-input w-full"
                       value="{{ old('billing_postal', $order->billing_postal) }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Phone</label>
            <input type="text" name="billing_phone"
                   class="form-input w-full"
                   value="{{ old('billing_phone', $order->billing_phone ?? $order->customer_phone) }}">
        </div>

        {{-- PAYMENT METHOD --}}
        <h2 class="text-xl font-bold mt-6">Payment Method</h2>

        <div class="space-y-3">

            {{-- BANK TRANSFER (MANUAL) --}}
            @forelse($manualMethods as $m)
                <label class="flex items-center gap-3 border p-3 rounded-lg cursor-pointer">
                    <input type="radio"
                           name="payment_method"
                           value="manual:{{ $m->id }}"
                           class="accent-blue-600"
                           required>
                    <div>
                        <div class="font-semibold">
                            {{ $m->method_name }}
                            @if($m->account_number)
                                ({{ $m->account_number }})
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">
                            Transfer manual ke rekening ini.
                        </div>
                    </div>
                </label>
            @empty
                {{-- kalau belum ada bank manual biarin kosong --}}
            @endforelse

            {{-- PAYMENT GATEWAY --}}
            @foreach($gateways as $g)
                @if($g->is_active)
                    <label class="flex items-center gap-3 border p-3 rounded-lg cursor-pointer">
                        <input type="radio"
                               name="payment_method"
                               value="gateway:{{ $g->name }}"
                               class="accent-blue-600"
                               required>
                        <div>
                            <div class="font-semibold">
                                Bayar online via {{ ucfirst($g->name) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Kartu, e-wallet, atau virtual account (melalui gateway).
                            </div>
                        </div>
                    </label>
                @endif
            @endforeach
        </div>

        {{-- BUTTON SUBMIT --}}
        <div class="pt-4">
            <button type="submit"
                    class="w-full md:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-700
                           text-white font-semibold rounded-lg">
                Lanjut ke Pembayaran
            </button>
        </div>

    </form>

    {{-- RIGHT SUMMARY --}}
    <div class="space-y-4">

        <div class="bg-white shadow rounded-xl p-5">
            <h2 class="text-lg font-bold mb-3">Ringkasan Pesanan</h2>

            <div class="flex gap-3">
                @if(!empty($package->thumbnail_path))
                    <img src="{{ asset('storage/'.$package->thumbnail_path) }}"
                         class="w-24 h-24 object-cover rounded-lg">
                @endif
                <div>
                    <p class="font-semibold mb-1">{{ $package->title ?? $order->product_name }}</p>
                    <p class="text-sm text-gray-500">
                        @if($order->type === 'tour')
                            Paket Tour
                        @else
                            Paket Rent Car
                        @endif
                    </p>
                </div>
            </div>

            <div class="mt-4 space-y-1 text-sm">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <b>Rp {{ number_format($order->subtotal,0,',','.') }}</b>
                </div>
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <b>Rp {{ number_format($order->discount,0,',','.') }}</b>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->final_price,0,',','.') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
