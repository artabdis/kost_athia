<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomsController;

Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

// Register
Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('dashboard');

Route::get('/kamar', [RoomsController::class, 'index'])->name('kamar');
Route::get('/kamar/vip', [RoomsController::class, 'showVip'])->name('kamar.vip');
Route::get('/kamar/{id}', [RoomsController::class, 'show'])->name('kamar.detail');

Route::middleware('auth')->group(function () {
    Route::get('/kamar/vip/booking', [RoomsController::class, 'bookingVip'])->name('kamar.vip.booking');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');