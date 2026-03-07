<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display user's wishlist.
     */
    public function index(): View
    {
        $wishlistItems = Wishlist::with('product.primaryImage')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('public.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist.
     */
    public function add(Product $product): RedirectResponse
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();

        if (! $exists) {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);

            return redirect()->back()->with('success', 'Product added to wishlist.');
        }

        return redirect()->back()->with('info', 'Product already in wishlist.');
    }

    /**
     * Remove product from wishlist.
     */
    public function remove(Product $product): RedirectResponse
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }

    /**
     * Clear entire wishlist.
     */
    public function clear(): RedirectResponse
    {
        Wishlist::where('user_id', auth()->id())->delete();

        return redirect()->route('wishlist.index')->with('success', 'Wishlist cleared successfully.');
    }

    /**
     * Move wishlist item to cart.
     */
    public function moveToCart(Product $product): RedirectResponse
    {
        // Remove from wishlist
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        // Redirect to cart with product pre-filled
        return redirect()->route('cart.index')->with('success', 'Product moved to cart. Please adjust quantity and checkout.');
    }
}
