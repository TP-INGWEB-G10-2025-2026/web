<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Handler d'exceptions global
 *
 * Uniformise toutes les réponses d'erreur de l'API :
 *
 * HTTP | Corps de la réponse
 * -----|-----------------------------------------------------
 * 401  | { "message": "Non authentifié." }
 * 403  | { "message": "Accès refusé." | "Compte bloqué." }
 * 404  | { "message": "Ressource introuvable." }
 * 422  | { "message": "Erreur de validation.", "errors": {...} }
 * 500  | { "message": "Erreur serveur interne." }
 */
class Handler extends ExceptionHandler
{
    /**
     * Champs sensibles jamais inclus dans les logs.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'new_password',
        'new_password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Point central de rendu — toutes les erreurs API passent ici.
     */
    public function render($request, Throwable $e)
    {
        // Appliquer le format JSON uniquement pour les routes API
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    // ─── Handler centralisé ───────────────────────────────────────

    private function handleApiException(Throwable $e): \Illuminate\Http\JsonResponse
    {
        // 401 — Token absent ou invalide
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Non authentifié.',
            ], 401);
        }

        // 403 — Droits insuffisants ou compte bloqué
        if ($e instanceof AccessDeniedHttpException) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Accès refusé.',
            ], 403);
        }

        // 404 — Modèle Eloquent introuvable
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Ressource introuvable.',
            ], 404);
        }

        // 404 — Route introuvable
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Ressource introuvable.',
            ], 404);
        }

        // 422 — Erreurs de validation des Form Requests
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Erreur de validation.',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Autres erreurs HTTP (abort(409), abort(403), etc.)
        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            return response()->json([
                'message' => $e->getMessage() ?: $this->defaultMessage($status),
            ], $status);
        }

        // 500 — En production : message générique. En dev : message réel.
        return response()->json([
            'message' => app()->isProduction()
                ? 'Erreur serveur interne.'
                : $e->getMessage(),
        ], 500);
    }

    // ─── Messages par défaut selon le code HTTP ───────────────────

    private function defaultMessage(int $status): string
    {
        return match ($status) {
            400 => 'Requête invalide.',
            401 => 'Non authentifié.',
            403 => 'Accès refusé.',
            404 => 'Ressource introuvable.',
            405 => 'Méthode non autorisée.',
            409 => 'Conflit de ressource.',
            422 => 'Erreur de validation.',
            429 => 'Trop de requêtes.',
            500 => 'Erreur serveur interne.',
            503 => 'Service indisponible.',
            default => 'Erreur inattendue.',
        };
    }
}
