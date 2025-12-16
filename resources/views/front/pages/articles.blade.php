@extends('layouts.front')

@section('title', 'Artikel')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-white">
    <div class="absolute inset-0 travel-grid opacity-70"></div>
    <svg class="absolute -top-16 -right-16 w-[520px] h-[520px] opacity-80" viewBox="0 0 600 600" fill="none" aria-hidden="true">
        <defs>
            <radialGradient id="articlesGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(320 280) rotate(90) scale(280)">
                <stop stop-color="#0194F3" stop-opacity="0.20"/>
                <stop offset="1" stop-color="#0194F3" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <circle cx="320" cy="280" r="280" fill="url(#articlesGlow)"/>
        <path d="M140 300c60-70 145-120 245-120 55 0 105 15 160 42" stroke="#0194F3" stroke-opacity="0.22" stroke-width="2" stroke-linecap="round"/>
        <path d="M190 360c70-55 125-80 200-80 55 0 100 12 145 30" stroke="#0194F3" stroke-opacity="0.16" stroke-width="2" stroke-linecap="round"/>
    </svg>

    <div class="max-w-7xl mx-auto px-4 py-14 lg:py-16 relative">
        <div class="max-w-2xl" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-extrabold"
                 style="background: rgba(1,148,243,0.08); border-color: rgba(1,148,243,0.22); color:#055a93;">
                <span class="h-2 w-2 rounded-full" style="background:#0194F3;"></span>
                Artikel & Insight Wisata
            </div>

            <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold text-slate-900">
                Artikel & Insight Wisata
            </h1>
            <p class="mt-3 text-slate-600">
                Pembaruan, tips perjalanan, dan inspirasi wisata untuk pengalaman yang lebih rapi dan nyaman.
            </p>
        </div>
    </div>

    {{-- wave divider --}}
    <svg class="block w-full" viewBox="0 0 1440 100" fill="none" aria-hidden="true">
        <path d="M0 40C180 90 360 90 540 55C720 20 900 20 1080 55C1260 90 1350 85 1440 60V100H0V40Z" fill="#F8FAFC"/>
    </svg>
</section>

{{-- SEARCH & FILTER --}}
<section class="max-w-7xl mx-auto px-4 -mt-10 relative z-10">
    <div class="card p-5" data-aos="fade-up">
        <form method="GET" class="grid gap-4 md:grid-cols-12 items-end">
            <div class="md:col-span-10">
                <label class="block text-sm font-extrabold text-slate-700 mb-2">Pencarian</label>
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari artikel..."
                           class="w-full rounded-xl border-slate-200 pl-11">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </span>
                </div>
            </div>

            <div class="md:col-span-2">
                <button class="btn btn-primary w-full" type="submit">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>
            </div>
        </form>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section class="py-14 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-4 gap-10">

        {{-- LEFT: ARTICLE LIST --}}
        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8" data-aos="fade-up" data-aos-delay="100">
                @forelse($items as $item)
                <article class="group card overflow-hidden">
                    <a href="{{ route('article.show', $item->slug) }}" class="block relative h-52 bg-slate-100 overflow-hidden">
                        <img src="{{ $item->cover_image
                            ? asset('storage/'.$item->cover_image)
                            : asset('images/placeholder.jpg') }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            alt="{{ $item->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/35 via-transparent to-transparent"></div>

                        <div class="absolute top-3 left-3 inline-flex items-center gap-2 rounded-full bg-white/92 border border-slate-200 px-3 py-1 text-xs font-extrabold text-slate-700 shadow">
                            <i data-lucide="calendar" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                            {{ $item->published_at?->format('d M Y') }}
                        </div>
                    </a>

                    <div class="p-6">
                        <h2 class="font-extrabold text-lg mb-3 text-slate-900">
                            <a href="{{ route('article.show', $item->slug) }}" class="hover:underline decoration-[#0194F3]">
                                {{ $item->title }}
                            </a>
                        </h2>

                        <p class="text-slate-600 text-sm mb-4 line-clamp-3">
                            {{ $item->excerpt }}
                        </p>

                        <a href="{{ route('article.show', $item->slug) }}"
                           class="inline-flex items-center gap-2 text-sm font-extrabold"
                           style="color:#0194F3;">
                            Baca Selengkapnya
                            <span class="transition group-hover:translate-x-0.5">→</span>
                        </a>
                    </div>
                </article>
                @empty
                    <div class="card p-10 text-center text-slate-600">
                        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-3" style="color:#0194F3;"></i>
                        Artikel tidak ditemukan.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-12">
                {{ $items->links() }}
            </div>
        </div>

        {{-- RIGHT: SIDEBAR --}}
        <aside class="space-y-6" data-aos="fade-up" data-aos-delay="140">

            {{-- Featured --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-slate-900">Artikel Terbaru</h3>
                    <span class="pill pill-azure">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        Update
                    </span>
                </div>

                <ul class="space-y-4">
                    @foreach($featured as $f)
                    <li>
                        <a href="{{ route('article.show', $f->slug) }}" class="flex gap-3 group">
                            <img src="{{ $f->cover_image
                                ? asset('storage/'.$f->cover_image)
                                : asset('images/placeholder.jpg') }}"
                                class="w-16 h-16 object-cover rounded-xl border border-slate-200"
                                alt="{{ $f->title }}">

                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-slate-900 group-hover:underline decoration-[#0194F3] line-clamp-2">
                                    {{ $f->title }}
                                </p>
                                <span class="text-xs text-slate-500 inline-flex items-center gap-2 mt-1">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5" style="color:#0194F3;"></i>
                                    {{ $f->published_at?->format('d M Y') }}
                                </span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

        </aside>
    </div>
</section>

@endsection
