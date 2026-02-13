@php
$isEn = app()->getLocale() === 'en';
$footerAddress = $siteSettings['footer_address'] ?? 'Jl. Raya Kuta No. 88, Bali';
$footerPhone = $siteSettings['footer_phone'] ?? '+62 811-1111-1752';
$footerEmail = $siteSettings['footer_email'] ?? 'info@bintangwisata.id';
$footerWhatsapp = $siteSettings['footer_whatsapp'] ?? '6281111111752';
@endphp


<footer class="relative overflow-hidden bg-slate-950 text-slate-100">
    {{-- Decorative background (brand dominant) --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full blur-3xl opacity-35"
            style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);"></div>
        <div class="absolute -bottom-40 -right-40 h-[520px] w-[520px] rounded-full blur-3xl opacity-25"
            style="background: radial-gradient(circle, #0194F3 0%, transparent 60%);"></div>
        <div class="absolute inset-0 opacity-[0.08]"
            style="background-image: linear-gradient(to right, #0194F3 1px, transparent 1px), linear-gradient(to bottom, #0194F3 1px, transparent 1px); background-size: 42px 42px;"></div>
    </div>

    <div class="relative container mx-auto px-4 py-14">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- Brand --}}
            <div class="lg:col-span-5">
                <div class="flex items-center gap-3">
                    <img src="{{ $siteSettings['site_logo'] ?? asset('images/logo.png') }}"
                        alt="{{ $siteSettings['seo_site_title'] ?? 'Bintang Wisata' }}"
                        class="h-11 w-auto object-contain">

                </div>

                <p class="mt-5 text-slate-300 leading-relaxed max-w-md">
                    {{ $siteSettings['footer_tagline'] ?? ($isEn
        ? 'Your trusted travel partner to explore the beauty of Indonesia. Premium tour packages at friendly prices.'
        : 'Partner perjalanan terpercaya untuk menjelajahi keindahan Indonesia. Paket wisata premium dengan harga bersahabat.'
    ) }}
                </p>




            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-3">
                <h3 class="text-sm font-semibold tracking-wider text-white/90 mb-4">
                    {{ $siteSettings['footer_quick_links_title'] ?? ($isEn ? 'Quick Links' : 'Tautan Cepat') }}
                </h3>

                <ul class="space-y-3 text-slate-300">
                    @php
                    $links = [
                    [
                    'label' => $siteSettings['footer_link1_label'] ?? ($isEn ? 'Home' : 'Beranda'),
                    'url' => $siteSettings['footer_link1_url'] ?? route('home')
                    ],
                    [
                    'label' => $siteSettings['footer_link2_label'] ?? ($isEn ? 'Tour Packages' : 'Paket Tour'),
                    'url' => $siteSettings['footer_link2_url'] ?? route('tours.index')
                    ],
                    [
                    'label' => $siteSettings['footer_link3_label'] ?? ($isEn ? 'Articles' : 'Artikel'),
                    'url' => $siteSettings['footer_link3_url'] ?? route('articles')
                    ],
                    [
                    'label' => $siteSettings['footer_link4_label'] ?? ($isEn ? 'About' : 'Tentang'),
                    'url' => $siteSettings['footer_link4_url'] ?? route('about')
                    ],

                    ['label' => $isEn ? 'Privacy Policy' : 'Kebijakan Privasi', 'url' => route('privacy-policy')],
                    ['label' => $isEn ? 'Terms & Conditions' : 'Syarat & Ketentuan', 'url' => route('terms-conditions')],
                    ['label' => $isEn ? 'Contact' : 'Kontak', 'url' => route('contact')],
                    ];
                    @endphp

                    @foreach($links as $l)
                    @if(!empty($l['label']) && !empty($l['url']))
                    <li>
                        <a class="hover:text-white hover:underline decoration-[#0194F3]" href="{{ $l['url'] }}">
                            {{ $l['label'] }}
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>

            </div>

            {{-- Contact --}}
            <div class="lg:col-span-4">
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-6 shadow-[0_10px_40px_rgba(1,148,243,0.12)]">
                    <h3 class="text-sm font-semibold tracking-wider text-white/90 mb-5">
                        {{ $isEn ? 'Contact Us' : 'Hubungi Kami' }}
                    </h3>

                    <div class="space-y-4 text-slate-200">
                        <div class="flex gap-3">
                            <div class="mt-1 h-9 w-9 rounded-xl grid place-items-center border border-white/10 bg-white/5">
                                <span class="text-[#0194F3]">⌁</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">{{ $isEn ? 'Address' : 'Alamat' }}</div>
                                <div class="leading-snug">{{ $footerAddress }}</div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="mt-1 h-9 w-9 rounded-xl grid place-items-center border border-white/10 bg-white/5">
                                <span class="text-[#0194F3]">✆</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">{{ $isEn ? 'Phone' : 'Telepon' }}</div>

                                <a class="hover:underline decoration-[#0194F3]" href="tel:{{ preg_replace('/\s+/', '', $footerPhone) }}">
                                    {{ $footerPhone }}
                                </a>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="mt-1 h-9 w-9 rounded-xl grid place-items-center border border-white/10 bg-white/5">
                                <span class="text-[#0194F3]">✉</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">{{ $isEn ? 'Email' : 'Email' }}</div>
                                <a class="hover:underline decoration-[#0194F3]" href="mailto:{{ $footerEmail }}">
                                    {{ $footerEmail }}
                                </a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="mt-12 border-t border-white/10 pt-6 text-center text-sm text-slate-400">
            {{ $siteSettings['footer_copyright'] ?? ('© ' . date('Y') . ' Bintang Wisata Indonesia. All rights reserved.') }}
        </div>

    </div>

    {{-- Floating WhatsApp --}}
    <a href="https://wa.me/{{ $footerWhatsapp }}"
        target="_blank"
        rel="noopener"
        class="fixed right-6 h-14 w-14 rounded-full grid place-items-center shadow-lg z-[60]
          bottom-[calc(7.5rem+env(safe-area-inset-bottom))] lg:bottom-6"
        style="background: #0194F3;">
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 32 32"
            width="24"
            height="24"
            fill="white"
            aria-hidden="true">
            <path d="M16.02 3C9.4 3 4 8.38 4 14.98c0 2.64.87 5.2 2.47 7.28L4 29l6.98-2.3a12.03 12.03 0 0 0 5.04 1.08h.01c6.62 0 12.02-5.38 12.02-11.98C28.05 8.38 22.64 3 16.02 3zm0 21.9h-.01a9.92 9.92 0 0 1-4.74-1.2l-.34-.18-4.14 1.36 1.38-4.03-.22-.36a9.9 9.9 0 0 1-1.54-5.33c0-5.45 4.47-9.9 9.96-9.9 5.49 0 9.96 4.45 9.96 9.9 0 5.45-4.47 9.9-9.96 9.9zm5.44-7.41c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.66.15-.19.3-.76.97-.93 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.89-.8-1.49-1.78-1.67-2.08-.17-.3-.02-.46.13-.61.14-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.58-.48-.5-.66-.51l-.56-.01c-.2 0-.52.07-.8.37-.27.3-1.05 1.03-1.05 2.52 0 1.49 1.08 2.93 1.23 3.13.15.2 2.13 3.25 5.16 4.56.72.31 1.29.5 1.73.64.73.23 1.4.2 1.93.12.59-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.08-.12-.27-.2-.57-.35z" />
        </svg>

    </a>

</footer>