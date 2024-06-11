<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(\App\Http\Controllers\API\AuthController::class)->group(function () {
    Route::post('/login','login');
    Route::post('/register','register');
});

Route::prefix('admin')->middleware('admin')->controller(\App\Http\Controllers\API\AdminController::class)->group(function () {
    Route::get('/','show');
});

// Route::prefix('teacher')->group(['middleware' => 'teacher'], function () {
    
// });

// Route::prefix('student')->group(['middleware' => 'student'], function () {
    
// });

// Route::prefix('delegate')->group(['middleware' => 'delegate'], function () {
    
// });