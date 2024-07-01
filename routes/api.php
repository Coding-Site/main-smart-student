<?php

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
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('level')->controller(\App\Http\Controllers\API\EducationLevelController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('material')->controller(\App\Http\Controllers\API\MaterialController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('package')->controller(\App\Http\Controllers\API\PackageController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('delegate')->controller(\App\Http\Controllers\API\DelegateController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('teacher')->controller(\App\Http\Controllers\API\TeacherController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('student')->controller(\App\Http\Controllers\API\StudentController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('course')->controller(\App\Http\Controllers\API\CourseController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('bank')->controller(\App\Http\Controllers\API\BankController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
    Route::prefix('exam')->controller(\App\Http\Controllers\API\ExamController::class)->group(function () {
        Route::get('/','index');
        Route::get('/{id}','show');
        Route::post('/store','store');
        Route::post('/update/{id}','update');
        Route::delete('/delete/{id}','destroy');
    });
});
