<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Pakai: ->middleware('role:Super Admin,HD TA')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        // Kalau route tidak menyebut role apapun, lanjut saja
        if (empty($roles)) {
            return $next($request);
        }

        // Cocokkan role user dengan daftar role di route
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'UNAUTHORIZED. ROLE TIDAK DIIZINKAN.');
        }

        return $next($request);
    }
}
