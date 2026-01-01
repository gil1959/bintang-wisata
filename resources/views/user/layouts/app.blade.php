<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'User Panel') - {{ config('app.name') }}</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">

    {{-- App CSS (Tailwind + design system Azure) --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')

    {{-- Alpine --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Lucide --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        [x-cloak]{ display:none !important; }
        body{ font-family: Nunito, ui-sans-serif, system-ui; }
        a{ text-decoration:none !important; }
    </style>
</head>

<body class="bg-slate-50 antialiased">

<div x-data="{ sidebarOpen:false }" x-init="if (window.lucide) lucide.createIcons()" class="min-h-screen">

    {{-- OVERLAY (MOBILE) --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen=false"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden"
    ></div>

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside
            class="fixed lg:static inset-y-0 left-0 z-50
                   w-[18rem] lg:w-64
                   bg-white/90 backdrop-blur
                   border-r border-slate-200
                   transform transition-transform duration-300
                   lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            {{-- BRAND (logo ikut navbar/front) --}}
            <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center gap-3 min-w-0">
                    <img
                        src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                        alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                        class="h-9 w-auto object-contain"
                    />

                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 leading-tight truncate">User Panel</div>
                        <div class="text-xs text-slate-500 -mt-0.5 truncate">{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}</div>
                    </div>
                </div>

                <button
                    @click="sidebarOpen=false"
                    class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                    aria-label="Tutup menu"
                >
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- NAV (ngikut admin: font-bold, active bg azure translucent) --}}
            <nav class="px-3 py-3 space-y-1 overflow-y-auto" style="max-height:calc(100vh - 4rem)">
                @php
                   $nav = [
  ['label'=>'Dashboard','route'=>'user.dashboard','match'=>'user.dashboard','icon'=>'layout-dashboard'],
  ['label'=>'Orders','route'=>'user.orders','match'=>'user.orders.*','icon'=>'receipt'],

 [
  'label' => 'Affiliate',
  'icon'  => 'badge-percent',
  'match' => 'user.affiliate.*',
  'children' => [
    ['label'=>'Commission','route'=>'user.affiliate.commission','match'=>'user.affiliate.commission','icon'=>'percent'],
    ['label'=>'Links','route'=>'user.affiliate.links','match'=>'user.affiliate.links*','icon'=>'link'],
    ['label'=>'Coupons','route'=>'user.affiliate.coupons','match'=>'user.affiliate.coupons*','icon'=>'ticket'],
    ['label'=>'Orders','route'=>'user.affiliate.orders','match'=>'user.affiliate.orders','icon'=>'shopping-bag'],
    ['label'=>'Withdraw','route'=>'user.withdrawals','match'=>'user.withdrawals*','icon'=>'wallet'],

  ],
],


  ['label'=>'Profile','route'=>'user.profile.edit','match'=>'user.profile.*','icon'=>'user'],
];

                @endphp

                @foreach($nav as $n)
    @php
        $hasChildren = isset($n['children']) && is_array($n['children']) && count($n['children']) > 0;
        $active = request()->routeIs($n['match'] ?? ($n['route'] ?? ''));
        $childActive = false;

        if ($hasChildren) {
            foreach ($n['children'] as $c) {
                if (request()->routeIs($c['match'] ?? ($c['route'] ?? ''))) {
                    $childActive = true;
                    break;
                }
            }
        }
    @endphp

    @if($hasChildren)
        <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
            <button type="button"
                @click="open = !open"
                class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                       {{ $childActive ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
            >
                <span class="flex items-center gap-3 min-w-0">
                    <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                          style="{{ $childActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">

                        <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5"
                           style="{{ $childActive ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                    </span>

                    <span class="font-bold text-sm truncate">{{ $n['label'] }}</span>
                </span>

                <span class="text-xs font-extrabold shrink-0" style="{{ $childActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}">
                    <span x-show="!open">+</span>
                    <span x-show="open">−</span>
                </span>
            </button>

            <div x-show="open" x-collapse class="pl-3 space-y-1">
                @foreach($n['children'] as $c)
                    @php
                        $cActive = request()->routeIs($c['match'] ?? ($c['route'] ?? ''));
                    @endphp

                    <a href="{{ route($c['route']) }}"
                       @click="sidebarOpen=false"
                       class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                              {{ $cActive ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                       style="{{ $cActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
                    >
                        <span class="flex items-center gap-3 min-w-0">
                            <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                                  style="{{ $cActive ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">
                                <i data-lucide="{{ $c['icon'] }}" class="w-5 h-5"
                                   style="{{ $cActive ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                            </span>

                            <span class="font-bold text-sm truncate">{{ $c['label'] }}</span>
                        </span>

                        <span class="text-xs font-extrabold shrink-0" style="{{ $cActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <a href="{{ route($n['route']) }}"
           @click="sidebarOpen=false"
           class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition
                  {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
           style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
        >
            <span class="flex items-center gap-3 min-w-0">
                <span class="h-9 w-9 rounded-xl grid place-items-center border shrink-0"
                      style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.10);border-color:rgba(148,163,184,0.20);' }}">
                    <i data-lucide="{{ $n['icon'] }}" class="w-5 h-5"
                       style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                </span>

                <span class="font-bold text-sm truncate">{{ $n['label'] }}</span>
            </span>

            <span class="text-xs font-extrabold shrink-0" style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">→</span>
        </a>
    @endif
@endforeach


                <div class="pt-3">
                    <div class="h-px bg-slate-200"></div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl border transition text-red-600 hover:bg-red-50"
                            style="border-color:transparent;">
                        <span class="flex items-center gap-3">
                            <span class="h-9 w-9 rounded-xl grid place-items-center border"
                                  style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.18);">
                                <i data-lucide="log-out" class="w-5 h-5" style="color:#ef4444;"></i>
                            </span>
                            <span class="font-bold text-sm">Logout</span>
                        </span>
                        <span class="text-xs font-extrabold">→</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="h-16 bg-white/70 backdrop-blur border-b border-slate-200">
                <div class="h-full px-4 lg:px-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button
                            class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                            @click="sidebarOpen=true"
                            aria-label="Buka menu"
                        >
                            <i data-lucide="menu" class="w-5 h-5"></i>
                        </button>

                        <div class="leading-tight">
                            <div class="text-xs text-slate-500">@yield('page-subtitle', 'Welcome')</div>
                            <div class="text-sm font-extrabold text-slate-900">@yield('page-title', 'User Dashboard')</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}"
                           class="hidden sm:inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-bold bg-white border border-slate-200 hover:bg-slate-50 transition">
                            <i data-lucide="home" class="w-4 h-4" style="color:#0194F3;"></i>
                            Back to Site
                        </a>

                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-xs text-slate-500">Signed in as</div>
                                <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            </div>

                            <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                                 style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                                <span class="font-extrabold" style="color:#0194F3;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-4 lg:p-5">
                <div class="mx-auto w-full max-w-[1160px]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</div>

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) lucide.createIcons();
    });
</script>
</body>
</html>
