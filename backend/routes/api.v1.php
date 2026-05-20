<?php

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


Route::get("/",function (): JsonResponse{
    return response()->json([
        'messsage' => 'hello',

    ], 200);
});

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

Route::apiResource('materials', MaterialController::class);


// ── Public ────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

//Routes categories
Route::apiResource('categories', CategoryController::class);

// ── Authenticated ──────────────────────────────────────────
Route::middleware(['auth:sanctum', 'auth.api'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);



    // ── Admin only ─────────────────────────────────────────
    Route::middleware('isAdmin')->group(function () {


        // Teachers
        Route::prefix('teachers')->group(function () {
            Route::get('/',               [TeacherController::class, 'index']);
            Route::post('/',              [TeacherController::class, 'store']);
            Route::get('/{id}',           [TeacherController::class, 'show']);
            Route::put('/{id}',           [TeacherController::class, 'update']);
            Route::delete('/{id}',        [TeacherController::class, 'destroy']);
            Route::patch('/{id}/block',   [TeacherController::class, 'block']);
            Route::patch('/{id}/unblock', [TeacherController::class, 'unblock']);

        });


    

    });

});
