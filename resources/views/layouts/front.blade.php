<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">


  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', $siteSettings['seo_site_title'] ?? config('app.name', 'Bintang Wisata'))</title>

  <meta name="description" content="@yield('meta_description', $siteSettings['seo_meta_description'] ?? '')">
  <meta name="keywords" content="@yield('meta_keywords', $siteSettings['seo_keywords'] ?? '')">

  <link rel="sitemap" type="application/xml" title="Sitemap" href="https://bintangwisataholiday.com/sitemap.xml">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://bintangwisataholiday.com/">
  <meta property="og:title" content="Paket Tour Murah 2026 - Agen Travel Resmi | Bintang Wisata Holiday">
  <meta property="og:description" content="Cek Harga Promo Paket Tour Terbaru. Agen Travel Resmi, Aman, &amp; Terpercaya. Amankan slot liburanmu sekarang!">
  <meta property="og:image" content="https://bintangwisataholiday.com/logo-atau-banner.jpg">
  <meta property="og:site_name" content="Bintang Wisata Holiday">

  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://bintangwisataholiday.com/">
  <meta property="twitter:title" content="Paket Tour Murah - Agen Travel Resmi | Bintang Wisata">
  <meta property="twitter:description" content="Dapatkan harga paket wisata termurah dan terpercaya hanya di Bintang Wisata Holiday. Promo terbatas!">
  <meta property="twitter:image" content="https://bintangwisataholiday.com/logo-atau-banner.jpg">
  <style>
    .quill-content .ql-align-center {
      text-align: center;
    }

    .quill-content .ql-align-right {
      text-align: right;
    }

    .quill-content .ql-align-justify {
      text-align: justify;
    }

    /* indent dari Quill */
    .quill-content .ql-indent-1 {
      padding-left: 2em;
    }

    .quill-content .ql-indent-2 {
      padding-left: 4em;
    }

    .quill-content .ql-indent-3 {
      padding-left: 6em;
    }

    /* Quill list support */
    .quill-content ol,
    .quill-content ul {
      padding-left: 1.25rem;
      margin: 0.75rem 0;
      list-style-position: outside;
    }

    .quill-content ul {
      list-style-type: disc;
    }

    .quill-content ol {
      list-style-type: decimal;
    }

    .quill-content li {
      margin: 0.25rem 0;
    }

    .quill-content ol>li[data-list="bullet"] {
      list-style-type: disc;
    }

    .quill-content ol>li[data-list="ordered"] {
      list-style-type: decimal;
    }

    .quill-content li[data-list="checked"],
    .quill-content li[data-list="unchecked"] {
      list-style-type: none;
      position: relative;
      padding-left: 1.4rem;
    }

    .quill-content li[data-list="checked"]::before {
      content: "✓";
      position: absolute;
      left: 0;
      top: 0;
    }

    .quill-content li[data-list="unchecked"]::before {
      content: "▢";
      position: absolute;
      left: 0;
      top: 0;
    }
  </style>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "TravelAgency",
      "name": "Bintang Wisata Holiday",
      "image": "https://bintangwisataholiday.com/logo-atau-banner.jpg",
      "url": "https://bintangwisataholiday.com/",
      "telephone": "+628111111752",
      "priceRange": "Rp 500.000 - Rp 50.000.000",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Alamat Kantor Anda",
        "addressLocality": "Jakarta",
        "addressCountry": "ID"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -6.2258357,
        "longitude": 107.0006298
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.9",
        "bestRating": "5",
        "worstRating": "1",
        "reviewCount": "5240"
      },
      "hasOfferCatalog": {
        "@type": "OfferCatalog",
        "name": "Layanan Paket Tour",
        "itemListElement": [{
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Paket Tour Domestik"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Paket Honeymoon Murah"
            }
          }
        ]
      }
    }
  </script>

  {{-- FONT --}}
  <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Poppins:wght@400;500;600;700&display=swap">


  {{-- APP CSS via MIX --}}
  <link rel="preload" as="style" href="{{ mix('css/app.css') }}">
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <script defer src="{{ mix('js/app.js') }}"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

  <link rel="manifest" href="/manifest.webmanifest">
  <meta name="theme-color" content="#0194F3">

  <style>
    /* Quill alignment classes (for rendered content) */
    .ql-align-center {
      text-align: center;
    }

    .ql-align-right {
      text-align: right;
    }

    .ql-align-justify {
      text-align: justify;
    }

    /* indent dari Quill */
    .prose .ql-indent-1 {
      padding-left: 2em;
    }

    .prose .ql-indent-2 {
      padding-left: 4em;
    }

    .prose .ql-indent-3 {
      padding-left: 6em;
    }

    /* ===== Quill LIST SUPPORT (bullet/number) =====
     Quill sering pakai <ol><li data-list="bullet"> ... </li></ol>
     Tanpa CSS ini, bullet/angka bisa nggak keluar / tampil aneh.
  */
    .prose ol,
    .prose ul {
      padding-left: 1.25rem;
      margin: 0.75rem 0;
      list-style-position: outside;
    }

    /* default */
    .prose ul {
      list-style-type: disc;
    }

    .prose ol {
      list-style-type: decimal;
    }

    .prose li {
      margin: 0.25rem 0;
    }

    /* Quill-specific: mixed list types inside <ol> */
    .prose ol>li[data-list="bullet"] {
      list-style-type: disc;
    }

    .prose ol>li[data-list="ordered"] {
      list-style-type: decimal;
    }

    /* Optional: kalau Quill checkbox dipakai */
    .prose li[data-list="checked"],
    .prose li[data-list="unchecked"] {
      list-style-type: none;
      position: relative;
      padding-left: 1.4rem;
    }

    .prose li[data-list="checked"]::before {
      content: "✓";
      position: absolute;
      left: 0;
      top: 0;
    }

    .prose li[data-list="unchecked"]::before {
      content: "▢";
      position: absolute;
      left: 0;
      top: 0;
    }
  </style>

  {{-- Tracking (Admin > Settings > General) --}}
  @if(!empty($siteSettings['tracking_head']))
  {!! $siteSettings['tracking_head'] !!}
  @endif

</head>

<body class="bg-slate-50 font-[Poppins] text-slate-800 antialiased">
  {{-- Tracking (Admin > Settings > General) --}}
  @if(!empty($siteSettings['tracking_body']))
  {!! $siteSettings['tracking_body'] !!}
  @endif

  {{-- Decorative global background (subtle, travel vibe) --}}
  <div class="fixed inset-0 -z-10 pointer-events-none">
    <div class="absolute inset-0 travel-dots opacity-50"></div>

    {{-- top-right glow --}}
    <svg class="absolute -top-28 -right-28 w-[560px] h-[560px] opacity-70" viewBox="0 0 600 600" fill="none" aria-hidden="true">
      <defs>
        <radialGradient id="globalGlow1" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(320 280) rotate(90) scale(290)">
          <stop stop-color="#0194F3" stop-opacity="0.20" />
          <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
        </radialGradient>
      </defs>
      <circle cx="320" cy="280" r="290" fill="url(#globalGlow1)" />
    </svg>

    {{-- bottom-left glow --}}
    <svg class="absolute -bottom-32 -left-32 w-[620px] h-[620px] opacity-60" viewBox="0 0 600 600" fill="none" aria-hidden="true">
      <defs>
        <radialGradient id="globalGlow2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(260 340) rotate(90) scale(300)">
          <stop stop-color="#0194F3" stop-opacity="0.14" />
          <stop offset="1" stop-color="#0194F3" stop-opacity="0" />
        </radialGradient>
      </defs>
      <circle cx="260" cy="340" r="300" fill="url(#globalGlow2)" />
    </svg>
  </div>

  <div class="min-h-screen flex flex-col">
    @include('front.partials.navbar')

    <main class="flex-1 pb-24 lg:pb-0">
      @yield('content')
    </main>

    @include('front.partials.footer')

    {{-- ✅ Mobile Bottom Nav --}}
    @include('front.partials.mobile-bottom-nav')

    @include('shared.popup-widget')

  </div>

  @yield('scripts')

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      duration: 700,
      offset: 80
    });
  </script>

  @if(app()->environment('production'))
  <script>
    if ("serviceWorker" in navigator) {
      window.addEventListener("load", function() {
        navigator.serviceWorker.register("/sw.js");
      });
    }
  </script>
  @endif


  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>



</body>

</html>