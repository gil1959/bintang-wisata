@php
    $tiers = ($package->tiers ?? collect())->where('type', $type)->values();
@endphp

<div x-data="{ open: false }" class="mb-4 border rounded shadow bg-white">

    <button type="button" 
        @click="open = !open"
        class="w-full px-4 py-3 bg-[#0194F3] text-white text-left font-semibold">
        {{ $label }}
    </button>

    <div x-show="open" class="p-4">

        <div x-data='{
            "rows": @json(old("tiers.$type", $tiers->toArray())),
            insertCustom() {
                if (this.rows.some(r => r.is_custom)) return;
                this.rows.push({ is_custom: true, min_people: 2, max_people: null, price: "" });
            },
            insertNormal() {
                this.rows.push({ is_custom: false, min_people: "", max_people: "", price: "" });
            }
        }'>

            <template x-for="(row, index) in rows" :key="index">
    <div class="p-3 mb-3 border rounded bg-gray-50">
        
        <div class="grid grid-cols-12 gap-2">

            <template x-if="row.is_custom">
                <div class="col-span-12 text-xs font-semibold text-blue-700 mb-2">
                    Custom Tier (min 2 orang, tanpa batas)
                </div>
            </template>

            <div class="col-span-4">
                <label class="text-sm">Min Orang</label>
                <input type="number"
                    :readonly="row.is_custom"
                    x-model="row.min_people"
                    :name="`tiers[{{ $type }}][${index}][min_people]`"
                    class="border rounded px-3 py-1 w-full bg-white">
            </div>

            <div class="col-span-4">
                <label class="text-sm">Max Orang</label>
                <input type="number"
                    :readonly="row.is_custom"
                    x-model="row.max_people"
                    :placeholder="row.is_custom ? '∞' : ''"
                    :name="`tiers[{{ $type }}][${index}][max_people]`"
                    class="border rounded px-3 py-1 w-full">
            </div>

            <div class="col-span-3">
                <label class="text-sm">Harga /pax</label>
                <input type="number"
                    x-model="row.price"
                    :name="`tiers[{{ $type }}][${index}][price]`"
                    class="border rounded px-3 py-1 w-full">
            </div>

            <div class="col-span-1 flex items-end">
                <button type="button"
                    @click="rows.splice(index, 1)"
                    class="px-3 py-1 bg-red-500 text-white rounded">
                    X
                </button>
            </div>

            {{-- FIX #1: is_custom boolean --}}
            <input type="hidden"
                :name="`tiers[{{ $type }}][${index}][is_custom]`"
                :value="row.is_custom ? 1 : 0">

            {{-- FIX #2: wajib ada field type --}}
            <input type="hidden"
                :name="`tiers[{{ $type }}][${index}][type]`"
                :value="'{{ $type }}'">

        </div>
    </div>
</template>

            <button type="button"
                @click="insertNormal()"
                class="px-4 py-2 bg-green-600 text-white rounded mr-3">
                + Tambah Tier Normal
            </button>

            <button type="button"
                @click="insertCustom()"
                class="px-4 py-2 bg-indigo-600 text-white rounded"
                :disabled="rows.some(r => r.is_custom)">
                + Tambah Tier Custom
            </button>

        </div>
    </div>
</div>
