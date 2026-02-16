@php
$isEn = app()->getLocale() === 'en';

// ✅ MOBILE NAV 6 item:
// Home, Tour, Rental, Kapal, Umrah, Dokumentasi
$mobileNav = [
[
'route' => 'home',
'icon' => 'home',
'label' => $isEn ? 'Home' : 'Beranda',
],
[
'route' => 'tours.index',
'icon' => 'map',
'label' => $isEn ? 'Tours' : 'Paket Tour',
],
[
'route' => 'rentcar.index',
'icon' => 'car',
'label' => $isEn ? 'Car ' : 'Mobil',
],
[
'route' => 'ship.index',
'icon' => 'anchor',
'label' => $isEn ? 'Ferry' : 'Kapal',
],
[
'route' => 'umrah.index',
'icon' => 'landmark',
'label' => $isEn ? 'Umrah' : 'Umrah',
],
[
'route' => 'docs',
'icon' => 'book-open',
'label' => $isEn ? 'Docs' : 'Dokumentasi',
],
];
@endphp


<div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
  {{-- ✅ BOTTOM NAV BAR --}}
  <div class="relative z-50 max-w-7xl mx-auto px-4">
    <div class="rounded-3xl border border-slate-200 bg-white/85 backdrop-blur shadow-[0_10px_30px_rgba(15,23,42,0.10)] px-2 py-2">
      <div
        class="grid grid-cols-6 items-center"
        style="display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); align-items:center;">
        @foreach($mobileNav as $item)
        @php
        $active = request()->routeIs($item['route']);
        // biar icon "Docs" tetap aktif juga saat user ada di docs.ship / docs.umrah (opsional)
        if ($item['route'] === 'docs') {
        $active = request()->routeIs('docs') || request()->routeIs('docs.*') || request()->is('dokumentasi*');
        }
        $label = $item['label'];
        @endphp

        <a
          href="{{ route($item['route']) }}"
          class="flex flex-col items-center justify-center py-2"
          style="width:100%;">
          <div
            class="h-9 w-9 rounded-2xl grid place-items-center border"
            style="{{ $active
                      ? 'background:rgba(1,148,243,0.10); border-color:rgba(1,148,243,0.24);'
                      : 'background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.18);' }}">
            <i data-lucide="{{ $item['icon'] }}"
              class="w-4.5 h-4.5"
              style="{{ $active ? 'color:#0194F3;' : 'color:#0f172a;' }}"></i>
          </div>

          <span class="mt-1 text-[11px] leading-none font-semibold {{ $active ? 'text-slate-900' : 'text-slate-700' }} max-w-[56px] truncate">
            {{ $label }}
          </span>

          <span class="mt-0.5 h-[3px] w-7 rounded-full {{ $active ? '' : 'opacity-0' }}" style="background:#0194F3;"></span>
        </a>
        @endforeach
      </div>
    </div>
  </div>
</div>