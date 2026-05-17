<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CategoryController; // ← Ajouté


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


// ─── Routes protégées ──────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'check.blocked', 'is.admin'])->group(function () {

    // ── Catégories ─────────────────────────────────────────────────────────────
    Route::prefix('categories')->group(function () {

        // GET    /api/v1/categories
        Route::get('/',        [CategoryController::class, 'index']);

        // GET    /api/v1/categories/{id}
        Route::get('/{id}',    [CategoryController::class, 'show']);

        // POST   /api/v1/categories
        Route::post('/',       [CategoryController::class, 'store']);

        // PUT    /api/v1/categories/{id}
        Route::put('/{id}',    [CategoryController::class, 'update']);

        // DELETE /api/v1/categories/{id}
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

});
