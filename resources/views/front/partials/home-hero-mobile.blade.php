@php
    $isEn = app()->getLocale() === 'en';
@endphp

{{-- ================= MOBILE & TABLET HERO (Match User Screenshot) ================= --}}
<div class="block lg:hidden w-full bg-slate-50">
    {{-- Blue Header & Search Container --}}
    <div class="bg-gradient-to-b from-[#0175c2] via-[#0194F3] to-[#018ae3] text-white px-4 pt-3 pb-5 shadow-sm">
        {{-- Brand / Points Sub-header Bar --}}
        <div class="flex items-center justify-between gap-2 pb-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white flex items-center justify-center shrink-0 p-1.5 shadow-sm">
                    <img src="{{ $siteSettings['site_logo'] ?? asset('favicon.ico') }}" alt="{{ $siteSettings['site_name'] ?? 'Bintang Wisata Holiday' }}" class="w-full h-full object-contain" onerror="this.src='{{ asset('favicon.ico') }}'">
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-white leading-tight truncate">
                        {{ $siteSettings['site_name'] ?? 'Bintang Wisata Holiday' }}
                    </div>
                    <div class="text-[10px] text-white/85 font-medium leading-tight truncate">
                        One-Stop Travel Solution
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                @auth
                <div class="inline-flex items-center gap-1 bg-white/15 backdrop-blur border border-white/25 px-2.5 py-1 rounded-full text-xs font-bold text-white">
                    <i data-lucide="award" class="w-3.5 h-3.5 text-amber-300"></i>
                    <span>Member</span>
                </div>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1 bg-white/15 backdrop-blur border border-white/25 hover:bg-white/25 px-2.5 py-1 rounded-full text-xs font-bold text-white transition">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    <span>{{ $isEn ? 'Sign In' : 'Masuk' }}</span>
                </a>
                @endauth
            </div>
        </div>

        {{-- Mobile Search Bar --}}
        <form method="GET" action="{{ route('tours.index') }}" class="relative mt-1">
            <div class="relative flex items-center">
                <span class="absolute left-3.5 text-slate-400 pointer-events-none flex items-center justify-center">
                    <i data-lucide="search" class="w-4 h-4 text-[#0194F3]"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    placeholder="{{ $isEn ? 'Search Tour, Hotel, MICE, or Destination...' : 'Cari Paket Tour, Hotel, MICE, atau Destinasi...' }}"
                    class="w-full bg-white text-slate-800 text-xs sm:text-sm pl-10 pr-4 py-2.5 rounded-full shadow-[0_2px_8px_rgba(0,0,0,0.12)] border-0 focus:outline-none focus:ring-2 focus:ring-white/80 placeholder:text-slate-400 font-medium">
            </div>
        </form>
    </div>

    {{-- White Menu Icons Grid Card (With Swipe & Smooth Sliding Carousel) --}}
    <div class="px-2 sm:px-4 -mt-2 mb-3">
        <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-sm border border-slate-100"
             x-data="{
                page: 1,
                maxPages: 2,
                touchStartX: 0,
                touchEndX: 0,
                setPage(p) {
                    this.page = p;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    const diff = this.touchStartX - this.touchEndX;
                    if (diff > 45 && this.page < this.maxPages) {
                        this.setPage(2);
                    } else if (diff < -45 && this.page > 1) {
                        this.setPage(1);
                    }
                },
                init() {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
             }"
             @touchstart.passive="handleTouchStart($event)"
             @touchend.passive="handleTouchEnd($event)">

            {{-- Sliding Viewport --}}
            <div class="overflow-hidden w-full relative">
                <div class="flex transition-transform duration-300 ease-in-out w-full"
                     :style="'transform: translateX(-' + ((page - 1) * 100) + '%)'">

                    {{-- ================= PAGE 1 (Screenshot Primary Services) ================= --}}
                    <div class="w-full shrink-0 grid grid-cols-5 gap-y-3.5 gap-x-1 sm:gap-x-2 text-center select-none">
                        {{-- 1. Paket Tour --}}
                        <a href="{{ route('tours.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#E8F8F0] border border-[#d1f2e2] flex items-center justify-center text-[#10B981] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="map-pin" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Paket Tour
                            </span>
                        </a>

                        {{-- 2. Hotel/Vila --}}
                        <a href="{{ route('hotel.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#E8F2FE] border border-[#d2e5fd] flex items-center justify-center text-[#0194F3] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="building-2" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Hotel/Vila
                            </span>
                        </a>

                        {{-- 3. MICE --}}
                        <a href="{{ route('mice.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FEF5E7] border border-[#fde8c9] flex items-center justify-center text-[#F59E0B] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="briefcase" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                MICE
                            </span>
                        </a>

                        {{-- 4. Mobil / Rent Car --}}
                        <a href="{{ route('rentcar.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#E6FAF5] border border-[#ccf4ea] flex items-center justify-center text-[#0D9488] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="car" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Mobil
                            </span>
                        </a>

                        {{-- 5. Resto --}}
                        <a href="{{ route('restoran.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FEECEE] border border-[#fdd4d8] flex items-center justify-center text-[#F43F5E] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="utensils" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Resto
                            </span>
                        </a>

                        {{-- 6. Boat / Sewa Kapal --}}
                        <a href="{{ route('ship.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#EEF0FE] border border-[#dcdffd] flex items-center justify-center text-[#6366F1] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="anchor" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Boat
                            </span>
                        </a>

                        {{-- 7. Bus & Travel --}}
                        <a href="{{ route('travel-bus.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#E6FCFF] border border-[#c6f6fc] flex items-center justify-center text-[#06B6D4] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="bus" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Bus & Travel
                            </span>
                        </a>

                        {{-- 8. Umrah --}}
                        <a href="{{ route('umrah.index') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F5EDFE] border border-[#ebdaf9] flex items-center justify-center text-[#A855F7] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="sparkles" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Umrah
                            </span>
                        </a>

                        {{-- 9. Dokumen --}}
                        <a href="{{ route('travel-documents') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FEF9E7] border border-[#fdefbe] flex items-center justify-center text-[#EAB308] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="file-text" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Dokumen
                            </span>
                        </a>

                        {{-- 10. Lainnya (Slides to Page 2) --}}
                        <button type="button" @click="setPage(2)" class="group flex flex-col items-center cursor-pointer">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="more-horizontal" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Lainnya
                            </span>
                        </button>
                    </div>

                    {{-- ================= PAGE 2 (Secondary / Remaining Menus) ================= --}}
                    <div class="w-full shrink-0 grid grid-cols-5 gap-y-3.5 gap-x-1 sm:gap-x-2 text-center select-none">
                        {{-- 11. Artikel / Blog --}}
                        <a href="{{ route('articles') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#EFF6FF] border border-[#bfdbfe] flex items-center justify-center text-[#2563EB] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="newspaper" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Artikel
                            </span>
                        </a>

                        {{-- 12. Dokumentasi Tour --}}
                        <a href="{{ route('docs') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#ECFDF5] border border-[#a7f3d0] flex items-center justify-center text-[#059669] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="camera" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Dok. Tour
                            </span>
                        </a>

                        {{-- 13. Dokumentasi Kapal --}}
                        <a href="{{ route('docs.ship') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#ECFEFF] border border-[#a5f3fc] flex items-center justify-center text-[#0891B2] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="compass" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Dok. Kapal
                            </span>
                        </a>

                        {{-- 14. Dokumentasi Umrah --}}
                        <a href="{{ route('docs.umrah') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F5F3FF] border border-[#ddd6fe] flex items-center justify-center text-[#7C3AED] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="award" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Dok. Umrah
                            </span>
                        </a>

                        {{-- 15. Tentang Kami --}}
                        <a href="{{ route('about') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FEFCE8] border border-[#fef08a] flex items-center justify-center text-[#CA8A04] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="info" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Tentang
                            </span>
                        </a>

                        {{-- 16. Kontak Kami --}}
                        <a href="{{ route('contact') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F0FDF4] border border-[#bbf7d0] flex items-center justify-center text-[#16A34A] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="phone-call" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Kontak
                            </span>
                        </a>

                        {{-- 17. Syarat & Ketentuan --}}
                        <a href="{{ route('terms-conditions') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#F8FAFC] border border-[#e2e8f0] flex items-center justify-center text-[#475569] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="shield-check" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Syarat
                            </span>
                        </a>

                        {{-- 18. Kebijakan Privasi --}}
                        <a href="{{ route('privacy-policy') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FFF1F2] border border-[#fecdd3] flex items-center justify-center text-[#E11D48] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="lock" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Privasi
                            </span>
                        </a>

                        {{-- 19. Jadi Mitra --}}
                        <a href="{{ route('partner.register') }}" class="group flex flex-col items-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#FFF7ED] border border-[#fed7aa] flex items-center justify-center text-[#EA580C] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="users" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-slate-800 leading-tight line-clamp-1">
                                Jadi Mitra
                            </span>
                        </a>

                        {{-- 20. Kembali ke Menu Utama --}}
                        <button type="button" @click="setPage(1)" class="group flex flex-col items-center cursor-pointer">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#E8F2FE] border border-[#0194F3]/30 flex items-center justify-center text-[#0194F3] shadow-xs group-hover:scale-105 group-active:scale-95 transition-transform">
                                <i data-lucide="arrow-left" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                            </div>
                            <span class="mt-1.5 text-[11px] font-semibold text-[#0194F3] leading-tight line-clamp-1">
                                Kembali
                            </span>
                        </button>
                    </div>

                </div>
            </div>

            {{-- Slider Pagination Indicator Dots --}}
            <div class="flex items-center justify-center gap-1.5 mt-3 pt-1 border-t border-slate-100">
                <button type="button" @click="setPage(1)" class="h-1.5 rounded-full transition-all duration-300"
                        :class="page === 1 ? 'w-5 bg-[#0194F3]' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"></button>
                <button type="button" @click="setPage(2)" class="h-1.5 rounded-full transition-all duration-300"
                        :class="page === 2 ? 'w-5 bg-[#0194F3]' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"></button>
            </div>
        </div>
    </div>
</div>
