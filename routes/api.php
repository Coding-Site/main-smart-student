<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(\App\Http\Controllers\API\AuthController::class)->group(function () {
    Route::post('/login','login');
    Route::post('/register','register');
});

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::prefix('data')->controller(\App\Http\Controllers\API\AdminController::class)->group(function () {
        Route::get('/','show');
        Route::post('/update','update');
        Route::post('/reset/passsword','resetPassword');
    });
    Route::prefix('classroom')->controller(\App\Http\Controllers\API\ClassroomController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::put('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('level')->controller(\App\Http\Controllers\API\EducationLevelController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::put('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
});
