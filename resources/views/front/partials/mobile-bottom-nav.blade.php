@php
  // ✅ MOBILE NAV cuma 6 item:
  // Home, Tour, Rental, Kapal, Umrah, Documentation (dropdown)
  $mobileNav = [
    ['type' => 'link', 'route' => 'home',         'icon' => 'home',      'mobile_label' => 'Home'],
    ['type' => 'link', 'route' => 'tours.index',  'icon' => 'map',       'mobile_label' => 'Tour'],
    ['type' => 'link', 'route' => 'rentcar.index','icon' => 'car',       'mobile_label' => 'Rental'],
    ['type' => 'link', 'route' => 'ship.index',   'icon' => 'anchor',    'mobile_label' => 'Kapal'],
    ['type' => 'link', 'route' => 'umrah.index',  'icon' => 'landmark',  'mobile_label' => 'Umrah'],
    ['type' => 'docs_dropdown', 'icon' => 'book-open', 'mobile_label' => 'Docs'],
  ];

  // Docs active state
  $docsActive = request()->routeIs('docs') || request()->routeIs('docs.*') || request()->is('dokumentasi*');
@endphp

<div
  x-data="{ docsOpen:false }"
  class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-[max(0.75rem,env(safe-area-inset-bottom))]"
>
  {{-- ✅ BACKDROP (klik untuk nutup dropdown), fixed biar gak ngaruh layout & gak bikin scroll loncat --}}
  <div
    x-show="docsOpen"
    x-transition.opacity
    x-cloak
    class="fixed inset-0 z-40"
    style="background: rgba(15,23,42,0.25);"
    @click="docsOpen=false"
  ></div>

  {{-- ✅ DROPDOWN PANEL: FIXED, gak ikut grid, gak nabrak --}}
  <div
    x-show="docsOpen"
    x-transition
    x-cloak
    class="fixed z-50 left-4 right-4"
    style="bottom: calc(5.2rem + env(safe-area-inset-bottom));"
  >
    <div class="rounded-3xl border border-slate-200 bg-white/95 backdrop-blur shadow-[0_14px_40px_rgba(15,23,42,0.18)] overflow-hidden">
      {{-- header kecil biar jelas --}}
      <div class="px-4 pt-4 pb-2 text-xs font-bold text-slate-500 tracking-wide">
        DOCUMENTATION
      </div>

      {{-- ✅ cuma icon + label, no panjang-panjang --}}
      <div class="px-2 pb-3">
        <a
          href="{{ route('docs') }}"
          @click="docsOpen=false"
          class="flex items-center gap-3 px-3 py-3 rounded-2xl hover:bg-slate-50 transition"
        >
          <span class="h-10 w-10 rounded-2xl grid place-items-center border border-slate-200"
                style="background: rgba(1,148,243,0.10);">
            <i data-lucide="map" class="w-5 h-5" style="color:#0194F3;"></i>
          </span>
          <span class="text-sm font-semibold text-slate-800">Dokumentasi Tour</span>
        </a>

        <a
          href="{{ route('docs.ship') }}"
          @click="docsOpen=false"
          class="flex items-center gap-3 px-3 py-3 rounded-2xl hover:bg-slate-50 transition"
        >
          <span class="h-10 w-10 rounded-2xl grid place-items-center border border-slate-200"
                style="background: rgba(1,148,243,0.10);">
            <i data-lucide="anchor" class="w-5 h-5" style="color:#0194F3;"></i>
          </span>
          <span class="text-sm font-semibold text-slate-800">Dokumentasi Kapal</span>
        </a>

        <a
          href="{{ route('docs.umrah') }}"
          @click="docsOpen=false"
          class="flex items-center gap-3 px-3 py-3 rounded-2xl hover:bg-slate-50 transition"
        >
          <span class="h-10 w-10 rounded-2xl grid place-items-center border border-slate-200"
                style="background: rgba(1,148,243,0.10);">
            <i data-lucide="landmark" class="w-5 h-5" style="color:#0194F3;"></i>
          </span>
          <span class="text-sm font-semibold text-slate-800">Dokumentasi Umrah</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ✅ BOTTOM NAV BAR --}}
  <div class="relative z-50 max-w-7xl mx-auto px-4">
    <div class="rounded-3xl border border-slate-200 bg-white/85 backdrop-blur shadow-[0_10px_30px_rgba(15,23,42,0.10)] px-2 py-2">
      <div
        class="grid grid-cols-6 items-center"
        style="display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); align-items:center;"
      >
        @foreach($mobileNav as $item)
          @php
            $isDocs = ($item['type'] ?? 'link') === 'docs_dropdown';
            $active = $isDocs
              ? $docsActive
              : request()->routeIs($item['route']);

            $label = $item['mobile_label'];
          @endphp

          @if(!$isDocs)
            <a
              href="{{ route($item['route']) }}"
              class="flex flex-col items-center justify-center py-2"
              style="width:100%;"
            >
              <div
                class="h-9 w-9 rounded-2xl grid place-items-center border"
                style="{{ $active
                        ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);'
                        : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}"
              >
                <i data-lucide="{{ $item['icon'] }}"
                   class="w-4.5 h-4.5"
                   style="{{ $active ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
              </div>

              <span class="mt-1 text-[11px] leading-none font-semibold {{ $active ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
                {{ $label }}
              </span>

              <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $active ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
            </a>

          @else
            {{-- ✅ DOCS BUTTON --}}
            <button
              type="button"
              class="flex flex-col items-center justify-center py-2 w-full"
              @click="docsOpen = !docsOpen"
            >
              <div
                class="h-9 w-9 rounded-2xl grid place-items-center border"
                style="{{ $active
                        ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);'
                        : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}"
              >
                <i data-lucide="{{ $item['icon'] }}"
                   class="w-4.5 h-4.5"
                   style="{{ $active ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
              </div>

              <span class="mt-1 text-[11px] leading-none font-semibold {{ $active ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
                {{ $label }}
              </span>

              <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $active ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
            </button>
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
