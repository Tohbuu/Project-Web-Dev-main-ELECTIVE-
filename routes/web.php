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
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
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
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/complete-checkout', [CartController::class, 'completeCheckout'])->name('cart.complete');
Route::post('/cart/complete', [CartController::class, 'complete'])->name('cart.complete');
});

    // Profile Dashboard Routes
    Route::get('/profile/dashboard', [ProfileDashboardController::class, 'index'])->name('profile.dashboard');
    Route::post('/profile/update', [ProfileDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/order/update/{id}', [ProfileDashboardController::class, 'updateOrder'])->name('order.update');
    Route::get('/order/receipt/{id}', [App\Http\Controllers\ProfileDashboardController::class, 'showReceipt'])->name('order.receipt')->middleware('auth');
    Route::delete('/order/delete/{id}', [ProfileDashboardController::class, 'deleteOrder'])->name('order.delete')->middleware('auth');
    Route::post('/order/{id}/update', [App\Http\Controllers\OrderController::class, 'update'])->name('order.update');
});

//test receipt route
Route::get('/test-receipt/{id}', function($id) {
    $order = App\Models\Cart::find($id);
    $user = Auth::user();
    
    if (!$order) {
        return "Order not found for ID: " . $id;
    }
    
    return "Order found: " . $order->item . " for user: " . $user->name;
})->middleware('auth');