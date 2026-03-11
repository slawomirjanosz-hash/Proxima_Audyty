<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param string ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $allowed = array_map('strtolower', $roles);
        $currentRole = strtolower((string) $user->role?->value);

        if ($currentRole === 'super_admin') {
            return $next($request);
        }

        if (! in_array($currentRole, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
