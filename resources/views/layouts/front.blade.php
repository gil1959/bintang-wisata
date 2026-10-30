<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">


  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @hasSection('meta')
    @yield('meta')
  @else
  <title>Paket Wisata Ekslusif & Terpercaya | Bintang Wisata</title>
  <meta name="description" content="Rasakan pengalaman liburan tak terlupakan dengan paket tour terbaik dari Bintang Wisata Holiday. Layanan profesional untuk paket wisata domestik dan internasional.">
  <meta name="keywords" content="paket tour, paket wisata, paket tour eksklusif, agen perjalanan wisata, paket liburan keluarga, paket tour domestik, paket tour internasional, bintang wisata holiday, paket wisata premium">
  <meta name="author" content="Bintang Wisata Holiday">
  <meta name="robots" content="index, follow">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://bintangwisataholiday.com/">
  <meta property="og:title" content="Bintang Wisata Holiday | Layanan Wisata Pilihan Keluarga">
  <meta property="og:description" content="Liburan impian jadi nyata dengan pelayanan bintang lima. Pilih paket tour favorit Anda dengan fasilitas lengkap dan kenyamanan maksimal.">
  <meta property="og:image" content="https://bintangwisataholiday.com/logo-atau-banner.jpg">

  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://bintangwisataholiday.com/">
  <meta property="twitter:title" content="Bintang Wisata Holiday | Paket Wisata Terbaik & Profesional">
  <meta property="twitter:description" content="Cari paket tour berkualitas? Bintang Wisata Holiday solusinya. Cek pilihan paket wisata premium terbaru kami di sini!">
  <meta property="twitter:image" content="https://bintangwisataholiday.com/logo-atau-banner.jpg">
  @endif

  <link rel="canonical" href="https://bintangwisataholiday.com/">
  <link rel="sitemap" type="application/xml" title="Sitemap" href="https://bintangwisataholiday.com/sitemap.xml">

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

  {{-- Font Awesome CDN --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  {{-- Alpine (konsisten dengan admin, partner, dan user layout) --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <script defer src="{{ mix('js/app.js') }}"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    [x-cloak] { display: none !important; }
  </style>

  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
  @stack('styles')

  <link rel="manifest" href="/manifest.webmanifest">
  <meta name="theme-color" content="#0194F3">

    <style>
    /* ==========================================================================
       QUILL & RICH TEXT CONTENT RENDERING (Frontend Article & Detail Pages)
       Ensures 1:1 visual match with WYSIWYG editor
       ========================================================================== */

    /* Reset .ql-editor on frontend for clean presentation */
    .ql-snow .ql-editor,
    .article-content {
      padding: 0 !important;
      height: auto !important;
      overflow-y: visible !important;
      font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, sans-serif;
      font-size: 1rem;
      line-height: 1.8;
      color: #334155;
    }

    /* Headings */
    .article-content h1,
    .ql-editor h1,
    .prose h1,
    .quill-content h1 {
      font-size: 2.125rem !important; /* ~34px */
      font-weight: 700 !important;
      line-height: 1.25 !important;
      margin-top: 2rem !important;
      margin-bottom: 0.875rem !important;
      color: #0f172a !important;
      letter-spacing: -0.02em !important;
    }

    .article-content h2,
    .ql-editor h2,
    .prose h2,
    .quill-content h2 {
      font-size: 1.625rem !important; /* ~26px */
      font-weight: 700 !important;
      line-height: 1.3 !important;
      margin-top: 1.75rem !important;
      margin-bottom: 0.75rem !important;
      color: #1e293b !important;
      letter-spacing: -0.01em !important;
    }

    .article-content h3,
    .ql-editor h3,
    .prose h3,
    .quill-content h3 {
      font-size: 1.3rem !important; /* ~21px */
      font-weight: 600 !important;
      line-height: 1.4 !important;
      margin-top: 1.5rem !important;
      margin-bottom: 0.5rem !important;
      color: #334155 !important;
    }

    .article-content h4,
    .ql-editor h4,
    .prose h4,
    .quill-content h4 {
      font-size: 1.125rem !important; /* ~18px */
      font-weight: 600 !important;
      line-height: 1.4 !important;
      margin-top: 1.25rem !important;
      margin-bottom: 0.5rem !important;
      color: #475569 !important;
    }

    /* Paragraphs */
    .article-content p,
    .ql-editor p,
    .prose p,
    .quill-content p {
      margin-bottom: 1.25rem !important;
      line-height: 1.8 !important;
      color: #334155 !important;
    }

    /* Text Alignments */
    .ql-align-justify {
      text-align: justify !important;
      text-justify: inter-word !important;
    }
    .ql-align-center {
      text-align: center !important;
    }
    .ql-align-right {
      text-align: right !important;
    }
    .ql-align-left {
      text-align: left !important;
    }

    /* Font Families */
    .ql-font-serif {
      font-family: Georgia, Cambria, "Times New Roman", Times, serif !important;
    }
    .ql-font-monospace {
      font-family: Monaco, Menlo, Consolas, "Courier New", monospace !important;
    }

    /* Font Sizes */
    .ql-size-small {
      font-size: 0.85em !important;
    }
    .ql-size-large {
      font-size: 1.35em !important;
    }
    .ql-size-huge {
      font-size: 1.85em !important;
    }

    /* Links */
    .article-content a,
    .ql-editor a,
    .prose a,
    .quill-content a {
      color: #0194F3 !important;
      text-decoration: underline !important;
      text-underline-offset: 3px !important;
      text-decoration-thickness: 1.5px !important;
      font-weight: 500 !important;
      transition: color 0.2s ease, text-decoration-color 0.2s ease !important;
      word-break: break-word !important;
    }
    .article-content a:hover,
    .ql-editor a:hover,
    .prose a:hover,
    .quill-content a:hover {
      color: #007fd1 !important;
      text-decoration-color: #007fd1 !important;
    }

    /* Formatting: Bold, Italic, Underline, Strike */
    .article-content strong,
    .ql-editor strong,
    .prose strong {
      font-weight: 700 !important;
      color: #0f172a !important;
    }
    .article-content em,
    .ql-editor em,
    .prose em {
      font-style: italic !important;
    }
    .article-content u,
    .ql-editor u,
    .prose u {
      text-decoration: underline !important;
    }
    .article-content s,
    .article-content strike,
    .ql-editor s,
    .ql-editor strike {
      text-decoration: line-through !important;
    }

    /* Lists (Bullet & Ordered) */
    .article-content ol,
    .article-content ul,
    .ql-editor ol,
    .ql-editor ul,
    .prose ol,
    .prose ul,
    .quill-content ol,
    .quill-content ul {
      padding-left: 1.5rem !important;
      margin-top: 0.75rem !important;
      margin-bottom: 1.25rem !important;
      list-style-position: outside !important;
    }

    .article-content ul,
    .ql-editor ul,
    .prose ul,
    .quill-content ul {
      list-style-type: disc !important;
    }

    .article-content ol,
    .ql-editor ol,
    .prose ol,
    .quill-content ol {
      list-style-type: decimal !important;
    }

    .article-content li,
    .ql-editor li,
    .prose li,
    .quill-content li {
      margin-bottom: 0.35rem !important;
      line-height: 1.7 !important;
      color: #334155 !important;
    }

    /* Quill mixed list support (<ol><li data-list="...">) */
    .article-content ol>li[data-list="bullet"],
    .ql-editor ol>li[data-list="bullet"],
    .prose ol>li[data-list="bullet"],
    .quill-content ol>li[data-list="bullet"] {
      list-style-type: disc !important;
    }

    .article-content ol>li[data-list="ordered"],
    .ql-editor ol>li[data-list="ordered"],
    .prose ol>li[data-list="ordered"],
    .quill-content ol>li[data-list="ordered"] {
      list-style-type: decimal !important;
    }

    /* Quill Indents */
    .ql-indent-1 { padding-left: 2rem !important; }
    .ql-indent-2 { padding-left: 4rem !important; }
    .ql-indent-3 { padding-left: 6rem !important; }
    .ql-indent-4 { padding-left: 8rem !important; }
    .ql-indent-5 { padding-left: 10rem !important; }
    .ql-indent-6 { padding-left: 12rem !important; }
    .ql-indent-7 { padding-left: 14rem !important; }
    .ql-indent-8 { padding-left: 16rem !important; }

    /* Blockquotes */
    .article-content blockquote,
    .ql-editor blockquote,
    .prose blockquote,
    .quill-content blockquote {
      border-left: 4px solid #0194F3 !important;
      background: #f8fafc !important;
      padding: 0.875rem 1.25rem !important;
      border-radius: 0 0.75rem 0.75rem 0 !important;
      font-style: italic !important;
      color: #475569 !important;
      margin: 1.5rem 0 !important;
    }

    /* Images inside content */
    .article-content img,
    .ql-editor img,
    .prose img,
    .quill-content img {
      max-width: 100% !important;
      height: auto !important;
      border-radius: 0.75rem !important;
      margin: 1.5rem auto !important;
      display: block !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06) !important;
    }

    /* Pre & Code */
    .article-content pre,
    .ql-editor pre,
    .prose pre {
      background-color: #0f172a !important;
      color: #f8fafc !important;
      padding: 1rem 1.25rem !important;
      border-radius: 0.75rem !important;
      overflow-x: auto !important;
      font-family: Monaco, Menlo, Consolas, "Courier New", monospace !important;
      font-size: 0.9rem !important;
      margin: 1.25rem 0 !important;
    }

    .article-content code,
    .ql-editor code,
    .prose code {
      background-color: #f1f5f9 !important;
      color: #0f172a !important;
      padding: 0.2rem 0.4rem !important;
      border-radius: 0.375rem !important;
      font-size: 0.875em !important;
    }
    .article-content pre code,
    .ql-editor pre code {
      background-color: transparent !important;
      color: inherit !important;
      padding: 0 !important;
    }
  </style>

  {{-- Tracking (Admin > Settings > General) --}}
  @if(!empty($siteSettings['tracking_head']))
  {!! $siteSettings['tracking_head'] !!}
  @endif

</head>

<body class="bg-slate-50 font-[Poppins] text-slate-800 antialiased">
  @if(session()->has('impersonating_admin_id') || session()->has('impersonator_id'))
    <div class="bg-amber-500 text-white text-xs sm:text-sm font-bold px-4 py-2 flex items-center justify-between sticky top-0 z-50 shadow-md">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>Mode Penyamaran: Anda sedang login sebagai {{ auth()->user()->name ?? 'Pengguna' }}</span>
      </div>
      <a href="{{ route('leave-impersonate') }}" class="bg-white text-amber-700 hover:bg-amber-50 px-3 py-1 rounded-lg text-xs font-extrabold transition">
        Kembali ke Admin
      </a>
    </div>
  @endif
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

    <main class="flex-1 {{ request()->routeIs('home') ? '' : 'pb-24' }} lg:pb-0">
      @yield('content')
    </main>

    @include('front.partials.footer')

    {{-- âœ… Mobile Bottom Nav --}}
    @include('front.partials.mobile-bottom-nav')

    @include('shared.popup-widget')

  </div>

  @yield('scripts')
  @stack('scripts')

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