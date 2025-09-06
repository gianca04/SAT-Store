<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!auth('sanctum')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no válido o no proporcionado',
                    'error' => 'Unauthorized'
                ], 401);
            }

            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
                'error' => 'Unauthorized'
            ], 401);
        }
    }
}
