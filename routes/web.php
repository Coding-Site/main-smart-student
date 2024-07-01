<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('file', function() {
    return response()->download(storage_path('app/public/Abdollah-Mansour.pdf'), 'abdoooo.pdf');
});