<?php

use App\Http\Middleware\CheckBlocked;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsTeacher;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',    // ← Routes API activées
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------
        | Enregistrement des alias de middleware personnalisés
        |
        | Utilisés dans routes/api.php sous forme de chaîne courte :
        |   'is.admin'      → IsAdmin::class
        |   'is.teacher'    → IsTeacher::class
        |   'check.blocked' → CheckBlocked::class
        |--------------------------------------------------------------
        */
        $middleware->alias([
            'is.admin'      => IsAdmin::class,
            'is.teacher'    => IsTeacher::class,
            'check.blocked' => CheckBlocked::class,
        ]);

        /*
        |--------------------------------------------------------------
        | API stateless — désactive la vérification CSRF pour les routes API
        | Sanctum gère l'authentification via Bearer token
        |--------------------------------------------------------------
        */
        $middleware->statefulApi();
        web: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();             // ← Sanctum stateless
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
