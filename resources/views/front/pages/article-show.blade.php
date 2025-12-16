@extends('layouts.front')

@section('title', $article->seo_title ?? $article->title)

@section('meta')
<meta name="description" content="{{ $article->seo_description ?? $article->excerpt }}">
@endsection

@section('content')
<article class="bg-white">

    {{-- Hero --}}
    <div class="relative h-[440px] overflow-hidden">
        <img src="{{ $article->cover_image
            ? asset('storage/'.$article->cover_image)
            : asset('images/placeholder.jpg') }}"
            class="w-full h-full object-cover"
            alt="{{ $article->title }}">

        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/40 to-slate-950/15"></div>

        {{-- subtle travel svg --}}
        <svg class="absolute -top-14 -right-14 w-80 h-80 opacity-70" viewBox="0 0 300 300" fill="none" aria-hidden="true">
            <circle cx="150" cy="150" r="120" fill="#0194F3" fill-opacity="0.12"/>
            <path d="M70 160c35-45 80-70 130-70 20 0 40 4 60 12" stroke="#FFFFFF" stroke-opacity="0.20" stroke-width="3" stroke-linecap="round"/>
            <path d="M95 205c42-34 78-50 115-50 30 0 55 8 80 19" stroke="#FFFFFF" stroke-opacity="0.16" stroke-width="3" stroke-linecap="round"/>
        </svg>

        <div class="absolute inset-0 flex items-center">
            <div class="max-w-7xl mx-auto px-4 w-full">
                <div class="max-w-3xl text-white" data-aos="fade-up">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/20 px-4 py-2 text-xs font-extrabold">
                        <i data-lucide="newspaper" class="w-4 h-4"></i>
                        Artikel
                    </div>

                    <p class="text-sm mt-4 inline-flex items-center gap-2 text-white/85">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ $article->published_at?->format('d M Y') }}
                    </p>

                    <h1 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight">
                        {{ $article->title }}
                    </h1>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 py-14">
        <div class="max-w-3xl mx-auto">

            {{-- Excerpt --}}
            <div class="card p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="icon-badge">
                        <i data-lucide="quote" class="w-5 h-5"></i>
                    </div>
                    <p class="text-base md:text-lg text-slate-700 font-semibold leading-relaxed">
                        {{ $article->excerpt }}
                    </p>
                </div>
            </div>

            {{-- Body --}}
            <div class="prose prose-lg max-w-none">
                {!! $article->content !!}
            </div>

            {{-- Back --}}
            <div class="mt-12">
                <a href="{{ route('articles') }}"
                   class="inline-flex items-center gap-2 text-sm font-extrabold hover:underline decoration-[#0194F3]"
                   style="color:#0194F3;">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Artikel
                </a>
            </div>

            {{-- CTA block (tanpa variabel baru) --}}
            <div class="mt-12 rounded-3xl border border-slate-200 bg-slate-50 p-6 md:p-8 travel-dots">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">Ingin rekomendasi perjalanan yang sesuai?</div>
                        <p class="mt-1 text-sm text-slate-600">Lihat paket tour populer atau gunakan rental untuk perjalanan fleksibel.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('tours.index') }}" class="btn btn-primary">
                            <i data-lucide="map" class="w-4 h-4"></i>
                            Lihat Paket Tour
                        </a>
                        <a href="{{ route('rentcar.index') }}" class="btn btn-ghost">
                            <i data-lucide="car" class="w-4 h-4"></i>
                            Rental Mobil
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</article>
@endsection
