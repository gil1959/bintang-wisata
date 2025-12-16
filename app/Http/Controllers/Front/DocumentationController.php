<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Documentation;

class DocumentationController extends Controller
{
    public function index()
    {
        $photos = Documentation::query()
            ->where('type', 'photo')->where('is_active', true)
            ->orderBy('sort_order')->orderByDesc('created_at')
            ->get();

        $videos = Documentation::query()
            ->where('type', 'video')->where('is_active', true)
            ->orderBy('sort_order')->orderByDesc('created_at')
            ->get();

        return view('front.pages.docs', compact('photos', 'videos'));
    }
}
