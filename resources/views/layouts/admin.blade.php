<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>@yield('title', 'Admin Panel') - Bintang Wisata</title>

    {{-- Fonts --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700;800&display=swap">

    {{-- App CSS (Tailwind) --}}
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

<body class="bg-slate-50 antialiased admin-ui">

<div
    x-data="{ sidebarOpen:false }"
    x-init="if (window.lucide) lucide.createIcons()"
    class="min-h-screen"
>

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
            {{-- BRAND --}}
            <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl grid place-items-center border"
                         style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                        <i data-lucide="shield" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 leading-tight truncate">Admin Panel</div>
                        <div class="text-xs text-slate-500 -mt-0.5 truncate">Bintang Wisata</div>
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

            {{-- NAV --}}
            @php
    $nav = [
        ['label'=>'Dashboard','route'=>'admin.dashboard','match'=>'admin.dashboard','icon'=>'layout-dashboard'],
        ['label'=>'Orders','route'=>'admin.orders.index','match'=>'admin.orders.*','icon'=>'shopping-bag'],
        ['label'=>'Pembayaran','route'=>'admin.payments.index','match'=>'admin.payments.*','icon'=>'credit-card'],

        // GROUP: Paket Wisata
        [
            'label' => 'Paket Wisata',
            'icon'  => 'map',
            'children' => [
                ['label'=>'Paket Wisata','route'=>'admin.tour-packages.index','match'=>'admin.tour-packages.*','icon'=>'map'],
                ['label'=>'Kategori Tour','route'=>'admin.categories.index','match'=>'admin.categories.*','icon'=>'tags'],
            ],
        ],

        // GROUP: Rental
        [
            'label' => 'Rental',
            'icon'  => 'car',
            'children' => [
                ['label'=>'Rental','route'=>'admin.rent-car-packages.index','match'=>'admin.rent-car-packages.*','icon'=>'car'],
                ['label'=>'Kategori Rental','route'=>'admin.rent-car-categories.index','match'=>'admin.rent-car-categories.*','icon'=>'tags'],
            ],
        ],

        ['label'=>'Promo','route'=>'admin.promos.index','match'=>'admin.promos.*','icon'=>'ticket-percent'],
        ['label'=>'Dokumentasi','route'=>'admin.documentations.index','match'=>'admin.documentations.*','icon'=>'images'],
        ['label'=>'Inspirasi Destinasi','route'=>'admin.destination-inspirations.index','match'=>'admin.destination-inspirations.*','icon'=>'sparkles'],
        ['label'=>'Artikel','route'=>'admin.articles.index','match'=>'admin.articles.*','icon'=>'newspaper'],
        ['label'=>'Home Promo Tours','route'=>'admin.home-sections.promo-tours.edit','match'=>'admin.home-sections.promo-tours.*','icon'=>'sparkles'],
        ['label'=>'Halaman Legal','route'=>'admin.legal-pages.edit','match'=>'admin.legal-pages.*','icon'=>'file-text'],

        // GROUP: Sewa Kapal
        [
            'label' => 'Sewa Kapal',
            'icon'  => 'ship',
            'children' => [
                ['label'=>'Paket Sewa Kapal','route'=>'admin.ship-packages.index','match'=>'admin.ship-packages.*','icon'=>'ship'],
                ['label'=>'Kategori Sewa Kapal','route'=>'admin.ship-categories.index','match'=>'admin.ship-categories.*','icon'=>'tags'],
            ],
        ],

        // GROUP: Umrah
        [
            'label' => 'Umrah',
            'icon'  => 'landmark',
            'children' => [
                ['label'=>'Paket Umrah','route'=>'admin.umrah-packages.index','match'=>'admin.umrah-packages.*','icon'=>'landmark'],
                ['label'=>'Kategori Umrah','route'=>'admin.umrah-categories.index','match'=>'admin.umrah-categories.*','icon'=>'tags'],
            ],
        ],

        [
    'label' => 'MICE',
    'icon'  => 'briefcase',
    'children' => [
        ['label'=>'Paket MICE','route'=>'admin.mice-packages.index','match'=>'admin.mice-packages.*','icon'=>'briefcase'],
        ['label'=>'Kategori MICE','route'=>'admin.mice-categories.index','match'=>'admin.mice-categories.*','icon'=>'tags'],
    ],
],

        ['label'=>'Client Logos','route'=>'admin.client-logos.index','match'=>'admin.client-logos.*','icon'=>'badge-check'],
        ['label'=>'Komentar Paket','route'=>'admin.reviews.index','match'=>'admin.reviews.*','icon'=>'message-circle'],
        ['label'=>'SEO','route'=>'admin.seo.edit','match'=>'admin.seo.*','icon'=>'globe'],
        [
    'label' => 'Users',
    'icon'  => 'users',
    'children' => [
        ['label'=>'All Users','route'=>'admin.users.index','match'=>'admin.users.index','icon'=>'users'],
        ['label'=>'Affiliate Requests','route'=>'admin.affiliate.requests.index','match'=>'admin.affiliate.requests.*','icon'=>'badge-percent'],
['label'=>'Affiliate Orders','route'=>'admin.affiliate.orders.index','match'=>'admin.affiliate.orders.*','icon'=>'receipt'],
['label'=>'Affiliate Withdrawals','route'=>'admin.affiliate.withdrawals.index','match'=>'admin.affiliate.withdrawals.*','icon'=>'wallet'],

    ],
],
// GROUP: Partner
[
    'label' => 'Partner',
    'icon'  => 'handshake',
    'children' => [
        ['label'=>'Partner Applications','route'=>'admin.partners.applications.index','match'=>'admin.partners.applications.*','icon'=>'user-check'],
        ['label'=>'Partner Users','route'=>'admin.partners.users.index','match'=>'admin.partners.users.*','icon'=>'users'],
        ['label'=>'Produk Partner','route'=>'admin.partners.products.index','match'=>'admin.partners.products.*','icon'=>'package'],
['label'=>'Partner Withdrawals','route'=>'admin.partner_withdrawals.index','match'=>'admin.partner_withdrawals.*','icon'=>'wallet'],

    ],
],



        ['label'=>'Settings','route'=>'admin.settings.general','match'=>'admin.settings.*','icon'=>'settings'],
        ['label'=>'Profil','route'=>'admin.profile.edit','match'=>'admin.profile.*','icon'=>'user'],
    ];
@endphp


            <nav class="px-3 py-3 space-y-1 overflow-y-auto" style="max-height:calc(100vh - 4rem)">
    @foreach($nav as $n)

        {{-- CASE 1: GROUP / HAS CHILDREN --}}
        @if(isset($n['children']) && is_array($n['children']))
            @php
                $childActive = false;
                foreach($n['children'] as $c){
                    if(request()->routeIs($c['match'])){
                        $childActive = true;
                        break;
                    }
                }
            @endphp

            <div x-data="{ open: {{ $childActive ? 'true' : 'false' }} }" class="space-y-1">
                {{-- Parent button --}}
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

                    <span class="shrink-0">
                        <i data-lucide="chevron-down"
                           class="w-4 h-4 transition-transform"
                           :class="open ? 'rotate-180' : ''"
                           style="{{ $childActive ? 'color:#0194F3;' : 'color:#94a3b8;' }}"
                        ></i>
                    </span>
                </button>

                {{-- Children --}}
                <div x-show="open" x-collapse class="pl-3">
                    <div class="space-y-1 border-l border-slate-200 ml-3 pl-3">
                        @foreach($n['children'] as $c)
                            @php $active = request()->routeIs($c['match']); @endphp

                            <a href="{{ route($c['route']) }}"
                               @click="sidebarOpen=false"
                               class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl border transition
                                      {{ $active ? 'text-slate-900' : 'text-slate-700 hover:bg-slate-50' }}"
                               style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'border-color:transparent;' }}"
                            >
                                <span class="flex items-center gap-3 min-w-0">
                                    <span class="h-8 w-8 rounded-xl grid place-items-center border shrink-0"
                                          style="{{ $active ? 'background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22);' : 'background:rgba(148,163,184,0.08);border-color:rgba(148,163,184,0.16);' }}">
                                        <i data-lucide="{{ $c['icon'] ?? 'dot' }}" class="w-4 h-4"
                                           style="{{ $active ? 'color:#0194F3;' : 'color:#64748b;' }}"></i>
                                    </span>

                                    <span class="font-bold text-sm truncate">{{ $c['label'] }}</span>
                                </span>

                                <span class="text-xs font-extrabold shrink-0"
                                      style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">
                                    →
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- CASE 2: NORMAL ITEM --}}
        @else
            @php $active = request()->routeIs($n['match']); @endphp

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

                <span class="text-xs font-extrabold shrink-0"
                      style="{{ $active ? 'color:#0194F3;' : 'color:#94a3b8;' }}">
                    →
                </span>
            </a>
        @endif

    @endforeach
</nav>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 min-w-0 flex flex-col">

            {{-- TOPBAR --}}
            <header class="h-16 bg-white/90 backdrop-blur border-b border-slate-200 px-4 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        @click="sidebarOpen=true"
                        class="lg:hidden h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 grid place-items-center"
                        aria-label="Buka menu"
                    >
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div class="hidden sm:flex h-9 w-9 rounded-xl border items-center justify-center"
                         style="background:rgba(1,148,243,0.10);border-color:rgba(1,148,243,0.22)">
                        <i data-lucide="sparkles" class="w-5 h-5" style="color:#0194F3;"></i>
                    </div>

                    <div class="min-w-0">
                        <h1 class="font-extrabold text-slate-900 truncate">
                            @yield('page-title','Dashboard')
                        </h1>
                        <div class="text-xs text-slate-500 hidden sm:block">
                            Kelola konten dan transaksi
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right">
                        <div class="font-bold text-slate-900">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold text-white transition"
                            style="background:#ef4444"
                            onmouseover="this.style.background='#dc2626'"
                            onmouseout="this.style.background='#ef4444'"
                        >
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Log Out</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 p-4 lg:p-5">
                <div class="mx-auto w-full max-w-[1180px]">
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
