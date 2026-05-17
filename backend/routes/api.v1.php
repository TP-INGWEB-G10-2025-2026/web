<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


Route::get("/status", function (): JsonResponse {

    $dbStatus = 'ok';
    $mysqlVersion = null;

    try {
        DB::connection()->getPdo();
        $mysqlVersion = DB::select("select version() as v")[0]->v;
    } catch (Exception $e) {
        $dbStatus = 'error:'.$e->getMessage();
    }

    return response()->json([
        'api_status' => 'ok',
        'database_status' => $dbStatus,
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'mysql_version' => $mysqlVersion,
        'server_time' => now()->toDateTimeString(),
    ], 200);
});



/*
|--------------------------------------------------------------------------
| API Routes — GMP (Gestion du Matériel Pédagogique)
|--------------------------------------------------------------------------
|
| Stack middleware appliqué :
|
|   Publiques      → aucun middleware
|   Authentifiées  → auth:sanctum  +  check.blocked
|   Admin only     → auth:sanctum  +  check.blocked  +  is.admin
|   Teacher only   → auth:sanctum  +  check.blocked  +  is.teacher
|
| En-tête requis pour les routes authentifiées :
|   Authorization: Bearer {token}
|   Accept: application/json
|
*/

// ════════════════════════════════════════════════════════════════
//  ROUTES PUBLIQUES — aucun middleware
// ════════════════════════════════════════════════════════════════

Route::prefix('auth')->group(function () {
    // POST /api/auth/login
    Route::post('/login', [AuthController::class, 'login']);
});


// ════════════════════════════════════════════════════════════════
//  ROUTES AUTHENTIFIÉES — auth:sanctum + check.blocked
// ════════════════════════════════════════════════════════════════

Route::middleware(['auth:sanctum', 'check.blocked'])->group(function () {

    // ── Authentification ─────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        // POST /api/auth/logout
        Route::post('/logout', [AuthController::class, 'logout']);
        // GET  /api/auth/me
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // ── Profil (tous les utilisateurs connectés) ─────────────────
    Route::prefix('profile')->group(function () {
        // GET /api/profile
        //Route::get('/',  [\App\Http\Controllers\ProfileController::class, 'show']);
        // PUT /api/profile
        //Route::put('/',  [\App\Http\Controllers\ProfileController::class, 'update']);
    });


    // ════════════════════════════════════════════════════════════════
    //  ROUTES ADMIN UNIQUEMENT — + is.admin
    // ════════════════════════════════════════════════════════════════

    Route::middleware('is.admin')->group(function () {

        // ── Gestion des enseignants (Tâche #4) ───────────────────
        Route::prefix('teachers')->group(function () {
            // GET    /api/teachers
            Route::get('/',               [\App\Http\Controllers\TeacherController::class, 'index']);
            // GET    /api/teachers/{id}
            Route::get('/{id}',           [\App\Http\Controllers\TeacherController::class, 'show']);
            // POST   /api/teachers
            Route::post('/',              [\App\Http\Controllers\TeacherController::class, 'store']);
            // PUT    /api/teachers/{id}
            Route::put('/{id}',           [\App\Http\Controllers\TeacherController::class, 'update']);
            // DELETE /api/teachers/{id}
            Route::delete('/{id}',        [\App\Http\Controllers\TeacherController::class, 'destroy']);
            // PATCH  /api/teachers/{id}/block
            Route::patch('/{id}/block',   [\App\Http\Controllers\TeacherController::class, 'block']);
            // PATCH  /api/teachers/{id}/unblock
            Route::patch('/{id}/unblock', [\App\Http\Controllers\TeacherController::class, 'unblock']);
        });

        /*
        |--------------------------------------------------------------
        | Tâche #2 — Matériels & Catégories (à compléter)
        |--------------------------------------------------------------
        | Route::apiResource('categories', CategoryController::class);
        | Route::apiResource('materials',  MaterialController::class);
        | Route::patch('materials/{id}/status', [MaterialController::class, 'updateStatus']);
        */

        /*
        |--------------------------------------------------------------
        | Tâche #3 — Réservations admin (à compléter)
        |--------------------------------------------------------------
        | Route::get('reservations',                 [ReservationController::class, 'index']);
        | Route::get('reservations/{id}',            [ReservationController::class, 'show']);
        | Route::patch('reservations/{id}/validate', [ReservationController::class, 'validate']);
        | Route::patch('reservations/{id}/reject',   [ReservationController::class, 'reject']);
        */

        /*
        |--------------------------------------------------------------
        | Tâche #4 — Prêts admin (à compléter)
        |--------------------------------------------------------------
        | Route::get('loans',              [LoanController::class, 'index']);
        | Route::post('loans',             [LoanController::class, 'store']);
        | Route::patch('loans/{id}/return',[LoanController::class, 'return']);
        | Route::get('loans/statistics',   [LoanController::class, 'statistics']);
        */
    });


    // ════════════════════════════════════════════════════════════════
    //  ROUTES ENSEIGNANT — + is.teacher
    // ════════════════════════════════════════════════════════════════

    Route::middleware('is.teacher')->group(function () {

        /*
        |--------------------------------------------------------------
        | Tâche #3 — Réservations enseignant (à compléter)
        |--------------------------------------------------------------
        | Route::get('reservations',               [ReservationController::class, 'index']);
        | Route::post('reservations',              [ReservationController::class, 'store']);
        | Route::get('reservations/{id}',          [ReservationController::class, 'show']);
        | Route::patch('reservations/{id}/cancel', [ReservationController::class, 'cancel']);
        */

        /*
        |--------------------------------------------------------------
        | Tâche #4 — Historique prêts enseignant (à compléter)
        |--------------------------------------------------------------
        | Route::get('loans/my-history', [LoanController::class, 'myHistory']);
        */
    });
});
