<?php

use App\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/materials', [MaterialController::class, 'index']);
});