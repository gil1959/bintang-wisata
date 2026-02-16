<div class="md:col-span-1">
    <div class="sticky top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100">

        <h2 class="font-bold text-lg mb-4 text-gray-900">{{ $i18n['reservation_title'] ?? 'Reservasi Paket' }}</h2>

        {{-- DAFTAR TIERS (DOMESTIK ONLY) --}}
        <div class="space-y-3">
            <template x-for="tier in tiers" :key="tier.id">
                <div
                    class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition flex justify-between items-center"
                    @click="selectedTier = tier"
                    :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''">
                    <div class="text-sm">
                        <p class="font-semibold text-gray-800"
                            x-text="tier.label_text ? tier.label_text : 'Paket'"></p>
                        <p class="text-xs text-gray-500 mt-1">{{ $i18n['domestic'] ?? 'Domestik' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[#0194F3] font-bold text-lg">
                            Rp <span x-text="Number(tier.price || 0).toLocaleString('id-ID')"></span>
                        </p>
                        <p class="text-[11px] text-gray-500">/ pax</p>
                    </div>
                </div>
            </template>

            <template x-if="!tiers || tiers.length === 0">
                <p class="text-sm text-gray-500">{{ $i18n['no_price'] ?? 'Belum ada harga untuk paket ini.' }}
                </p>
            </template>
        </div>

        {{-- BUTTON --}}
        <button
            type="button"
            class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
            :disabled="!selectedTier"
            @click="window.dispatchEvent(new CustomEvent('open-booking', { detail: { tier: selectedTier } }))">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            <span>{{ $i18n['continue_booking'] ?? 'Lanjut Booking' }}</span>

        </button>

    </div>
</div>