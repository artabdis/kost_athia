<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomManagementController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\TagihanController;

Route::get('/', function () {
    return view('welcome');
});

// Login routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

// Register
Route::get('/register', function () {
    return view('register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');


// Dashboard
Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->middleware('auth')
    ->name('dashboard');


// =========================================================
// AUTHENTICATED ROUTES
// =========================================================

Route::middleware('auth')->group(function () {

    // =====================================================
    // PEMBAYARAN AWAL
    // JANGAN DIUBAH
    // =====================================================

    Route::get('/payment', [PaymentController::class, 'show'])
        ->name('payment.show');

    Route::post('/payment', [PaymentController::class, 'upload'])
        ->name('payment.upload');

    Route::get('/admin/users/{user}/payment-proof', [PaymentController::class, 'proof'])
        ->name('admin.payment.proof');

    Route::post('/admin/users/{user}/payment', [PaymentController::class, 'verify'])
        ->name('admin.payment.verify');


    // =====================================================
    // ADMIN KELOLA KAMAR
    // =====================================================

    Route::prefix('admin/rooms')
        ->name('admin.rooms.')
        ->controller(RoomManagementController::class)
        ->group(function () {

            Route::get('/', 'index')
                ->name('index');

            Route::get('/create', 'create')
                ->name('create');

            Route::post('/', 'store')
                ->name('store');

            Route::get('/{room}/edit', 'edit')
                ->name('edit');

            Route::put('/{room}', 'update')
                ->name('update');

            Route::delete('/{room}', 'destroy')
                ->name('destroy');
        });


    // =====================================================
    // TAGIHAN PENGHUNI
    // =====================================================

    // Detail tagihan
    Route::get('/tagihan/{tagihan}', [TagihanController::class, 'show'])
        ->name('tagihan.show');

    // Penghuni upload bukti
    Route::post('/tagihan/{tagihan}/bayar', [TagihanController::class, 'bayar'])
        ->name('tagihan.bayar');


    // =====================================================
    // VERIFIKASI TAGIHAN OLEH ADMIN
    // =====================================================

    // Admin melihat bukti tagihan
    Route::get('/admin/tagihan/{tagihan}/proof', [TagihanController::class, 'proof'])
        ->name('admin.tagihan.proof');

    // Admin verifikasi / tolak tagihan
    Route::post('/admin/tagihan/{tagihan}/verify', [TagihanController::class, 'verify'])
        ->name('admin.tagihan.verify');
});


// =========================================================
// KAMAR
// =========================================================

Route::get('/kamar', [RoomsController::class, 'index'])
    ->name('kamar');

Route::get('/kamar/vip', [RoomsController::class, 'showVip'])
    ->name('kamar.vip');

Route::get('/kamar/{id}', [RoomsController::class, 'show'])
    ->name('kamar.detail');

Route::get('/kamar/vip/booking', [RoomsController::class, 'bookingVip'])
    ->name('kamar.vip.booking');

Route::get('/kamar/{id}/booking', [RoomsController::class, 'booking'])
    ->name('kamar.booking');


// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');