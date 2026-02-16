<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Article::published()->latest();

        // SEARCH
        $isEn = app()->getLocale() === 'en';

        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';

            $query->where(function ($q) use ($term, $isEn) {
                if ($isEn) {
                    $q->where('title_en', 'like', $term)
                        ->orWhere('excerpt_en', 'like', $term)
                        ->orWhere('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term);
                } else {
                    $q->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term);
                }
            });
        }


        $items = $query->paginate(9)->withQueryString();

        // Featured (sidebar)
        $featured = Article::published()
            ->latest()
            ->limit(4)
            ->get();

        return view('front.pages.articles', compact('items', 'featured'));
    }

    public function show($slug)
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('front.pages.article-show', compact('article'));
    }
}
