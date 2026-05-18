<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum') ?? auth('sanctum')->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        if ($user->isBlocked()) {
            return response()->json(['message' => 'Compte bloqué. Contactez un administrateur.'], 403);
        }

        return $next($request);
    }
}
