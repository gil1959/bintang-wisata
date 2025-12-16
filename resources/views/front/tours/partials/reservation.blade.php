<div class="md:col-span-1">
    <div class="sticky top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100">

        <h2 class="font-bold text-lg mb-4 text-gray-900">Reservasi Paket</h2>

        {{-- TAB DOMESTIK / WNA --}}
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

        {{-- DAFTAR TIERS --}}
        <div class="space-y-3">
            <template x-for="tier in tiers[active]" :key="tier.id">
                <div
                    class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition flex justify-between items-center"
                    @click="selectedTier = tier"
                    :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''"
                >
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800"
                           x-text="tier.is_custom
                                ? 'Custom (min 2 pax)'
                                : (tier.max_people
                                    ? (tier.min_people + '-' + tier.max_people + ' Org')
                                    : (tier.min_people + '+ Org'))">
                        </p>
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

            <template x-if="!tiers[active] || tiers[active].length === 0">
                <p class="text-sm text-gray-500">Belum ada harga untuk kategori ini.</p>
            </template>
        </div>

        {{-- INFO PESAWAT --}}
        <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded text-xs text-blue-800 space-y-1">
            <template x-if="flightInfo === 'included'">
                <p>Paket ini <b>SUDAH termasuk</b> tiket pesawat.</p>
            </template>
            <template x-if="flightInfo === 'not_included'">
                <p>Paket ini <b>BELUM termasuk</b> tiket pesawat.</p>
            </template>
        </div>

        {{-- BUTTON --}}
        <button
            type="button"
            class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
            :disabled="!selectedTier"
            @click="$dispatch('open-booking', { tier: selectedTier })"
        >
            <span>🛒</span>
            <span>Lanjut Booking</span>
        </button>

    </div>
</div>
