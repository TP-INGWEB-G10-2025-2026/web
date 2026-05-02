<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

/*adamou 
|--------------------------------------------------------------------------
| API Routes — GMP
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════
//  ROUTES ENSEIGNANTS — middleware auth:sanctum
// ═══════════════════════════════════════════════════════════

Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('teachers')->group(function () {
        // GET    /api/teachers
        Route::get('/',               [TeacherController::class, 'index']);
        // POST   /api/teachers
        Route::post('/',              [TeacherController::class, 'store']);
        // GET    /api/teachers/{id}
        Route::get('/{id}',           [TeacherController::class, 'show']);
        // PUT    /api/teachers/{id}
        Route::put('/{id}',           [TeacherController::class, 'update']);
        // DELETE /api/teachers/{id}
        Route::delete('/{id}',        [TeacherController::class, 'destroy']);
        // PATCH  /api/teachers/{id}/block
        Route::patch('/{id}/block',   [TeacherController::class, 'block']);
        // PATCH  /api/teachers/{id}/unblock
        Route::patch('/{id}/unblock', [TeacherController::class, 'unblock']);
    });
});
