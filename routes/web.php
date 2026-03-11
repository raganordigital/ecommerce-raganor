<?php

declare(strict_types=1);

use App\Http\Controllers\Public\ProductController as PublicProductController;
use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', [App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');

// Static pages
Route::view('/about', 'public.pages.about')->name('about');
Route::view('/contact', 'public.pages.contact')->name('contact');

// Add a dashboard redirect for authenticated users
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    if ($user && $user->hasRole('admin')) {
        return redirect('/admin');
    }

    // For regular users, show the default dashboard
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public product routes
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [PublicProductController::class, 'show'])->name('products.show');

// ==================== CART ROUTES ====================
Route::prefix('cart')->name('cart.')->group(function () {
    // View cart
    Route::get('/', [App\Http\Controllers\Public\CartController::class, 'index'])->name('index');

    // Add to cart
    Route::post('/add', [App\Http\Controllers\Public\CartController::class, 'add'])->name('add');

    // Buy now - clears cart and adds single item
    Route::post('/buy-now', [App\Http\Controllers\Public\CartController::class, 'buyNow'])->name('buy-now');

    // Update quantity
    Route::post('/update/{id}', [App\Http\Controllers\Public\CartController::class, 'updateQuantity'])->name('update');

    // Remove item
    Route::delete('/remove/{id}', [App\Http\Controllers\Public\CartController::class, 'removeItem'])->name('remove');

    // Clear cart
    Route::post('/clear', [App\Http\Controllers\Public\CartController::class, 'clear'])->name('clear');
});

// ==================== CHECKOUT ROUTES ====================
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    // Original checkout routes (keep for backward compatibility)
    Route::get('/', [App\Http\Controllers\Public\CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [App\Http\Controllers\Public\CheckoutController::class, 'process'])->name('process');

    // Livewire checkout page (new)
    Route::get('/livewire', function () {
        return view('public.checkout.livewire-index');
    })->name('livewire');

    Route::get('/cart-checkout', function () {
    session()->forget('buy_now_item');
    return redirect()->route('checkout.livewire');
})->name('checkout.cart');

    // Buy now endpoint (kept for API compatibility)
    Route::post('/buy-now', [App\Http\Controllers\Public\CheckoutController::class, 'buyNow'])->name('buy-now');
});

// ==================== PAYMENT SUCCESS/CANCEL ROUTES ====================
// These don't need auth middleware as they're redirects from Stripe
Route::get('/checkout/success', [App\Http\Controllers\Public\CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::get('/checkout/cancel', [App\Http\Controllers\Public\CheckoutController::class, 'cancel'])
    ->name('checkout.cancel');

// Stripe webhook (no auth)
Route::post('/stripe/webhook', [App\Http\Controllers\Public\CheckoutController::class, 'webhook'])
    ->name('cashier.webhook');

Route::get('/checkout/success/{orderNumber}', [App\Http\Controllers\Public\CheckoutController::class, 'successWithOrder'])
    ->name('checkout.success.order');

Route::get('/checkout/cod-success/{orderNumber}', [App\Http\Controllers\Public\CheckoutController::class, 'codSuccess'])
    ->name('checkout.cod-success');

    

// ==================== ORDER ROUTES ====================
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [App\Http\Controllers\Public\OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [App\Http\Controllers\Public\OrderController::class, 'show'])->name('show');
});

// ==================== WISHLIST ROUTES ====================
Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [App\Http\Controllers\Public\WishlistController::class, 'index'])->name('index');
    Route::post('/add/{product}', [App\Http\Controllers\Public\WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [App\Http\Controllers\Public\WishlistController::class, 'remove'])->name('remove');
    Route::post('/clear', [App\Http\Controllers\Public\WishlistController::class, 'clear'])->name('clear');
    Route::post('/move-to-cart/{product}', [App\Http\Controllers\Public\WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// ==================== REVIEW ROUTES ====================
Route::middleware('auth')->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/create/{product}', [App\Http\Controllers\Public\ReviewController::class, 'create'])->name('create');
    Route::post('/{product}', [App\Http\Controllers\Public\ReviewController::class, 'store'])->name('store');
    Route::post('/{review}/helpful', [App\Http\Controllers\Public\ReviewController::class, 'helpful'])->name('helpful');
    Route::post('/{review}/unhelpful', [App\Http\Controllers\Public\ReviewController::class, 'unhelpful'])->name('unhelpful');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
