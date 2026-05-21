<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Middleware\Authenticate;
use App\Middleware\IsAdmin;
use App\Middleware\IsTeacher;

Route::post('/auth/login', 'AuthController@login');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/user', 'UserController@getUser');
});

Route::middleware([Authenticate::class, IsAdmin::class])->group(function () {
    Route::get('/admin', 'AdminController@getAdmin');
});

Route::middleware([Authenticate::class, IsTeacher::class])->group(function () {
    Route::get('/teacher', 'TeacherController@getTeacher');
});

?>