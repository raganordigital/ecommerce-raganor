<?php

declare(strict_types=1);

use App\Http\Controllers\Public\ProductController as PublicProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Include auth routes (already in Breeze)
require __DIR__.'/auth.php';

// Admin routes - define them directly here first to test
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');
});


// Add this temporary test route at the bottom of routes/web.php
Route::get('/test-admin', function () {
    return 'Admin route works!';
})->middleware(['auth', 'admin']);