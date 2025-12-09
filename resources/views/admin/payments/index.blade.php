@extends('layouts.admin')

@section('content')
<div class="p-5">

    <h2 class="text-2xl font-bold mb-5">Pembayaran</h2>

    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================================= --}}
    {{-- ============== BANK MANUAL ============== --}}
    {{-- ========================================= --}}
    <div class="bg-white p-5 rounded shadow mb-8">
        <h3 class="text-lg font-semibold mb-3">Rekening Transfer Manual</h3>

        {{-- FORM TAMBAH BANK --}}
        <form method="POST" action="{{ route('admin.bank.add') }}" class="grid grid-cols-3 gap-3 mb-4">
            @csrf
            <input name="bank_name" class="border p-2 rounded" placeholder="Nama Bank">
            <input name="account_number" class="border p-2 rounded" placeholder="Nomor Rekening">
            <input name="account_holder" class="border p-2 rounded" placeholder="Atas Nama">

            {{-- type dan slug otomatis untuk manual --}}
            <input type="hidden" name="type" value="manual">

            <button class="col-span-3 bg-blue-600 text-white px-4 py-2 rounded">
                Tambah Rekening
            </button>
        </form>

        {{-- LIST BANK MANUAL --}}
        <div class="space-y-3">

            @foreach ($methods as $m)
                @if ($m->type === 'manual')
                    <div class="flex items-center justify-between border p-3 rounded">

                        <div>
                            <b>{{ $m->bank_name }}</b><br>
                            {{ $m->account_number }} a.n {{ $m->account_holder }}
                        </div>

                        <form method="POST" action="{{ route('admin.bank.delete', $m->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Hapus</button>
                        </form>

                    </div>
                @endif
            @endforeach

        </div>
    </div>

    {{-- ========================================= --}}
    {{-- ======== PAYMENT GATEWAY SECTION ======== --}}
    {{-- ========================================= --}}
    <div class="bg-white p-5 rounded shadow">

        <h3 class="text-lg font-semibold mb-4">Payment Gateway Integration</h3>

        @foreach ($gateways as $g)
            <div class="p-4 mb-6 rounded border
                @if($g->name=='xendit') bg-blue-50 border-blue-200
                @elseif($g->name=='duitku') bg-red-50 border-red-200
                @else bg-yellow-50 border-yellow-300 @endif">

                <form method="POST" action="{{ route('admin.gateway.update', $g) }}" class="space-y-3">
                    @csrf

                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold capitalize">{{ $g->name }}</h4>

                        <label class="flex items-center gap-2">
                            <span>Aktif</span>
                            <input type="checkbox" name="is_active" {{ $g->is_active ? 'checked' : '' }}>
                        </label>
                    </div>

                    @php $creds = $g->credentials ?? []; @endphp

                    {{-- XENDIT --}}
                    @if ($g->name === 'xendit')
                        <input name="credentials[secret_key]" class="border p-2 rounded w-full"
                               placeholder="Secret Key" value="{{ $creds['secret_key'] ?? '' }}">
                        <input name="credentials[public_key]" class="border p-2 rounded w-full"
                               placeholder="Public Key" value="{{ $creds['public_key'] ?? '' }}">
                    @endif

                    {{-- DUITKU --}}
                    @if ($g->name === 'duitku')
                        <input name="credentials[merchant_code]" class="border p-2 rounded w-full"
                               placeholder="Merchant Code" value="{{ $creds['merchant_code'] ?? '' }}">
                        <input name="credentials[api_key]" class="border p-2 rounded w-full"
                               placeholder="API Key" value="{{ $creds['api_key'] ?? '' }}">
                    @endif

                    {{-- TRIPAY --}}
                    @if ($g->name === 'tripay')
                        <input name="credentials[merchant_code]" class="border p-2 rounded w-full"
                               placeholder="Merchant Code" value="{{ $creds['merchant_code'] ?? '' }}">
                        <input name="credentials[api_key]" class="border p-2 rounded w-full"
                               placeholder="API Key" value="{{ $creds['api_key'] ?? '' }}">
                        <input name="credentials[private_key]" class="border p-2 rounded w-full"
                               placeholder="Private Key" value="{{ $creds['private_key'] ?? '' }}">
                    @endif

                    <button class="bg-green-600 text-white px-4 py-2 rounded">
                        Simpan
                    </button>

                </form>
            </div>
        @endforeach

    </div>

</div>
@endsection
