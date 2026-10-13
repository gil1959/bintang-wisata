<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class MobileVerificationSignature
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasValidSignature()) {
            if ($request->expectsJson()) throw new InvalidSignatureException;
            return response()->view('auth.mobile-verified',['verified'=>false],403);
        }
        return $next($request);
    }
}
