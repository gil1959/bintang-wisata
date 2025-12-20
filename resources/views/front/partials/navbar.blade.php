<header
  x-data="{ open:false, scrolled:false }"
  x-init="window.addEventListener('scroll', ()=> scrolled = window.scrollY > 10)"
  class="sticky top-0 z-50 transition"
  :class="scrolled ? 'bg-white/80 backdrop-blur border-b border-slate-200 shadow-sm' : 'bg-transparent'"
>
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between gap-4">

    {{-- BRAND --}}
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      <img
  src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
  alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
  class="h-10 w-auto object-contain"
/>

      
    </a>

    {{-- DESKTOP NAV --}}
    <nav class="hidden lg:flex items-center gap-1">
      @php
       $nav = [
  ['label' => __('front.nav.home'),     'route' => 'home',         'icon' => 'home'],
  ['label' => __('front.nav.tours'),    'route' => 'tours.index',  'icon' => 'map'],
  ['label' => __('front.nav.rental'),   'route' => 'rentcar.index','icon' => 'car'],
  ['label' => __('front.nav.docs'),     'route' => 'docs',         'icon' => 'book-open'],
  ['label' => __('front.nav.about'),    'route' => 'about',        'icon' => 'info'],
  ['label' => __('front.nav.articles'), 'route' => 'articles',     'icon' => 'newspaper'],
];
      @endphp

      @foreach($nav as $n)
        @php $active = request()->routeIs($n['route']); @endphp

        <a
          href="{{ route($n['route']) }}"
          class="group relative px-4 py-2 rounded-xl text-sm font-semibold transition hover:bg-slate-50 flex items-center gap-2
                 {{ $active ? 'text-slate-900' : 'text-slate-700 hover:text-slate-900' }}"
        >
          <i data-lucide="{{ $n['icon'] }}" class="w-4 h-4 {{ $active ? '' : 'text-slate-500' }}" style="{{ $active ? 'color:#0194F3;' : '' }}"></i>
          <span>{{ $n['label'] }}</span>

          {{-- underline --}}
          <span
            class="absolute left-4 right-4 -bottom-0.5 h-[2px] rounded-full transition-all duration-300
                   {{ $active ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}"
            style="background:#0194F3;"
          ></span>
        </a>
      @endforeach
    </nav>

    {{-- RIGHT BUTTON (LOGIN ONLY) --}}
    <div class="hidden lg:flex items-center">
  <div x-data="{ open:false }" class="relative">
    <button @click="open = !open"
      class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition flex items-center gap-2">
      <span>{{ config('app.available_locales')[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}</span>
      <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
      </svg>
    </button>

    <div x-show="open" @click.outside="open=false" x-transition
      class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden z-50">
      @foreach (config('app.available_locales', []) as $code => $label)
        <a href="{{ route('lang.switch', $code) }}"
           class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>
</div>


    {{-- MOBILE TOGGLE --}}
    <button
      class="lg:hidden rounded-xl p-2 border border-slate-200 bg-white/70 hover:bg-white transition"
      @click="open = !open"
      aria-label="Toggle menu"
    >
      <i x-show="!open" data-lucide="menu" class="w-6 h-6"></i>
      <i x-show="open" x-cloak data-lucide="x" class="w-6 h-6"></i>
    </button>
  </div>

  {{-- MOBILE MENU --}}
  <div x-show="open" x-transition.opacity class="lg:hidden">
    <div class="bg-white border-t border-slate-200">
      <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-2">

        @foreach($nav as $n)
          @php $active = request()->routeIs($n['route']); @endphp

          <a
            href="{{ route($n['route']) }}"
            class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition border
                   {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
            style="{{ $active ? 'background:rgba(1,148,243,0.08); border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
          >
            <span class="flex items-center gap-3">
              <span class="h-9 w-9 rounded-xl grid place-items-center"
                    style="{{ $active ? 'background:rgba(1,148,243,0.10);' : 'background:rgba(148,163,184,0.10);' }}">
                <i data-lucide="{{ $n['icon'] }}" class="w-4 h-4" style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
              </span>
              <span>{{ $n['label'] }}</span>
            </span>

            <span class="font-extrabold" style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
          </a>
        @endforeach

        <div x-data="{ open:false }" class="relative">
    <button @click="open = !open"
      class="px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition flex items-center gap-2">
      <span>{{ config('app.available_locales')[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}</span>
      <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
      </svg>
    </button>

    <div x-show="open" @click.outside="open=false" x-transition
      class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden z-50">
      @foreach (config('app.available_locales', []) as $code => $label)
        <a href="{{ route('lang.switch', $code) }}"
           class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </div>

      </div>
    </div>
  </div>
</header>
