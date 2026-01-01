@php
    $status = $status ?? null;

    $map = [
        'waiting_payment'       => ['Menunggu Pembayaran', 'bg-sky-50 text-sky-700 border-sky-200'],
        'waiting_verification'  => ['Verifikasi',          'bg-amber-50 text-amber-700 border-amber-200'],
        'paid'                  => ['Lunas',              'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'failed'                => ['Gagal',              'bg-rose-50 text-rose-700 border-rose-200'],
    ];

    [$label, $cls] = $map[$status] ?? ['-', 'bg-slate-50 text-slate-700 border-slate-200'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold border {{ $cls }}">
    {{ $label }}
</span>
