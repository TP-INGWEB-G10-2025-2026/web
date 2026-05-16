<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware CheckBlocked
 * Vérifie que le compte de l'utilisateur authentifié n'est pas bloqué.
 * Ce middleware s'applique APRÈS auth:sanctum.
 *
 * Si le compte est bloqué :
 *  - Le token est révoqué (déconnexion forcée)
 *  - Retourne 403 avec message explicite
 *
 * Usage dans routes/api.php :
 *   Route::middleware(['auth:sanctum', 'check.blocked'])->group(...)
 */
class CheckBlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isBlocked()) {
            // Révocation du token courant → déconnexion immédiate
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Compte bloqué. Veuillez contacter un administrateur.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
