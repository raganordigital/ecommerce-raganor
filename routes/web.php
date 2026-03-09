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

// Public product routes (we'll create these controllers later)
Route::get('/products', [PublicProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [PublicProductController::class, 'show'])->name('products.show');
// Cart routes
Route::get('/cart', function () {
    return view('public.cart.index');
})->name('cart.index');

// Add this line - POST route for adding items to cart
Route::post('/cart/add', [App\Http\Controllers\Public\CartController::class, 'add'])->name('cart.add');
// Include authentication routes (Breeze)

// Add this with your other cart routes
Route::post('/cart/buy-now', [App\Http\Controllers\Public\CartController::class, 'buyNow'])
    ->name('cart.buy-now');

// Checkout routes
Route::get('/checkout', [App\Http\Controllers\Public\CheckoutController::class, 'index'])
    ->middleware('auth')
    ->name('checkout.index');

Route::post('/checkout', [App\Http\Controllers\Public\CheckoutController::class, 'process'])
    ->middleware('auth')
    ->name('checkout.process');

Route::get('/checkout/success/{order}', [App\Http\Controllers\Public\CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::get('/checkout/cancel/{order}', [App\Http\Controllers\Public\CheckoutController::class, 'cancel'])
    ->name('checkout.cancel');

// Stripe webhook (no auth)
Route::post('/stripe/webhook', [App\Http\Controllers\Public\CheckoutController::class, 'webhook'])
    ->name('cashier.webhook');

// Customer order routes (protected)
Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [App\Http\Controllers\Public\OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [App\Http\Controllers\Public\OrderController::class, 'show'])->name('show');
});

// Wishlist routes (protected)
Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [App\Http\Controllers\Public\WishlistController::class, 'index'])->name('index');
    Route::post('/add/{product}', [App\Http\Controllers\Public\WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{product}', [App\Http\Controllers\Public\WishlistController::class, 'remove'])->name('remove');
    Route::post('/clear', [App\Http\Controllers\Public\WishlistController::class, 'clear'])->name('clear');
    Route::post('/move-to-cart/{product}', [App\Http\Controllers\Public\WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// Public review routes
Route::middleware('auth')->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/create/{product}', [App\Http\Controllers\Public\ReviewController::class, 'create'])->name('create');
    Route::post('/{product}', [App\Http\Controllers\Public\ReviewController::class, 'store'])->name('store');
    Route::post('/{review}/helpful', [App\Http\Controllers\Public\ReviewController::class, 'helpful'])->name('helpful');
    Route::post('/{review}/unhelpful', [App\Http\Controllers\Public\ReviewController::class, 'unhelpful'])->name('unhelpful');
});

require __DIR__ . '/auth.php';

// Include admin routes
require __DIR__ . '/admin.php';
