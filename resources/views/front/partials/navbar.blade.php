<header
  x-data="{ open:false, scrolled:false }"
  x-init="window.addEventListener('scroll', ()=> scrolled = window.scrollY > 10)"
  :class="scrolled ? 'bg-white/80 backdrop-blur border-b border-slate-200' : 'bg-transparent'"
  class="sticky top-0 z-50 transition"
>
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      {{-- kalau ada logo image, ganti di sini --}}
      <div class="h-10 w-10 rounded-2xl bg-brand-500 shadow-soft"></div>
      <div class="leading-tight">
        <div class="font-extrabold text-slate-900">Bintang Wisata</div>
        <div class="text-xs text-slate-500">Travel & Tour</div>
      </div>
    </a>

    <nav class="hidden lg:flex items-center gap-6">
      <a class="navlink {{ request()->routeIs('home') ? 'navlink-active' : '' }}" href="{{ route('home') }}">Home</a>
      <a class="navlink {{ request()->routeIs('tours.index') ? 'navlink-active' : '' }}" href="{{ route('tours.index') }}">Paket Tour</a>
      <a class="navlink {{ request()->routeIs('rentcar.index') ? 'navlink-active' : '' }}" href="{{ route('rentcar.index') }}">Rental</a>
      <a class="navlink {{ request()->routeIs('docs') ? 'navlink-active' : '' }}" href="{{ route('docs') }}">Dokumentasi</a>
      <a class="navlink {{ request()->routeIs('about') ? 'navlink-active' : '' }}" href="{{ route('about') }}">About</a>
      <a class="navlink {{ request()->routeIs('articles') ? 'navlink-active' : '' }}" href="{{ route('articles') }}">Artikel</a>
    </nav>

    <div class="hidden lg:flex items-center gap-3">
      <a class="btn btn-ghost" href="{{ route('tours.index') }}">Lihat Paket</a>
      <a class="btn btn-primary" href="#" aria-label="Contact">Contact</a>
    </div>

    <button class="lg:hidden rounded-xl p-2 border border-slate-200 bg-white/70"
            @click="open = !open" aria-label="Toggle menu">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>

  <div x-show="open" x-transition class="lg:hidden bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-3">
      <a class="navlink" href="{{ route('home') }}">Home</a>
      <a class="navlink" href="{{ route('tours.index') }}">Paket Tour</a>
      <a class="navlink" href="{{ route('rentcar.index') }}">Rental</a>
      <a class="navlink" href="{{ route('docs') }}">Dokumentasi</a>
      <a class="navlink" href="{{ route('about') }}">About</a>
      <a class="navlink" href="{{ route('articles') }}">Artikel</a>

      <div class="pt-2 flex gap-2">
        <a class="btn btn-ghost flex-1" href="{{ route('tours.index') }}">Lihat Paket</a>
        <a class="btn btn-primary flex-1" href="#">Contact</a>
      </div>
    </div>
  </div>
</header>
