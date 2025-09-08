<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Pakai di route: ->middleware('role:super_admin,hd_ta')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(401); // belum login
        }

        // Tanpa parameter = lolos saja
        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak punya akses untuk aksi ini.');
        }

        return $next($request);
    }
}
