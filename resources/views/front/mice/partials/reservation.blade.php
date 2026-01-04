@php
    $tiers = $package->tiers ?? collect();

    $domestic = $tiers->where('type','domestic')
        ->filter(function ($t) {
            $price = (int)($t->price ?? 0);
            $label = trim((string)($t->label_text ?? ''));
            return $price > 0 || $label !== '';
        })
        ->sortBy('sort_order')
        ->values();

    $foreign = $tiers->where('type','foreign')
        ->filter(function ($t) {
            $price = (int)($t->price ?? 0);
            $label = trim((string)($t->label_text ?? ''));
            return $price > 0 || $label !== '';
        })
        ->sortBy('sort_order')
        ->values();

    $domesticJs = $domestic->map(function ($t) {
        return [
            'id'    => (int)$t->id,
            'label' => (string)($t->label_text ?: 'Harga'),
            'price' => (int)$t->price,
            'type'  => 'domestic',
        ];
    })->values();

    $foreignJs = $foreign->map(function ($t) {
        return [
            'id'    => (int)$t->id,
            'label' => (string)($t->label_text ?: 'Harga'),
            'price' => (int)$t->price,
            'type'  => 'international',
        ];
    })->values();
@endphp

<div class="md:col-span-1">
    <div class="sticky top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100"
         x-data="{
            active: 'domestic',
            selectedTier: null,
            tiers: {
                domestic: @js($domesticJs),
                international: @js($foreignJs)
            }
         }">

        <h2 class="font-bold text-lg mb-4 text-gray-900">Reservasi Paket</h2>

        {{-- TAB DOMESTIK / FOREIGN TOURISTS (SAMA PERSIS TOUR) --}}
        <div class="flex mb-4 bg-gray-100 rounded-full p-1 text-sm font-semibold">
            <button
                type="button"
                @click="active = 'domestic'"
                :class="active === 'domestic' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
                class="flex-1 py-2 rounded-full transition"
            >
                Domestik
            </button>
            <button
                type="button"
                @click="active = 'international'"
                :class="active === 'international' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
                class="flex-1 py-2 rounded-full transition"
            >
                Foreign Tourists
            </button>
        </div>

        {{-- DAFTAR TIERS (HOVER EFFECT SAMA TOUR) --}}
        <div class="space-y-3">
            <template x-for="tier in tiers[active]" :key="tier.id">
                <div
                    class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition flex justify-between items-center"
                    @click="selectedTier = tier"
                    :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''"
                >
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800" x-text="tier.label"></p>
                        <p class="text-xs text-gray-500 mt-1" x-text="active === 'domestic' ? 'Domestik' : 'WNA'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[#0194F3] font-bold text-lg">
                            Rp <span x-text="tier.price.toLocaleString('id-ID')"></span>
                        </p>
                        <p class="text-[11px] text-gray-500">/ pax</p>
                    </div>
                </div>
            </template>

            {{-- DOMESTIK: kalau kosong tampilkan info. FOREIGN: harus kosong tanpa text apa pun --}}
            <template x-if="active === 'domestic' && (!tiers[active] || tiers[active].length === 0)">
                <p class="text-sm text-gray-500">Belum ada harga untuk kategori ini.</p>
            </template>
        </div>

        {{-- BUTTON (ICON SAMA TOUR, bukan emoji) --}}
        <button
            type="button"
            class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
            :disabled="!selectedTier"
            @click="$dispatch('open-booking', { tier: selectedTier })"
        >
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            <span>Lanjut Booking</span>
        </button>

    </div>
</div>
