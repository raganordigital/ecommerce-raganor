<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

// All admin routes are protected by auth and admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard - make sure this is the root admin route
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Alias for convenience

    // Products - resource routes
    Route::resource('products', ProductController::class);

    // Additional product routes (these must come AFTER resource to avoid conflicts)
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])
        ->name('products.toggle-featured');
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])
        ->name('products.images.delete');
    Route::post('products/{product}/images/reorder', [ProductController::class, 'reorderImages'])
        ->name('products.images.reorder');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Orders
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status');

        // Admin review routes
Route::resource('reviews', App\Http\Controllers\Admin\ReviewController::class)->except(['create', 'store', 'edit']);
Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
Route::post('reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
Route::post('reviews/bulk/approve', [App\Http\Controllers\Admin\ReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
});
