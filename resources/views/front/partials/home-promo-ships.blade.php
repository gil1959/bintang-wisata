@php
    // default enabled kalau setting belum ada
    $shipPromoEnabled = ($siteSettings['home_ship_promo_enabled'] ?? '1') === '1';
@endphp

@if($shipPromoEnabled && isset($promoShips) && $promoShips->count() > 0)
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-14"
         x-data="promoShipSlider()"
         x-init="init()">

        <div class="flex items-end justify-between gap-4" data-aos="fade-up">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="ship" class="w-4 h-4"></i>
                    {{ $siteSettings['home_ship_promo_badge'] ?? 'PROMO KAPAL' }}
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $siteSettings['home_ship_promo_title'] ?? 'Paket Sewa Kapal Promo' }}
                </h2>

                @if(!empty($siteSettings['home_ship_promo_desc']))
                    <p class="mt-2 text-slate-600">
                        {{ $siteSettings['home_ship_promo_desc'] }}
                    </p>
                @endif
            </div>

            {{-- Buttons desktop --}}
            <div class="hidden md:flex items-center gap-2">
                <button type="button"
                        class="btn btn-ghost !px-3 !py-2"
                        @click="prev()"
                        aria-label="Sebelumnya">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>

                <button type="button"
                        class="btn btn-ghost !px-3 !py-2"
                        @click="next()"
                        aria-label="Berikutnya">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="mt-7">
            <div x-ref="track"
                 class="flex gap-5 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar pb-2">
                @foreach($promoShips as $package)
    @php
        // konsisten dengan Tours: rating_value & rating_count
        $ratingValue = (float) ($package->rating_value ?? 5);
        $ratingCount = (int) ($package->rating_count ?? 0);

        // harga termurah dari semua tier kapal
        $minPrice = ($package->tiers ?? collect())->min('price');
    @endphp

    <div class="snap-start shrink-0 w-[82%] sm:w-[48%] md:w-[32%] lg:w-[24%] xl:w-[19%]">
        <a href="{{ route('ship.show', $package->slug) }}"
           class="group block bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition">

            <div class="relative h-44 overflow-hidden bg-slate-100">
                <img
                    src="{{ $package->thumbnail_path ? asset('storage/'.$package->thumbnail_path) : 'https://via.placeholder.com/1200x600?text=Sewa+Kapal' }}"
                    alt="{{ $package->title }}"
                    class="h-full w-full object-cover"
                >

                @if(!empty($package->label))
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center rounded-full bg-red-600 border border-red-600 px-3 py-1 text-xs font-extrabold text-white shadow">
            {{ $package->label }}
        </span>
                    </div>
                @endif

                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                        <i data-lucide="tag" class="w-4 h-4" style="color:#0194F3;"></i>
                        {{ $package->category?->name ?? 'Kapal' }}
                    </span>
                </div>
            </div>

            <div class="px-4 pt-4 pb-3">
                <div class="text-[15px] font-extrabold text-[#0194F3] line-clamp-2">
                    {{ $package->title }}
                </div>

                <div class="mt-2 text-sm">
                    <span class="text-slate-600">Mulai </span>
                    <span class="font-extrabold text-rose-600">
                        @if($minPrice !== null)
                            Rp {{ number_format((int) $minPrice, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </span>
                    <span class="text-slate-500">/charter</span>
                </div>

                <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                    <div class="flex items-center gap-0.5" aria-label="Rating">
                        @for($i=0; $i<5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FBBF24" class="w-4 h-4">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        @endfor
                    </div>
                    <span>({{ $ratingCount }})</span>
                </div>
            </div>

            <div class="border-t border-slate-200 px-4 pt-3 pb-4">
                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <i data-lucide="ship" class="w-4 h-4" style="color:#0194F3;"></i>
                    <span class="line-clamp-1">Private charter</span>
                </div>

                <div class="mt-3">
                    <div class="btn btn-primary w-full justify-center !rounded-md !py-2">
                        Lihat Detail
                    </div>
                </div>
            </div>
        </a>
    </div>
@endforeach

            </div>

            {{-- Buttons mobile --}}
            <div class="mt-4 flex md:hidden items-center justify-center gap-2">
                <button type="button" class="btn btn-ghost !px-3 !py-2" @click="prev()">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button type="button" class="btn btn-ghost !px-3 !py-2" @click="next()">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

    </div>
</section>

<script>
/**
 * Slider promo ship:
 * pola sama dengan promo tours, tapi nama function beda supaya gak tabrakan.
 */
window.promoShipSlider = window.promoShipSlider || function () {
    return {
        init() {
            this.$nextTick(() => {});
        },
        step() {
            const el = this.$refs.track;
            if (!el) return 320;

            const firstCard = el.querySelector('.snap-start');
            const gap = 20; // match gap-5
            return firstCard ? (firstCard.getBoundingClientRect().width + gap) : 320;
        },
        next() {
            const el = this.$refs.track;
            if (!el) return;
            el.scrollBy({ left: this.step(), behavior: 'smooth' });
        },
        prev() {
            const el = this.$refs.track;
            if (!el) return;
            el.scrollBy({ left: -this.step(), behavior: 'smooth' });
        }
    }
};
</script>
@endif
