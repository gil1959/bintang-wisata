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
        src="{{ asset('images/logo.png') }}"
        alt="Bintang Wisata"
        class="h-10 w-auto object-contain"
      />
      
    </a>

    {{-- DESKTOP NAV --}}
    <nav class="hidden lg:flex items-center gap-1">
      @php
        $nav = [
          ['label'=>'Home', 'route'=>'home', 'icon'=>'home'],
          ['label'=>'Paket Tour', 'route'=>'tours.index', 'icon'=>'map'],
          ['label'=>'Rental', 'route'=>'rentcar.index', 'icon'=>'car'],
          ['label'=>'Dokumentasi', 'route'=>'docs', 'icon'=>'book-open'],
          ['label'=>'About', 'route'=>'about', 'icon'=>'info'],
          ['label'=>'Artikel', 'route'=>'articles', 'icon'=>'newspaper'],
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
    <div class="hidden lg:flex items-center gap-2">
      <a
        href="{{ route('login') }}"
        class="btn btn-primary px-5 py-2.5"
      >
        <i data-lucide="log-in" class="w-4 h-4"></i>
        Login
      </a>
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

        <div class="pt-2">
          <a
            href="{{ route('login') }}"
            class="btn btn-primary w-full"
          >
            <i data-lucide="log-in" class="w-4 h-4"></i>
            Login
          </a>
        </div>

      </div>
    </div>
  </div>
</header>
