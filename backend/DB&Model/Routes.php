<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('v1')->group(function () {
    Route::apiResource('materials', MaterialController::class);
    Route::patch('materials/{id}/status', [MaterialController::class, 'updateStatus']);
    
    Route::apiResource('categories', CategoryController::class);
});

?>