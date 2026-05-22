<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// ── Authenticated ──────────────────────────────────────────
Route::middleware(['auth:sanctum', 'auth.api'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);

    // Reservations — teacher
    Route::prefix('reservations')->group(function () {
        Route::post('/',         [ReservationController::class, 'store']);
        Route::get('/available', [ReservationController::class, 'available']);

        // Admin only
        Route::middleware('isAdmin')->group(function () {
            Route::get('/',                [ReservationController::class, 'index']);
            Route::get('/{id}',            [ReservationController::class, 'show']);
            Route::patch('/{id}/validate', [ReservationController::class, 'validate']);
            Route::patch('/{id}/reject',   [ReservationController::class, 'reject']);
        });
    });

    // ── Admin only ─────────────────────────────────────────
    Route::middleware('isAdmin')->group(function () {

        // Statistics
        Route::prefix('statistics')->group(function () {
            Route::get('/usage',            [StatisticsController::class, 'usageStats']);
            Route::get('/materials/top',    [StatisticsController::class, 'topMaterials']);
            Route::get('/loans/monthly',    [StatisticsController::class, 'monthlyLoans']);
            Route::get('/overdue',          [StatisticsController::class, 'overdueLoans']);
            Route::get('/summary',          [StatisticsController::class, 'summary']);
        });

        // Teachers
        Route::prefix('teachers')->group(function () {
            Route::get('/',               [TeacherController::class, 'index']);
            Route::post('/',              [TeacherController::class, 'store']);
            Route::get('/{id}',           [TeacherController::class, 'show']);
            Route::put('/{id}',           [TeacherController::class, 'update']);
            Route::delete('/{id}',        [TeacherController::class, 'destroy']);
            Route::patch('/{id}/block',   [TeacherController::class, 'block']);
            Route::patch('/{id}/unblock', [TeacherController::class, 'unblock']);
            // Teacher loan history
            Route::get('/{id}/loans',     [LoanController::class, 'teacherHistory']);
        });

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/',        [CategoryController::class, 'index']);
            Route::post('/',       [CategoryController::class, 'store']);
            Route::get('/{id}',    [CategoryController::class, 'show']);
            Route::put('/{id}',    [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });

        // Materials
        Route::prefix('materials')->group(function () {
            Route::get('/',              [MaterialController::class, 'index']);
            Route::post('/',             [MaterialController::class, 'store']);
            Route::get('/{id}',          [MaterialController::class, 'show']);
            Route::put('/{id}',          [MaterialController::class, 'update']);
            Route::delete('/{id}',       [MaterialController::class, 'destroy']);
            Route::patch('/{id}/status', [MaterialController::class, 'updateStatus']);
            // Material loan history
            Route::get('/{id}/loans',    [LoanController::class, 'materialHistory']);
        });

        // Loans — immutable records
        Route::prefix('loans')->group(function () {
            Route::get('/',              [LoanController::class, 'index']);
            Route::post('/',             [LoanController::class, 'store']);
            Route::get('/{id}',          [LoanController::class, 'show']);
            Route::patch('/{id}/return', [LoanController::class, 'return']);
            // Explicitly block PUT/DELETE → 405
            Route::put('/{id}',          [LoanController::class, 'update']);
            Route::delete('/{id}',       [LoanController::class, 'destroy']);
        });
    });
});
