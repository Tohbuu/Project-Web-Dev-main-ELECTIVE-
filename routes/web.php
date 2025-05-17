<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\ProfileDashboardController;

// Redirect root path based on auth status
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('frontpage');
    }
    return redirect()->route('login');
});

// Authentication Routes (accessible without auth)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
});

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Frontpage (replaces original /)
    Route::get('/frontpage', [FrontPageController::class, 'index'])->name('frontpage');

    // Cart Routes
    Route::middleware(['auth'])->group(function () {
    
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    // Keep checkout as POST for form submissions
    Route::post('/checkout', [CartController::class, 'store'])->name('cart.store');
});

    // Profile Dashboard Routes
    Route::get('/profile/dashboard', [ProfileDashboardController::class, 'index'])->name('profile.dashboard');
    Route::post('/profile/update', [ProfileDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/order/update/{id}', [ProfileDashboardController::class, 'updateOrder'])->name('order.update');
});
