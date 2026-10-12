<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class MobileAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        abort_if($request->user()->is_suspended,403,'Akun sedang ditangguhkan. Hubungi admin.');
        return $next($request);
    }
}
