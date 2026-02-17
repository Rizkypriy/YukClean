<?php
// routes/cleaner.php

use App\Http\Controllers\Cleaner\AuthController;
use App\Http\Controllers\Cleaner\DashboardController;
use App\Http\Controllers\Cleaner\TaskController;
use App\Http\Controllers\Cleaner\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CLEANER ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('cleaner')->name('cleaner.')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | GUEST ROUTES (TIDAK PERLU LOGIN)
    |--------------------------------------------------------------------------
    */
    Route::middleware('guest:cleaner')->group(function () {
        // Login
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        
        // Register
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    });

    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES (HARUS LOGIN SEBAGAI CLEANER)
    |--------------------------------------------------------------------------
    */
    Route::middleware('cleaner')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/location', [DashboardController::class, 'updateLocation'])->name('location');
        Route::post('/status', [DashboardController::class, 'updateStatus'])->name('status');

        // Tasks Management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');                 // Daftar semua tugas
            Route::get('/current', [TaskController::class, 'current'])->name('current');      // Tugas aktif
            Route::post('/{task}/accept', [TaskController::class, 'accept'])->name('accept'); // Ambil tugas
            Route::post('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status'); // Update status
            Route::get('/history', [TaskController::class, 'history'])->name('history');      // Riwayat tugas
        });

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');              // Lihat profil
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');            // Form edit profil
            Route::put('/', [ProfileController::class, 'update'])->name('update');            // Update profil
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password'); // Ganti password
            Route::get('/statistics', [ProfileController::class, 'statistics'])->name('statistics'); // Statistik
        });

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});