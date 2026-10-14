<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
    <div class="flex justify-center mb-6">
        <a href="{{ route('home') }}">
            <img src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                 alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                 class="h-12 w-auto object-contain">
        </a>
    </div>

    {{ $slot }}
</div>
