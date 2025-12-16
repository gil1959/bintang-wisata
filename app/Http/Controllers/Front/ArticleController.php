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
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->q . '%');
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
