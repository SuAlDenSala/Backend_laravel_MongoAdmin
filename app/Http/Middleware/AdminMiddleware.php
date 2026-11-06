<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request for Admin authentication.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for session auth or Bearer/Token header
        $user = auth()->user();

        if (!$user) {
            $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');
            if ($token) {
                $user = \App\Models\User::where('api_token', $token)->first();
            }
        }

        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin privileges required.'
                ], 403);
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'Admin authorization required to access this dashboard.']);
        }

        return $next($request);
    }
}
