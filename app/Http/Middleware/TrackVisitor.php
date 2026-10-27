<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && !$request->ajax()) {
            $path = $request->path();
            if (!preg_match('/^(bw-admin|admin|partner|api|_debugbar|storage|livewire)/i', $path)) {
                try {
                    $ip = $request->ip();
                    $date = now()->toDateString();
                    if ($ip) {
                        $visitor = Visitor::firstOrCreate(
                            ['ip_address' => $ip, 'date' => $date],
                            ['hits' => 0]
                        );
                        $visitor->increment('hits');
                    }
                } catch (\Throwable $e) {
                    // Fail silently to avoid breaking visitor requests if DB is unavailable
                }
            }
        }

        return $next($request);
    }
}
