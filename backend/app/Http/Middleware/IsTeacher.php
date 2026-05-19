<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IsTeacher
 * Vérifie que l'utilisateur connecté possède le rôle "teacher".
 * Retourne 403 Forbidden si ce n'est pas le cas.
 *
 * Usage dans routes/api.php :
 *   Route::middleware(['auth:sanctum', 'check.blocked', 'is.teacher'])->group(...)
 */
class IsTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isTeacher()) {
            return response()->json([
                'message' => 'Accès refusé. Droits enseignant requis.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
