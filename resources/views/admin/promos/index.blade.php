@extends('layouts.admin')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius: 8px;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="max-w-5xl mx-auto">

    <h2 class="text-2xl font-semibold mb-6">Promo</h2>

    {{-- Card Tambah Promo --}}
    <div class="bg-white shadow p-6 rounded-xl mb-6">

        <h3 class="font-semibold mb-4 flex items-center gap-2">
            <span class="text-teal-600">🏷️</span> Tambah Promo Baru
        </h3>

        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-700 rounded mb-3">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.promos.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf

            <div>
                <label>Kode Promo</label>
                <input type="text" name="code" class="form-input mt-1 w-full" placeholder="CONTOH: LIBURAN50">
            </div>

            <div>
                <label>Tipe Diskon</label>
                <select name="type" id="promoType" class="form-select">
                    <option value="nominal">Potongan Harga (Rp)</option>
                    <option value="percentage">Persentase (%)</option>
                </select>

            </div>

            <div>
                <label>Nilai Diskon</label>
                <input type="number" name="value" id="discount_value"           class="form-control" placeholder="10%">
                   
            </div>

            <div class="flex items-end">
                <button class="bg-teal-700 text-white px-4 py-2 rounded w-full">Simpan</button>
            </div>

        </form>
    </div>

    {{-- List Promo --}}
    <div class="bg-white shadow p-6 rounded-xl">

        <h3 class="font-semibold mb-4">Daftar Promo Aktif</h3>

        @if ($promos->isEmpty())
            <p class="text-gray-500">Belum ada kode promo.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Kode</th>
                        <th class="py-2">Tipe</th>
                        <th class="py-2">Nilai</th>
                        <th class="py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promos as $p)
                        <tr class="border-b">
                            <td class="py-2 font-semibold">{{ $p->code }}</td>
                            <td class="py-2">
                                {{ $p->type === 'nominal' ? 'Potongan (Rp)' : 'Persentase (%)' }}
                            </td>
                            <td class="py-2">
                                @if($p->type === 'nominal')
                                Rp{{ number_format($p->value, 0, ',', '.') }}
                                 @else
                                    {{ rtrim(rtrim(number_format($p->value, 2), '0'), '.') }}%
                                @endif
                            </td>

                            <td class="py-2">
                                <form method="POST" action="{{ route('admin.promos.destroy', $p->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect = document.getElementById('discount_type');
    const valueInput = document.getElementById('discount_value');

    function updateInputAppearance() {
        let t = typeSelect.value;

        if (t === 'nominal') {
            valueInput.placeholder = "contoh: 50000";
            valueInput.classList.remove('text-end');
        } 
        else if (t === 'percent') {
            valueInput.placeholder = "contoh: 10";
            valueInput.classList.add('text-end');
        }
    }

    typeSelect.addEventListener('change', updateInputAppearance);

    updateInputAppearance(); // init
});
</script>

@endsection
