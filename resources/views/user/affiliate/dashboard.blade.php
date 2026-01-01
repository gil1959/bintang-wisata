@extends('user.layouts.app')

@section('content')
<div class="space-y-5">
  <div>
    <h1 class="text-2xl font-extrabold text-slate-900">Affiliate</h1>
    <p class="mt-1 text-sm text-slate-600">Ringkasan performa affiliate kamu.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Links</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['links']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Clicks</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['clicks']) }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Conversions</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($stats['conversions']) }}</div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Commission Pending</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_pending'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Commission Approved</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_approved'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-200">
      <div class="text-xs font-extrabold text-slate-600 uppercase">Commission Paid</div>
      <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp {{ number_format((int)$stats['commission_paid'], 0, ',', '.') }}</div>
    </div>
  </div>
</div>
@endsection
