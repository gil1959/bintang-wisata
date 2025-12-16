@extends('layouts.front')

@section('title', 'Checkout')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-10">
  <div class="mb-8">
    <h1 class="text-2xl font-extrabold text-slate-900">Checkout</h1>
    <p class="mt-1 text-sm text-slate-500">Lengkapi detail billing dan pilih metode pembayaran.</p>
  </div>

  <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

    {{-- LEFT FORM --}}
    <form method="POST"
          action="{{ route('checkout.process', $order->id) }}"
          class="lg:col-span-2 space-y-6">
      @csrf

      {{-- ERROR GLOBAL --}}
      @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
          <div class="font-bold">Ada yang perlu dibenerin:</div>
          <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- BILLING CARD --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-5 flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-extrabold text-slate-900">Billing Address</h2>
            <p class="mt-1 text-sm text-slate-500">Data ini dipakai untuk invoice dan konfirmasi pembayaran.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="text-sm font-bold text-slate-700">First Name</label>
            <input type="text" name="billing_first_name"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_first_name', $order->billing_first_name) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">Last Name</label>
            <input type="text" name="billing_last_name"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_last_name', $order->billing_last_name) }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">Country</label>
            <input type="text" name="billing_country"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_country', $order->billing_country ?? 'Indonesia') }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">Address</label>
            <textarea name="billing_address" rows="2"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30">{{ old('billing_address', $order->billing_address) }}</textarea>
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">City</label>
            <input type="text" name="billing_city"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_city', $order->billing_city) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">State</label>
            <input type="text" name="billing_state"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_state', $order->billing_state) }}">
          </div>

          <div>
            <label class="text-sm font-bold text-slate-700">Postal</label>
            <input type="text" name="billing_postal"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_postal', $order->billing_postal) }}">
          </div>

          <div class="sm:col-span-2">
            <label class="text-sm font-bold text-slate-700">Phone</label>
            <input type="text" name="billing_phone"
                   class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0194F3]/30"
                   value="{{ old('billing_phone', $order->billing_phone ?? $order->customer_phone) }}">
          </div>
        </div>
      </section>

      {{-- PAYMENT CARD --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-5">
          <h2 class="text-lg font-extrabold text-slate-900">Payment Method</h2>
          <p class="mt-1 text-sm text-slate-500">Pilih salah satu metode pembayaran di bawah.</p>
        </div>

        <div class="space-y-6">

          {{-- MANUAL BANK --}}
          @if($manualMethods->count())
            <div>
              <div class="mb-2 text-sm font-extrabold text-slate-800">Bank Transfer (Manual)</div>

              <div class="space-y-3">
                @foreach($manualMethods as $m)
                  <label class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 hover:border-[#0194F3]/50 hover:bg-slate-50">
                    <input type="radio"
                           name="payment_method"
                           value="manual:{{ $m->id }}"
                           class="mt-1 h-4 w-4 accent-[#0194F3]"
                           required>

                    <div class="min-w-0">
                      <div class="font-extrabold text-slate-900">
                        {{ $m->method_name }}
                        @if($m->account_number)
                          <span class="font-semibold text-slate-500">({{ $m->account_number }})</span>
                        @endif
                      </div>
                      <div class="mt-0.5 text-sm text-slate-500">
                        Transfer manual ke rekening ini.
                      </div>
                      @if(!empty($m->account_holder))
                        <div class="mt-1 text-xs text-slate-500">
                          A/n: <span class="font-semibold text-slate-700">{{ $m->account_holder }}</span>
                        </div>
                      @endif
                    </div>
                  </label>
                @endforeach
              </div>
            </div>
          @endif

          {{-- GATEWAY --}}
          <div>
            <div class="mb-2 text-sm font-extrabold text-slate-800">Payment Gateway</div>

            @forelse($gatewayOptions as $opt)
              <label class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 hover:border-[#0194F3]/50 hover:bg-slate-50">
                <input class="mt-1 h-4 w-4 accent-[#0194F3]"
                       type="radio"
                       name="payment_method"
                       value="{{ $opt['value'] }}"
                       required>
                <div class="min-w-0">
                  <div class="font-extrabold text-slate-900">{{ $opt['label'] }}</div>
                  <div class="mt-0.5 text-sm text-slate-500">Pembayaran otomatis via gateway.</div>
                </div>
              </label>
            @empty
              <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                Tidak ada gateway aktif.
              </div>
            @endforelse
          </div>

        </div>

        {{-- SUBMIT --}}
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-xs text-slate-500">
            Dengan melanjutkan, kamu menyetujui proses pembayaran sesuai metode yang dipilih.
          </p>

          <button type="submit"
                  class="w-full sm:w-auto rounded-xl px-6 py-3 text-sm font-extrabold text-white shadow-sm"
                  style="background:#0194F3"
                  onmouseover="this.style.background='#0186DB'"
                  onmouseout="this.style.background='#0194F3'">
            Lanjut ke Pembayaran
          </button>
        </div>
      </section>

    </form>

    {{-- RIGHT SUMMARY --}}
    <aside class="space-y-4">
      <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-4">
          <h2 class="text-lg font-extrabold text-slate-900">Ringkasan Pesanan</h2>
          <p class="mt-1 text-sm text-slate-500">Pastikan detail pesanan sudah benar.</p>
        </div>

        <div class="flex gap-3">
          @if(!empty($package->thumbnail_path))
            <img src="{{ asset('storage/'.$package->thumbnail_path) }}"
                 class="h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200"
                 alt="Thumbnail">
          @endif

          <div class="min-w-0">
            <p class="truncate font-extrabold text-slate-900">
              {{ $package->title ?? $order->product_name }}
            </p>
            <p class="mt-1 text-sm text-slate-500">
              {{ $order->type === 'tour' ? 'Paket Tour' : 'Paket Rent Car' }}
            </p>
            @if($order->invoice_number ?? false)
              <p class="mt-1 text-xs text-slate-500">
                Invoice: <span class="font-semibold text-slate-700">{{ $order->invoice_number }}</span>
              </p>
            @endif
          </div>
        </div>

        <div class="mt-5 space-y-2 text-sm">
          <div class="flex items-center justify-between text-slate-600">
            <span>Subtotal</span>
            <span class="font-bold text-slate-900">Rp {{ number_format($order->subtotal,0,',','.') }}</span>
          </div>

          <div class="flex items-center justify-between text-slate-600">
            <span>Diskon</span>
            <span class="font-bold text-slate-900">Rp {{ number_format($order->discount,0,',','.') }}</span>
          </div>

          <div class="my-3 border-t border-slate-200"></div>

          <div class="flex items-center justify-between">
            <span class="text-sm font-extrabold text-slate-900">Total</span>
            <span class="text-lg font-extrabold" style="color:#0194F3">
              Rp {{ number_format($order->final_price,0,',','.') }}
            </span>
          </div>
        </div>
      </div>
    </aside>

  </div>
</div>
@endsection
