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
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController; 
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CleanerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/
Route::get('/', function() {
    return view('auth.landing');
})->name('login.landing');

Route::get('/login', function() {
    return redirect()->route('user.login');
})->name('login');

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
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
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
            Route::get('/completed/{order}', [OrderController::class, 'completed'])->name('completed');
            Route::post('/{order}/rate', [OrderController::class, 'rate'])->name('rate');
            Route::put('/{order}/update-notes', [OrderController::class, 'updateNotes'])->name('update-notes');
            
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
        Route::post('/logout', [CleanerAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [CleanerDashboardController::class, 'index'])->name('dashboard');
        Route::post('/location', [CleanerDashboardController::class, 'updateLocation'])->name('location');
        Route::post('/status', [CleanerDashboardController::class, 'updateStatus'])->name('status');

        // Tasks
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [CleanerTaskController::class, 'index'])->name('index');
            Route::get('/current', [CleanerTaskController::class, 'current'])->name('current');
            Route::post('/{task}/accept', [CleanerTaskController::class, 'accept'])->name('accept');
            Route::post('/{task}/status', [CleanerTaskController::class, 'updateStatus'])->name('update-status');
            Route::post('/{task}/progress', [CleanerTaskController::class, 'updateProgress'])->name('update-progress');
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
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Cleaner Management
        Route::prefix('cleaners')->name('cleaners.')->group(function () {
            Route::get('/', [CleanerController::class, 'index'])->name('index');
            Route::get('/{cleaner}', [CleanerController::class, 'show'])->name('show');
            Route::put('/{cleaner}', [CleanerController::class, 'update'])->name('update');
            Route::delete('/{cleaner}', [CleanerController::class, 'destroy'])->name('destroy');
            Route::post('/{cleaner}/toggle-status', [CleanerController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::put('/{order}', [OrderController::class, 'update'])->name('update');
            Route::post('/{order}/assign-cleaner', [OrderController::class, 'assignCleaner'])->name('assign-cleaner');
            Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::get('/', [AdminOrderController::class, 'monitoring'])->name('monitoring');
            });

        // Service Management
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
            Route::post('/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/weekly', [App\Http\Controllers\Admin\ReportController::class, 'weekly'])->name('weekly');
    Route::get('/export/pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('export.excel');
});
    });
});


// Admin routes - Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/weekly', [App\Http\Controllers\Admin\ReportController::class, 'weekly'])->name('weekly');
    Route::get('/export/pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('export.excel');
});
/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE (404)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});