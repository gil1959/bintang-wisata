<div>
  <div class="md:sticky md:top-24 bg-white shadow-lg rounded-2xl p-6 border border-gray-100">

    <h2 class="font-bold text-lg mb-4 text-gray-900">Reservasi Sewa Kapal</h2>

    <div class="flex mb-4 bg-gray-100 rounded-full p-1 text-sm font-semibold">
      <button type="button"
        @click="active = 'weekday'"
        :class="active === 'weekday' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
        class="flex-1 py-2 rounded-full transition">
        Weekday
      </button>

      <button type="button"
        @click="active = 'weekend'"
        :class="active === 'weekend' ? 'bg-[#0194F3] text-white shadow-sm' : 'text-gray-600'"
        class="flex-1 py-2 rounded-full transition">
        Weekend
      </button>
    </div>

    <div class="space-y-3">
      <template x-for="tier in tiers[active]" :key="tier.id">
        <div
  class="p-4 border rounded-xl cursor-pointer hover:border-[#0194F3] transition
       flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2"

  @click="selectTier(tier)"
  :class="selectedTier && selectedTier.id === tier.id ? 'border-[#0194F3] bg-[#0194F3]/5' : ''"
>
  <div class="text-sm">
    <p class="font-semibold text-gray-800" x-text="tier.label_text"></p>
    <p class="text-xs text-gray-500 mt-1" x-text="active === 'weekday' ? 'Weekday' : 'Weekend'"></p>
  </div>

  <div class="text-left sm:text-right">
  <p class="text-[#0194F3] font-bold text-base sm:text-lg leading-tight">
    Rp <span x-text="Number(tier.price || 0).toLocaleString('id-ID')"></span>
  </p>
  <p class="text-[11px] text-gray-500">/ pax</p>
</div>


</div>

      </template>

      <template x-if="!tiers[active] || tiers[active].length === 0">
        <p class="text-sm text-gray-500">Belum ada harga untuk kategori ini.</p>
      </template>
    </div>

    <button
      type="button"
      class="w-full mt-6 bg-[#0194F3] text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
      :disabled="!selectedTier"
      @click="$dispatch('open-ship-booking', { tier: selectedTier })"
    >
      <i data-lucide="shopping-cart" class="w-4 h-4"></i>
      <span>Lanjut Booking</span>
    </button>

  </div>
</div>
