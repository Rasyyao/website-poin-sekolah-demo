<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentAuthenticated
{
    /**
     * Ensure the request has an active parent/student session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('parent_student_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('parent.login.form');
        }

        return $next($request);
    }
}
