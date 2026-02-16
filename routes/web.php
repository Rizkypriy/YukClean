<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\BundleController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;



// ============ CLEANER ROUTES (DILETAKKAN PALING ATAS) ============
require __DIR__.'/cleaner.php';
Route::get('/cleaner-test', function() {
    return 'CLEANER ROUTE BEKERJA!';});
// ============ PUBLIC ROUTES ============
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// ============ GUEST ROUTES (USER) ============
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ============ PROTECTED ROUTES (USER) ============
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
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
    
    // Promo
    Route::prefix('promo')->name('promo.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::post('/check', [PromoController::class, 'check'])->name('check');
        Route::get('/{code}', [PromoController::class, 'show'])->name('show');
    });
    
    // Bundles
    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', [BundleController::class, 'index'])->name('index');
        Route::get('/{bundle}', [BundleController::class, 'show'])->name('show');
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
    
    // Payments
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/create/{order}', [PaymentController::class, 'create'])->name('create');
        Route::post('/store/{order}', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
        Route::post('/{payment}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    });
});

// ============ FALLBACK ROUTE ============
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/debug-routes', function() {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        if (str_contains($route->uri(), 'cleaner')) {
            $routes[] = [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        }
    }
    return response()->json($routes);
});