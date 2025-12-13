<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Bintang Wisata'))</title>

    {{-- FONT --}}
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Poppins:wght@400;500;600;700&display=swap">

    {{-- APP CSS via MIX --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{-- ALPINE JS HARUS DI BAWAH CSS & DEFER --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#0194F3">

</head>

<body class="bg-gray-50 font-[Poppins] text-gray-800">

    <div class="min-h-screen flex flex-col">

        @include('front.partials.navbar')


        <main class="flex-1">
            @yield('content')
        </main>

    </div>

    @yield('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script> AOS.init({ once:true, duration:700, offset:80 }); </script>
<script>
  if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => navigator.serviceWorker.register("/sw.js"));
  }
</script>

</body>
</html>
