<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

   <title>@yield('title', $siteSettings['seo_site_title'] ?? config('app.name', 'Bintang Wisata'))</title>

<meta name="description" content="@yield('meta_description', $siteSettings['seo_meta_description'] ?? '')">
<meta name="keywords" content="@yield('meta_keywords', $siteSettings['seo_keywords'] ?? '')">

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
    
<style>
  /* Quill alignment classes (for rendered content) */
  .ql-align-center { text-align: center; }
  .ql-align-right { text-align: right; }
  .ql-align-justify { text-align: justify; }

  /* optional: keep lists nicer from Quill */
  .prose .ql-indent-1 { padding-left: 2em; }
  .prose .ql-indent-2 { padding-left: 4em; }
  .prose .ql-indent-3 { padding-left: 6em; }
</style>

</head>

<body class="bg-slate-50 font-[Poppins] text-slate-800 antialiased">

    {{-- Decorative global background (subtle, travel vibe) --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute inset-0 travel-dots opacity-50"></div>

        {{-- top-right glow --}}
        <svg class="absolute -top-28 -right-28 w-[560px] h-[560px] opacity-70" viewBox="0 0 600 600" fill="none" aria-hidden="true">
            <defs>
                <radialGradient id="globalGlow1" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(320 280) rotate(90) scale(290)">
                    <stop stop-color="#0194F3" stop-opacity="0.20"/>
                    <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
                </radialGradient>
            </defs>
            <circle cx="320" cy="280" r="290" fill="url(#globalGlow1)"/>
        </svg>

        {{-- bottom-left glow --}}
        <svg class="absolute -bottom-32 -left-32 w-[620px] h-[620px] opacity-60" viewBox="0 0 600 600" fill="none" aria-hidden="true">
            <defs>
                <radialGradient id="globalGlow2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(260 340) rotate(90) scale(300)">
                    <stop stop-color="#0194F3" stop-opacity="0.14"/>
                    <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
                </radialGradient>
            </defs>
            <circle cx="260" cy="340" r="300" fill="url(#globalGlow2)"/>
        </svg>
    </div>

    <div class="min-h-screen flex flex-col">
        @include('front.partials.navbar')

        <main class="flex-1">
            @yield('content')
        </main>

        @include('front.partials.footer')
    </div>

    @yield('scripts')

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script> AOS.init({ once:true, duration:700, offset:80 }); </script>

    <script>
      if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => navigator.serviceWorker.register("/sw.js"));
      }
    </script>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    


</body>
</html>
