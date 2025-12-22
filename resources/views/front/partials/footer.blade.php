@php
  $footerAddress  = $siteSettings['footer_address'] ?? 'Jl. Raya Kuta No. 88, Bali';
  $footerPhone    = $siteSettings['footer_phone'] ?? '+62 811-1111-1752';
  $footerEmail    = $siteSettings['footer_email'] ?? 'info@bintangwisata.id';
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
    {{ $siteSettings['footer_tagline'] ?? 'Partner perjalanan terpercaya untuk menjelajahi keindahan Indonesia. Paket wisata premium dengan harga bersahabat.' }}
</p>



                
            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-3">
                <h3 class="text-sm font-semibold tracking-wider text-white/90 mb-4">
    {{ $siteSettings['footer_quick_links_title'] ?? 'Tautan Cepat' }}
</h3>

               <ul class="space-y-3 text-slate-300">
    @php
        $links = [
            ['label' => $siteSettings['footer_link1_label'] ?? 'Beranda',    'url' => $siteSettings['footer_link1_url'] ?? route('home')],
            ['label' => $siteSettings['footer_link2_label'] ?? 'Paket Tour', 'url' => $siteSettings['footer_link2_url'] ?? route('tours.index')],
            ['label' => $siteSettings['footer_link3_label'] ?? 'Artikel',    'url' => $siteSettings['footer_link3_url'] ?? route('articles')],
            ['label' => $siteSettings['footer_link4_label'] ?? 'Tentang',    'url' => $siteSettings['footer_link4_url'] ?? route('about')],
              ['label' => 'Privacy Policy', 'url' => route('privacy-policy')],
    ['label' => 'Terms & Conditions', 'url' => route('terms-conditions')],
    ['label' => 'Contact', 'url' => route('contact')],
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
                        Hubungi Kami
                    </h3>

                    <div class="space-y-4 text-slate-200">
                        <div class="flex gap-3">
                            <div class="mt-1 h-9 w-9 rounded-xl grid place-items-center border border-white/10 bg-white/5">
                                <span class="text-[#0194F3]">⌁</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Alamat</div>
                                <div class="leading-snug">{{ $footerAddress }}</div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="mt-1 h-9 w-9 rounded-xl grid place-items-center border border-white/10 bg-white/5">
                                <span class="text-[#0194F3]">✆</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Telepon</div>
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
                                <div class="text-xs text-slate-400">Email</div>
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
       class="fixed bottom-6 right-6 h-14 w-14 rounded-full grid place-items-center shadow-lg"
       style="background: #0194F3;">
        <span class="text-white text-xl">☎</span>
    </a>
</footer>
