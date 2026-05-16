<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IsAdmin
 * Vérifie que l'utilisateur connecté possède le rôle "admin".
 * Retourne 403 Forbidden si ce n'est pas le cas.
 *
 * Usage dans routes/api.php :
 *   Route::middleware(['auth:sanctum', 'check.blocked', 'is.admin'])->group(...)
 */
class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Accès refusé. Droits administrateur requis.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
