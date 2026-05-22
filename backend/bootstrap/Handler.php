<?php

class ErrorHandler
{
    public function handle($request, $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json(['message' => 'Erreur de validation', 'errors' => $e->validator->errors()], 422);
            }
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json(['message' => 'Ressource introuvable'], 404);
            }
            return response()->json(['message' => 'Erreur serveur interne'], 500);
        }
    }
}

?>