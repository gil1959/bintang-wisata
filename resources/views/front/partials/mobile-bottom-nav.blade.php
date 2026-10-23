@php
$isEn = app()->getLocale() === 'en';
@endphp

{{-- Sembunyikan Mobile Bottom Nav HANYA di halaman beranda, munculkan di halaman selain beranda --}}
@if(!request()->routeIs('home') && !request()->is('/'))
<div
  x-data="{
    openMore: false,
    init() {
      this.$watch('openMore', val => {
        if (val) {
          this.$nextTick(() => {
            if (window.lucide) window.lucide.createIcons();
          });
        }
      });
    }
  }"
  class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-[max(0.75rem,env(safe-area-inset-bottom))]">

  {{-- 1. Backdrop Dropup --}}
  <div
    x-show="openMore"
    x-cloak
    @click="openMore = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40"></div>

  {{-- 2. Dropup List Modal (Menu selain yang ada di bottom nav) --}}
  <div
    x-show="openMore"
    x-cloak
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0 translate-y-8 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-8 scale-95"
    class="fixed bottom-[74px] left-3 right-3 sm:left-auto sm:right-4 sm:w-96 z-50 max-h-[75vh] flex flex-col">
    <div class="bg-white rounded-3xl p-4 shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col">
      {{-- Header Modal --}}
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#0194F3] flex items-center justify-center">
            <i data-lucide="grid" class="w-4.5 h-4.5"></i>
          </div>
          <div>
            <div class="text-xs font-extrabold text-slate-900">
              {{ $isEn ? 'Other Services & Pages' : 'Layanan & Menu Lainnya' }}
            </div>
            <div class="text-[10px] text-slate-500">
              Bintang Wisata Holiday
            </div>
          </div>
        </div>
        <button
          type="button"
          @click="openMore = false"
          class="h-7 w-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      {{-- Menu List (Scrollable) --}}
      <div class="overflow-y-auto py-3 space-y-3.5 max-h-[58vh] pr-1">
        {{-- Group 1: Layanan Wisata --}}
        <div>
          <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-1">
            {{ $isEn ? 'Services' : 'Layanan Utama Lainnya' }}
          </div>
          <div class="grid grid-cols-4 gap-2 text-center select-none">
            {{-- Hotel/Vila --}}
            <a href="{{ route('hotel.index') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#E8F2FE] border border-[#d2e5fd] flex items-center justify-center text-[#0194F3] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="building-2" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Hotel/Vila</span>
            </a>

            {{-- Restoran --}}
            <a href="{{ route('restoran.index') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#FEECEE] border border-[#fdd4d8] flex items-center justify-center text-[#F43F5E] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="utensils" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Resto</span>
            </a>

            {{-- Bus & Travel --}}
            <a href="{{ route('travel-bus.index') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#E6FCFF] border border-[#c6f6fc] flex items-center justify-center text-[#06B6D4] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="bus" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Bus Travel</span>
            </a>

            {{-- Umrah --}}
            <a href="{{ route('umrah.index') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#F5EDFE] border border-[#ebdaf9] flex items-center justify-center text-[#A855F7] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="sparkles" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Umrah</span>
            </a>
          </div>
        </div>

        {{-- Group 2: Informasi & Dokumentasi --}}
        <div>
          <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-1">
            {{ $isEn ? 'Information & Gallery' : 'Informasi & Dokumentasi' }}
          </div>
          <div class="grid grid-cols-4 gap-2 text-center select-none">
            {{-- Dokumen Perjalanan --}}
            <a href="{{ route('travel-documents') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#FEF9E7] border border-[#fdefbe] flex items-center justify-center text-[#EAB308] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="file-text" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Dokumen</span>
            </a>

            {{-- Artikel --}}
            <a href="{{ route('articles') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#EFF6FF] border border-[#bfdbfe] flex items-center justify-center text-[#2563EB] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="newspaper" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Artikel</span>
            </a>

            {{-- Dok. Tour --}}
            <a href="{{ route('docs') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#ECFDF5] border border-[#a7f3d0] flex items-center justify-center text-[#059669] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="camera" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Dok. Tour</span>
            </a>

            {{-- Dok. Kapal --}}
            <a href="{{ route('docs.ship') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#ECFEFF] border border-[#a5f3fc] flex items-center justify-center text-[#0891B2] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="compass" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Dok. Kapal</span>
            </a>

            {{-- Dok. Umrah --}}
            <a href="{{ route('docs.umrah') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#F5F3FF] border border-[#ddd6fe] flex items-center justify-center text-[#7C3AED] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="award" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Dok. Umrah</span>
            </a>

            {{-- Tentang Kami --}}
            <a href="{{ route('about') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#FEFCE8] border border-[#fef08a] flex items-center justify-center text-[#CA8A04] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="info" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Tentang</span>
            </a>

            {{-- Kontak --}}
            <a href="{{ route('contact') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#F0FDF4] border border-[#bbf7d0] flex items-center justify-center text-[#16A34A] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="phone-call" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Kontak</span>
            </a>

            {{-- Jadi Mitra --}}
            <a href="{{ route('partner.register') }}" class="group flex flex-col items-center p-1.5 rounded-2xl hover:bg-slate-50 transition">
              <div class="w-11 h-11 rounded-2xl bg-[#FFF7ED] border border-[#fed7aa] flex items-center justify-center text-[#EA580C] shadow-xs group-hover:scale-105 transition-transform">
                <i data-lucide="users" class="w-5 h-5"></i>
              </div>
              <span class="mt-1 text-[11px] font-semibold text-slate-700 leading-tight">Mitra</span>
            </a>
          </div>
        </div>

        {{-- Group 3: Legalitas --}}
        <div class="pt-2 border-t border-slate-100 flex items-center justify-between px-2 text-[11px] text-slate-500 font-semibold">
          <a href="{{ route('terms-conditions') }}" class="hover:text-blue-600 transition flex items-center gap-1">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
            <span>Syarat & Ketentuan</span>
          </a>
          <a href="{{ route('privacy-policy') }}" class="hover:text-blue-600 transition flex items-center gap-1">
            <i data-lucide="lock" class="w-3.5 h-3.5"></i>
            <span>Kebijakan Privasi</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- 3. BOTTOM NAV BAR UTAMA --}}
  <div class="relative z-50 max-w-7xl mx-auto px-4">
    <div class="rounded-3xl border border-slate-200 bg-white/90 backdrop-blur-md shadow-[0_10px_30px_rgba(15,23,42,0.12)] px-2 py-1.5">
      <div
        class="grid grid-cols-6 items-center text-center"
        style="display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); align-items:center;">

        {{-- Item 1: Beranda --}}
        @php $activeHome = request()->routeIs('home'); @endphp
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-1.5" style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $activeHome ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);' : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="home" class="w-4.5 h-4.5" style="{{ $activeHome ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $activeHome ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            {{ $isEn ? 'Home' : 'Beranda' }}
          </span>
          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $activeHome ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>

        {{-- Item 2: Paket Tour --}}
        @php $activeTour = request()->routeIs('tours.index') || request()->is('paket-tour*') || request()->is('tours*'); @endphp
        <a href="{{ route('tours.index') }}" class="flex flex-col items-center justify-center py-1.5" style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $activeTour ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);' : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="map" class="w-4.5 h-4.5" style="{{ $activeTour ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $activeTour ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            {{ $isEn ? 'Tours' : 'Paket Tour' }}
          </span>
          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $activeTour ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>

        {{-- Item 3: Mobil --}}
        @php $activeCar = request()->routeIs('rentcar.index') || request()->is('sewa-mobil*'); @endphp
        <a href="{{ route('rentcar.index') }}" class="flex flex-col items-center justify-center py-1.5" style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $activeCar ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);' : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="car" class="w-4.5 h-4.5" style="{{ $activeCar ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $activeCar ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            {{ $isEn ? 'Car' : 'Mobil' }}
          </span>
          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $activeCar ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>

        {{-- Item 4: Kapal --}}
        @php $activeShip = request()->routeIs('ship.index') || request()->is('sewa-kapal*'); @endphp
        <a href="{{ route('ship.index') }}" class="flex flex-col items-center justify-center py-1.5" style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $activeShip ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);' : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="anchor" class="w-4.5 h-4.5" style="{{ $activeShip ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $activeShip ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            {{ $isEn ? 'Boat' : 'Kapal' }}
          </span>
          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $activeShip ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>

        {{-- Item 5: MICE --}}
        @php $activeMice = request()->routeIs('mice.index') || request()->is('paket-mice*'); @endphp
        <a href="{{ route('mice.index') }}" class="flex flex-col items-center justify-center py-1.5" style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $activeMice ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);' : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="briefcase" class="w-4.5 h-4.5" style="{{ $activeMice ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>
          <span class="mt-1 text-[11px] leading-none font-semibold {{ $activeMice ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            MICE
          </span>
          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $activeMice ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>

        {{-- Item 6: LAINNYA (Trigger Dropup List) --}}
        <button
          type="button"
          @click="openMore = !openMore"
          class="flex flex-col items-center justify-center py-1.5 focus:outline-none cursor-pointer"
          style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border transition-all duration-200"
            :style="openMore
              ? 'background:rgba(1,148,243,0.15); border-color:rgba(1,148,243,0.35); transform:scale(1.05);'
              : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);'">
            <i data-lucide="more-horizontal"
              class="w-4.5 h-4.5 transition-colors"
              :style="openMore ? 'color:#0194F3;' : 'color:#0f172a;'"></i>
          </div>
          <span
            class="mt-1 text-[11px] leading-none font-semibold max-w-[56px] truncate transition-colors"
            :class="openMore ? 'text-[#0194F3] font-bold' : 'text-slate-700'">
            {{ $isEn ? 'Others' : 'Lainnya' }}
          </span>
          <span
            class="mt-0.5 h-[3px] w-7 rounded-full transition-opacity duration-200"
            :class="openMore ? 'opacity-100' : 'opacity-0'"
            style="background:#0194F3;"></span>
        </button>

      </div>
    </div>
  </div>
</div>
@endif