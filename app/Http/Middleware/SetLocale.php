<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));
        $available = array_keys(config('app.available_locales', []));
        if (in_array($locale, $available, true)) app()->setLocale($locale);
        return $next($request);
    }
}
