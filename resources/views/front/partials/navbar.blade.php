<header
  x-data="{ scrolled:false }"
  x-init="window.addEventListener('scroll', ()=> scrolled = window.scrollY > 10)"
  class="sticky top-0 z-50 transition"
  :class="scrolled ? 'bg-white/80 backdrop-blur border-b border-slate-200 shadow-sm' : 'bg-white/60 backdrop-blur'"
>
  <div class="max-w-7xl mx-auto px-4 py-2 lg:py-4 flex items-center gap-3">


    {{-- BRAND --}}
   <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
  <img
    src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
    alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
    class="h-7 lg:h-7 w-12 object-contain shrink-0"
  />
</a>


    {{-- DESKTOP NAV --}}
    <nav class="hidden lg:flex items-center gap-1 flex-nowrap whitespace-nowrap">
      @php
        $nav = [
          ['type' => 'link', 'label' => __('front.nav.home'),     'route' => 'home',         'icon' => 'home'],
          ['type' => 'link', 'label' => __('front.nav.tours'),    'route' => 'tours.index',  'icon' => 'map'],
          ['type' => 'link', 'label' => __('front.nav.rental'),   'route' => 'rentcar.index','icon' => 'car'],
          ['type' => 'link', 'label' => __('front.nav.ship'),     'route' => 'ship.index',   'icon' => 'anchor'],
          ['type' => 'link', 'label' => __('front.nav.umrah'),    'route' => 'umrah.index',  'icon' => 'landmark'],
['type' => 'link', 'label' => __('front.nav.mice'),     'route' => 'mice.index',   'icon' => 'briefcase'],
          // ✅ Documentation harus setelah Umrah
          ['type' => 'docs_dropdown'],

          ['type' => 'link', 'label' => __('front.nav.about'),    'route' => 'about',        'icon' => 'info'],
          ['type' => 'link', 'label' => __('front.nav.articles'), 'route' => 'articles',     'icon' => 'newspaper'],
        ];
      @endphp

      @foreach($nav as $n)
        @if(($n['type'] ?? 'link') === 'link')
          @php $active = request()->routeIs($n['route']); @endphp

          <a
            href="{{ route($n['route']) }}"
            class="group relative px-3 py-2 rounded-xl text-sm font-semibold transition hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap
                   {{ $active ? 'text-slate-900' : 'text-slate-700 hover:text-slate-900' }}"
          >
            <i data-lucide="{{ $n['icon'] }}"
               class="w-4 h-4 {{ $active ? '' : 'text-slate-500' }}"
               style="{{ $active ? 'color:#0194F3;' : '' }}"></i>

            <span>{{ $n['label'] }}</span>

            {{-- underline --}}
            <span
              class="absolute left-3 right-3 -bottom-0.5 h-[2px] rounded-full transition-all duration-300
                     {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"
              style="background:#0194F3;"
            ></span>
          </a>

        @else
          {{-- DROPDOWN: DOKUMENTASI (posisi setelah Umrah) --}}
          @php
            $docsActive = request()->routeIs('docs') || request()->routeIs('docs.*') || request()->is('dokumentasi*');
          @endphp

          <div x-data="{ open:false }"
               class="relative"
               @mouseenter="open=true"
               @mouseleave="open=false">

            <button type="button"
                    class="group relative px-3 py-2 rounded-xl text-sm font-semibold transition hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap
                           {{ $docsActive ? 'text-slate-900' : 'text-slate-700 hover:text-slate-900' }}">
              <i data-lucide="book-open"
                 class="w-4 h-4 {{ $docsActive ? '' : 'text-slate-500' }}"
                 style="{{ $docsActive ? 'color:#0194F3;' : '' }}"></i>

              <span>{{ __('front.nav.docs') }}</span>

              <svg class="w-4 h-4 ml-1 {{ $docsActive ? 'text-slate-700' : 'text-slate-400' }}"
                   viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>

              {{-- underline --}}
              <span
                class="absolute left-3 right-3 -bottom-0.5 h-[2px] rounded-full transition-all duration-300
                       {{ $docsActive ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"
                style="background:#0194F3;"
              ></span>
            </button>

            {{-- dropdown menu --}}
            <div x-show="open" x-transition x-cloak
                 class="absolute left-0 mt-2 w-64 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden z-50">

              <a href="{{ route('docs') }}"
                 class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="map" class="w-4 h-4" style="color:#0194F3;"></i>
                Dokumentasi Tour
              </a>

              <a href="{{ route('docs.ship') }}"
                 class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="anchor" class="w-4 h-4" style="color:#0194F3;"></i>
                Dokumentasi Sewa Kapal
              </a>

              <a href="{{ route('docs.umrah') }}"
                 class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i data-lucide="landmark" class="w-4 h-4" style="color:#0194F3;"></i>
                Dokumentasi Umrah
              </a>

            </div>
          </div>
        @endif
      @endforeach

    </nav>

    {{-- RIGHT: LANG SWITCHER (DESKTOP + MOBILE) + LOGIN (DESKTOP ONLY) --}}
<div class="flex items-center gap-3">
 

  {{-- LOGIN / MASUK (DESKTOP ONLY) --}}
  <div class="hidden lg:flex items-center">
   @auth
  @php
    $u = auth()->user();

    if ($u->hasRole('admin') || $u->hasRole('site_moderator')) {
        $to = route('admin.dashboard');
    } elseif ($u->hasRole('partner')) {
        $to = route('partner.dashboard');
    } else {
        $to = route('user.dashboard');
    }
  @endphp

  <a href="{{ $to }}"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow-sm hover:opacity-95"
     style="background: linear-gradient(90deg, #0194F3 0%, #027DD1 100%);">
    Masuk
  </a>
@else
  <a href="{{ route('login') }}"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow-sm hover:opacity-95"
     style="background: linear-gradient(90deg, #0194F3 0%, #027DD1 100%);">
    Login
  </a>
@endauth


  </div>
</div>


  </div>
</header>
