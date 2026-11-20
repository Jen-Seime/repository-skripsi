<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        if (! $request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $request->user()->is_active) {
            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        $userRole = $request->user()->role?->value ?? $request->user()->role;

        if (! in_array($userRole, $role)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses.',
            ], 403);
        }

        return $next($request);
    }
}
