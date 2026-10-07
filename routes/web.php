<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Register
Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/register', [AuthController::class, 'register']);
