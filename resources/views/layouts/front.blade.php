<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Bintang Wisata'))</title>

    {{-- FONT --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Poppins:wght@400;500;600;700&display=swap">

    {{-- TAILWIND / APP CSS (punya lu sendiri) --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{-- ALPINE --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 font-[Poppins] text-gray-800">
    <div class="min-h-screen flex flex-col">

        {{-- SIMPLE NAVBAR --}}
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-4">
                <a href="{{ route('home') }}" class="text-xl font-bold text-[#0194F3]">
                    Bintang Wisata
                </a>
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

    </div>

    @yield('scripts')
</body>
</html>
