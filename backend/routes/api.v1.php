<?php

use App\Http\Controllers\StatisticsController;
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

Route::middleware(['auth:sanctum'])->prefix('statistics')->group(function () {

    // GET /api/v1/statistics/usage
    // GET /api/v1/statistics/usage?period=monthly&year=2026
    Route::get('/usage', [StatisticsController::class, 'usageStats']);

    // GET /api/v1/statistics/materials/top
    Route::get('/materials/top', [StatisticsController::class, 'topMaterials']);
});
