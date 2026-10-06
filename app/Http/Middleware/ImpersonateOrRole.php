<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: bypass Spatie role check ketika admin sedang impersonate user.
 * Daftarkan di Kernel sebagai 'impersonate_or_role'.
 *
 * Pemakaian di route:  ->middleware('impersonate_or_role:user')
 * Artinya: loloskan jika sedang impersonate ATAU punya role 'user'.
 */
class ImpersonateOrRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika sedang impersonate → bypass role check
        if (session('impersonating_admin_id')) {
            return $next($request);
        }

        // Jika tidak sedang impersonate → cek role seperti biasa (Spatie)
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'User does not have the right roles.');
    }
}
