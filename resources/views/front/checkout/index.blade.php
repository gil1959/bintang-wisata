@extends('layouts.front')

@section('title', 'Checkout')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- LEFT FORM --}}
    <form method="POST" action="{{ route('checkout.process', $order->id) }}" 
          class="md:col-span-2 space-y-6 bg-white p-6 rounded-xl shadow">
        @csrf

        <h2 class="text-xl font-bold mb-2">Billing Address</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>First Name</label>
                <input name="billing_first_name" class="form-control"
                       value="{{ old('billing_first_name', $order->customer_name) }}">
            </div>

            <div>
                <label>Last Name</label>
                <input name="billing_last_name" class="form-control"
                       value="{{ old('billing_last_name') }}">
            </div>
        </div>

        <div>
            <label>Country</label>
            <input name="billing_country" class="form-control" value="{{ old('billing_country','Indonesia') }}">
        </div>

        <div>
            <label>Address</label>
            <textarea name="billing_address" class="form-control">{{ old('billing_address') }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>City</label>
                <input name="billing_city" class="form-control" value="{{ old('billing_city') }}">
            </div>

            <div>
                <label>State</label>
                <input name="billing_state" class="form-control" value="{{ old('billing_state') }}">
            </div>

            <div>
                <label>Postal</label>
                <input name="billing_postal" class="form-control" value="{{ old('billing_postal') }}">
            </div>
        </div>

        <div>
            <label>Phone</label>
            <input name="billing_phone" class="form-control"
                   value="{{ old('billing_phone', $order->customer_phone) }}">
        </div>

        {{-- PAYMENT METHOD --}}
        <h2 class="text-xl font-bold mt-6">Payment Method</h2>

        <div class="space-y-3">
            @foreach($methods as $m)
            <label class="flex items-center gap-3 border p-3 rounded-lg cursor-pointer">
                <input type="radio" name="payment_method" value="{{ $m->slug }}" required>
                <span class="font-semibold">{{ $m->method_name }}</span>
            </label>
            @endforeach
        </div>

        <button class="w-full mt-6 bg-blue-600 text-white py-3 rounded-xl font-bold">
            Lanjut ke Pembayaran
        </button>

    </form>

    {{-- RIGHT SUMMARY --}}
    <div class="bg-white p-6 rounded-xl shadow h-fit">
        @if($package && $package->thumbnail_path)
    <img src="{{ asset('storage/' . $package->thumbnail_path) }}" 
         class="w-full rounded mb-4">
@endif
        <h2 class="text-lg font-bold mb-4">Ringkasan Pesanan</h2>

        <p class="font-semibold">{{ $order->product_name }}</p>

        <div class="mt-4 space-y-2 text-sm">
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
@endsection
