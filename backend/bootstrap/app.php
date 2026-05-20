<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
<<<<<<< HEAD
        
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.api'  => \App\Http\Middleware\Authenticate::class,
            'isAdmin'   => \App\Http\Middleware\IsAdmin::class,
            'isTeacher' => \App\Http\Middleware\IsTeacher::class,
        ]);
=======
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
>>>>>>> main
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 401 — Unauthenticated
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
        });

        // 404 — Not found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Ressource introuvable.'], 404);
            }
        });

        // 422 — Validation
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Erreur de validation.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // 403 — HTTP exceptions
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: match($e->getStatusCode()) {
                        403 => 'Accès refusé.',
                        405 => 'Méthode non autorisée.',
                        default => 'Erreur HTTP.',
                    },
                ], $e->getStatusCode());
            }
        });

        // 500 — Server error
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') && ! $e instanceof ValidationException) {
                return response()->json(['message' => 'Erreur serveur interne.'], 500);
            }
        });
    })->create();
