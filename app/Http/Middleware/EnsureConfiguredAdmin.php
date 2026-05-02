<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConfiguredAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminEmail = env('ADMIN_EMAIL');
        $user = $request->user();

        if (! $adminEmail || ! $user || $user->email !== $adminEmail) {
            return response()->json([
                'message' => 'No tienes permisos para realizar esta accion.',
            ], 403);
        }

        return $next($request);
    }
}
