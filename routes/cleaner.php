<?php
// routes/cleaner.php

use App\Http\Controllers\Cleaner\AuthController;
use App\Http\Controllers\Cleaner\DashboardController;
use App\Http\Controllers\Cleaner\TaskController;
use App\Http\Controllers\Cleaner\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cleaner Routes
|--------------------------------------------------------------------------
*/
// Tambahkan route test di paling atas
Route::prefix('cleaner')->name('cleaner.')->group(function () {
    
    // Guest routes (untuk cleaner yang belum login)
    Route::middleware('guest:cleaner')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });

    // Protected routes (untuk cleaner yang sudah login)
    Route::middleware('cleaner')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/location', [DashboardController::class, 'updateLocation'])->name('location');
        Route::post('/status', [DashboardController::class, 'updateStatus'])->name('status');

        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::get('/current', [TaskController::class, 'current'])->name('current');
            Route::post('/{task}/accept', [TaskController::class, 'accept'])->name('accept');
            Route::post('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
            Route::get('/history', [TaskController::class, 'history'])->name('history');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics');
        });

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});