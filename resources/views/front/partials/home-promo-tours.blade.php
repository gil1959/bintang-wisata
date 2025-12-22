@php
    // default enabled kalau setting belum ada
    $promoEnabled = ($siteSettings['home_promo_enabled'] ?? '1') === '1';
@endphp

@if($promoEnabled && isset($promoTours) && $promoTours->count() > 0)
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-10 lg:py-14"
         x-data="promoTourSlider()"
         x-init="init()">

        <div class="flex items-end justify-between gap-4" data-aos="fade-up">
            <div>
                <div class="pill pill-azure">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                    {{ $siteSettings['home_promo_badge'] ?? 'PROMO' }}
                </div>

                <h2 class="mt-4 text-2xl lg:text-3xl font-extrabold text-slate-900">
                    {{ $siteSettings['home_promo_title'] ?? 'Paket Tour Promo' }}
                </h2>

                @if(!empty($siteSettings['home_promo_desc']))
                    <p class="mt-2 text-slate-600">
                        {{ $siteSettings['home_promo_desc'] }}
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
                @foreach($promoTours as $package)
                    <div class="snap-start shrink-0 w-[82%] sm:w-[48%] md:w-[32%] lg:w-[24%] xl:w-[19%]">
                        <a href="{{ route('tour.show', $package) }}" class="group card overflow-hidden block h-full">
                            <div class="relative h-44 overflow-hidden bg-slate-100">
                                @if($package->thumbnail_path)
                                    <img
                                        src="{{ asset('storage/'.$package->thumbnail_path) }}"
                                        alt="{{ $package->title }}"
                                        class="h-full w-full object-cover group-hover:scale-105 transition duration-500"
                                    >
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-tr from-slate-100 via-white to-white"></div>
                                    <div class="absolute inset-0 grid place-items-center text-slate-500 text-sm">
                                        No Image
                                    </div>
                                @endif

                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-600 text-white px-3 py-1 text-xs font-extrabold shadow-soft">
                                        PROMO
                                    </span>
                                </div>
                            </div>

                            <div class="p-5">
                                <h3 class="text-base font-extrabold text-slate-900 group-hover:text-azure transition line-clamp-2">
                                    {{ $package->title }}
                                </h3>

                                @if($package->duration_text)
                                    <div class="mt-2 text-sm text-slate-600 flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-slate-500"></i>
                                        Durasi: {{ $package->duration_text }}
                                    </div>
                                @endif
<div class="mt-3 flex items-center gap-2 text-sm text-slate-700">
    {{-- 1 bintang full kuning --}}
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 24 24"
         fill="#FBBF24"
         class="w-4 h-4">
        <path d="M12 17.27L18.18 21l-1.64-7.03
                 L22 9.24l-7.19-.61L12 2
                 9.19 8.63 2 9.24l5.46 4.73
                 L5.82 21z"/>
    </svg>

    <span class="font-semibold">
        {{ $package->rating_value ?? 5 }} Rating
    </span>
</div>

                                <div class="mt-4 inline-flex items-center gap-2 text-sm font-extrabold" style="color:#0194F3;">
                                    Lihat Detail
                                    <span class="transition group-hover:translate-x-1">→</span>
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
 * FIX slider promo:
 * - Tidak bergantung 'alpine:init' (yang bisa kelewat).
 * - Tidak register global Alpine.data (jadi nggak conflict sama Alpine lain).
 * - Function langsung dipakai di x-data="promoTourSlider()".
 * - Ada init() supaya $refs.track pasti kebaca setelah render.
 */
window.promoTourSlider = window.promoTourSlider || function () {
    return {
        init() {
            // pastikan ref kebaca
            this.$nextTick(() => {
                // no-op, tapi memastikan DOM sudah siap
                // bisa dipakai buat debug: console.log(this.$refs.track)
            });
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
