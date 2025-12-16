@extends('layouts.front')

@section('title', 'Pembayaran ' . $order->invoice_number)

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10">
  {{-- Flash --}}
  <div class="space-y-3">
    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
        {{ session('error') }}
      </div>
    @endif
  </div>

  {{-- Header --}}
  <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Pembayaran Manual</h1>
      <p class="mt-1 text-sm text-slate-600">
        Invoice: <span class="font-semibold text-slate-900">{{ $order->invoice_number }}</span>
      </p>
    </div>

    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-700">
      {{ strtoupper($order->type) }}
    </span>
  </div>

  <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Left: Summary + Instructions --}}
    <div class="space-y-6 lg:col-span-2">

      {{-- Ringkasan Pesanan --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div class="min-w-0">
            <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Produk</div>
            <div class="mt-1 text-base font-extrabold text-slate-900">
              {{ $order->product_name }}
            </div>

            <div class="mt-2 text-sm text-slate-600">
              Atas nama: <span class="font-semibold text-slate-900">{{ $order->customer_name }}</span>
              <span class="mx-2 text-slate-300">•</span>
              <span class="break-all">{{ $order->customer_email }}</span>
            </div>
          </div>

          <div class="text-left sm:text-right">
            <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Total</div>
            <div class="mt-1 text-2xl font-extrabold" style="color:#0194F3">
              Rp {{ number_format($order->final_price,0,',','.') }}
            </div>
          </div>
        </div>

        <div class="mt-5 border-t border-slate-200 pt-4">
          <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
              <span class="text-slate-600">Subtotal</span>
              <span class="font-extrabold text-slate-900">Rp {{ number_format($order->subtotal,0,',','.') }}</span>
            </div>

            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
              <span class="text-slate-600">Diskon</span>
              <span class="font-extrabold text-slate-900">Rp {{ number_format($order->discount,0,',','.') }}</span>
            </div>
          </div>
        </div>
      </section>

      {{-- Instruksi Transfer --}}
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl"
               style="background:rgba(1,148,243,.12); color:#0194F3;">
            <span class="font-extrabold">i</span>
          </div>

          <div class="min-w-0">
            <h2 class="text-lg font-extrabold text-slate-900">Instruksi Transfer</h2>
            <p class="mt-1 text-sm text-slate-600">
              Transfer sesuai nominal <span class="font-extrabold text-slate-900">persis</span> agar mudah diverifikasi.
              Setelah itu upload bukti transfer di bawah.
            </p>
          </div>
        </div>

        {{-- Detail Rekening --}}
        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div>
              <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Bank</div>
              <div class="mt-1 font-extrabold text-slate-900">{{ $manualMethod->bank_name }}</div>
            </div>
            <div>
              <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">No. Rekening</div>
              <div class="mt-1 font-extrabold text-slate-900">{{ $manualMethod->account_number }}</div>
            </div>
            <div>
              <div class="text-xs font-extrabold uppercase tracking-wide text-slate-500">Atas Nama</div>
              <div class="mt-1 font-extrabold text-slate-900">{{ $manualMethod->account_holder }}</div>
            </div>
          </div>
        </div>
      </section>

    </div>

    {{-- Right: Upload --}}
    <aside class="lg:col-span-1">
      <div class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h3 class="text-base font-extrabold text-slate-900">Upload Bukti Transfer</h3>
        <p class="mt-1 text-sm text-slate-600">
          Setelah upload, status akan jadi <span class="font-extrabold text-slate-900">waiting_verification</span>.
        </p>

        <form method="POST"
              action="{{ route('payment.manual.submit', $order) }}"
              enctype="multipart/form-data"
              class="mt-5 space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-extrabold text-slate-700">Bukti Transfer</label>

            <div class="mt-2 rounded-2xl border border-slate-200 bg-white p-3">
              <input type="file"
                     name="proof"
                     required
                     class="block w-full text-sm text-slate-700
                            file:mr-3 file:rounded-xl file:border-0 file:px-4 file:py-2 file:text-sm file:font-extrabold
                            file:text-white file:shadow-sm
                            file:[background:#0194F3] hover:file:opacity-90
                            focus:outline-none" />

              <p class="mt-2 text-xs text-slate-500">
                Format: JPG/JPEG/PNG • Maks: 2MB
              </p>
            </div>

            @error('proof')
              <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <button type="submit"
                  class="w-full rounded-2xl px-4 py-3 text-sm font-extrabold text-white shadow-sm"
                  style="background:#0194F3"
                  onmouseover="this.style.background='#0186DB'"
                  onmouseout="this.style.background='#0194F3'">
            Upload Bukti & Selesaikan
          </button>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">
            <div class="font-extrabold text-slate-800">Catatan</div>
            <ul class="mt-2 list-disc space-y-1 pl-5">
              <li>Pastikan nominal transfer sesuai total invoice.</li>
              <li>Upload gambar yang jelas (tidak blur).</li>
              <li>Verifikasi dilakukan oleh admin.</li>
            </ul>
          </div>
        </form>
      </div>
    </aside>

  </div>
</div>
@endsection
