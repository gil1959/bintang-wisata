<?php

$files = [
    'resources/views/front/umrah/show.blade.php',
    'resources/views/front/tours/show.blade.php',
    'resources/views/front/ships/show.blade.php',
    'resources/views/front/restoran/show.blade.php',
    'resources/views/front/rentcar/show.blade.php',
    'resources/views/front/mice/show.blade.php',
    'resources/views/front/hotel/show.blade.php',
    'resources/views/front/pages/article-show.blade.php'
];

$metaBlock = <<<BLADE

@section('meta')
    @php
        \$pkgOrArticle = isset(\$article) ? \$article : (\$package ?? null);
        \$mTitle = \$pkgOrArticle->seo_title ?? \$pkgOrArticle->title ?? 'Bintang Wisata Holiday';
        \$mDesc = \$pkgOrArticle->seo_description ?? \$pkgOrArticle->short_description ?? \$pkgOrArticle->excerpt ?? 'Liburan impian jadi nyata dengan pelayanan bintang lima.';
        \$mKey = \$pkgOrArticle->seo_keywords ?? 'paket tour, paket wisata, bintang wisata holiday';
        \$mImage = !empty(\$pkgOrArticle->seo_image_path) ? asset('storage/' . \$pkgOrArticle->seo_image_path) : asset('logo-atau-banner.jpg');
        \$sTitle = \$pkgOrArticle->social_title ?? \$mTitle;
        \$sDesc = \$pkgOrArticle->social_description ?? \$mDesc;
    @endphp
    <title>{{ \$mTitle }} | Bintang Wisata Holiday</title>
    <meta name="description" content="{{ \$mDesc }}">
    <meta name="keywords" content="{{ \$mKey }}">
    <meta name="author" content="Bintang Wisata Holiday">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ \$sTitle }}">
    <meta property="og:description" content="{{ \$sDesc }}">
    <meta property="og:image" content="{{ \$mImage }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ \$sTitle }}">
    <meta property="twitter:description" content="{{ \$sDesc }}">
    <meta property="twitter:image" content="{{ \$mImage }}">
@endsection

BLADE;

foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        
        // Remove existing @section('meta') if any
        if (strpos($content, "@section('meta')") !== false) {
            $content = preg_replace("/@section\('meta'\).*?@endsection\s*/s", "", $content);
        }
        
        // Find @extends('layouts.front')
        $pos = strpos($content, "@extends('layouts.front')");
        if ($pos !== false) {
            $insertPos = $pos + strlen("@extends('layouts.front')");
            $new_content = substr($content, 0, $insertPos) . "\n" . $metaBlock . substr($content, $insertPos);
            file_put_contents($f, $new_content);
            echo "Updated {$f}\n";
        } else {
            echo "No extends found in {$f}\n";
        }
    }
}
