<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Admin Panel') - Bintang Wisata</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
</head>
<body class="bg-gray-100 antialiased">
<div class="min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r">
        <div class="h-16 flex items-center px-6 border-b">
            <span class="font-bold text-lg text-emerald-700">
                Admin Panel
            </span>
        </div>

        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.bookings.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.bookings.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Order
            </a>

            <a href="{{ route('admin.bank-accounts.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Pembayaran
            </a>

            <a href="{{ route('admin.tour-packages.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.tour-packages.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Paket Wisata
            </a>

            <a href="{{ route('admin.rental-units.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.rental-units.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Rental
            </a>

            <a href="{{ route('admin.promos.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.promos.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Promo
            </a>

            <a href="{{ route('admin.tour-reviews.index') }}"
               class="block px-6 py-2 text-sm {{ request()->routeIs('admin.tour-reviews.*') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                Komentar Paket
            </a>

            {{-- nanti: Konten Home, Artikel, SEO, Users, Settings --}}
        </nav>
    </aside>

    {{-- MAIN AREA --}}
    <div class="flex-1 flex flex-col">

        {{-- TOP BAR --}}
        <header class="h-16 bg-white border-b flex items-center justify-between px-6">
            <h1 class="text-lg font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>

            <div class="flex items-center gap-4 text-sm">
                <div class="text-right">
                    <div class="font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-gray-500 text-xs">
                        {{ auth()->user()->email }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="text-xs px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600">
                        Log Out
                    </button>
                </form>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
