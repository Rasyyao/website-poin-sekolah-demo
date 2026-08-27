<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolIsActive
{
    /**
     * Ensure the authenticated user's school has an active/trial subscription.
     * Super admins (no school_id) bypass this check.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        // Super admin has no school — bypass
        if (! $user->school_id) {
            return $next($request);
        }

        $school = $user->school;

        if (! $school || ! $school->isAccessible()) {
            abort(403, 'Langganan sekolah Anda telah kadaluarsa. Hubungi administrator.');
        }

        return $next($request);
    }
}
