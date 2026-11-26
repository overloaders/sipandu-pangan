<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        // Check if user has any of the required abilities
        foreach ($abilities as $ability) {
            if ($request->user()?->tokenCan($ability)) {
                return $next($request);
            }
        }

        // Check if user has admin ability
        if ($request->user()?->tokenCan('admin')) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Anda tidak memiliki akses untuk melakukan operasi ini',
            'error' => 'forbidden',
            'required_abilities' => $abilities,
        ], 403);
    }
}
