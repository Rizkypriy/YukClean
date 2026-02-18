<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Cleaner\AuthController as CleanerAuthController;
use App\Http\Controllers\Cleaner\DashboardController as CleanerDashboardController;
use App\Http\Controllers\Cleaner\TaskController as CleanerTaskController;
use App\Http\Controllers\Cleaner\ProfileController as CleanerProfileController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function() {
    return view('auth.landing');
})->name('login.landing');

// Tambahkan ini di bawah landing page
Route::get('/login', function() {
    return redirect()->route('user.login');
})->name('login');

// Route logout manual
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('user')->name('user.')->group(function () {
    
    // ===== GUEST ROUTES (USER) =====
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    });

    // ===== PROTECTED ROUTES (USER) =====
    Route::middleware('auth')->group(function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/search', [HomeController::class, 'search'])->name('search');
        
        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create/service/{service}', [OrderController::class, 'create'])->name('create');
            Route::get('/create/bundle/{bundle}', [OrderController::class, 'createBundle'])->name('create.bundle');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::get('/{order}/track', [OrderController::class, 'track'])->name('track');
            Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::post('/check-promo', [OrderController::class, 'checkPromo'])->name('check-promo');
            Route::post('/check-availability', [OrderController::class, 'checkAvailability'])->name('check-availability');
        });
        
        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::get('/security', [ProfileController::class, 'security'])->name('security');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
        });
        
        // Promo
        Route::prefix('promo')->name('promo.')->group(function () {
            Route::get('/', [PromoController::class, 'index'])->name('index');
            Route::post('/check', [PromoController::class, 'check'])->name('check');
            Route::get('/{code}', [PromoController::class, 'show'])->name('show');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/create/{order}', [PaymentController::class, 'create'])->name('create');
            Route::post('/store/{order}', [PaymentController::class, 'store'])->name('store');
            Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
            Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
            Route::post('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
            Route::get('/processing/{order}', [PaymentController::class, 'processing'])->name('processing');
        });
    });
});

/*
|--------------------------------------------------------------------------
| CLEANER ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('cleaner')->name('cleaner.')->group(function () {
    
    // ===== GUEST ROUTES (CLEANER) =====
    Route::middleware('guest:cleaner')->group(function () {
        Route::get('/login', [CleanerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CleanerAuthController::class, 'login'])->name('login.submit');
        Route::get('/register', [CleanerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [CleanerAuthController::class, 'register'])->name('register.submit');
    });

    // ===== PROTECTED ROUTES (CLEANER) =====
    Route::middleware('cleaner')->group(function () {
        // Logout
        Route::post('/logout', [CleanerAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [CleanerDashboardController::class, 'index'])->name('dashboard');
        Route::post('/location', [CleanerDashboardController::class, 'updateLocation'])->name('location');
        Route::post('/status', [CleanerDashboardController::class, 'updateStatus'])->name('status');

        // Tasks
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [CleanerTaskController::class, 'index'])->name('index');
            Route::get('/current', [CleanerTaskController::class, 'current'])->name('current');
            Route::post('/{task}/accept', [CleanerTaskController::class, 'accept'])->name('accept');
            Route::post('/{task}/status', [CleanerTaskController::class, 'updateStatus'])->name('update-status');
            Route::get('/history', [CleanerTaskController::class, 'history'])->name('history');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [CleanerProfileController::class, 'index'])->name('index');
            Route::get('/edit', [CleanerProfileController::class, 'edit'])->name('edit');
            Route::put('/', [CleanerProfileController::class, 'update'])->name('update');
            Route::put('/password', [CleanerProfileController::class, 'updatePassword'])->name('password');
            Route::get('/statistics', [CleanerProfileController::class, 'statistics'])->name('statistics');
        });
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    // ===== GUEST ROUTES (ADMIN) =====
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // ===== PROTECTED ROUTES (ADMIN) =====
    Route::middleware('admin')->group(function () {
        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'users'])->name('index');
        });
        
        // Cleaner Management
        Route::prefix('cleaners')->name('cleaners.')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'cleaners'])->name('index');
        });
        
        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'orders'])->name('index');
        });
    });
});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});