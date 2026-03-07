<?php

declare(strict_types=1);

namespace App\Livewire\Wishlist;

use App\Models\Wishlist;
use App\Services\CartService;
use Livewire\Component;

class WishlistPage extends Component
{
    protected CartService $cartService;

    protected $listeners = ['wishlist-updated' => '$refresh'];

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Remove item from wishlist.
     */
    public function remove(int $productId): void
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        $this->dispatch('wishlist-updated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Product removed from wishlist.',
        ]);
    }

    /**
     * Add item to cart and remove from wishlist.
     */
    public function addToCart(int $productId): void
    {
        $product = \App\Models\Product::find($productId);

        if ($product) {
            $result = $this->cartService->add($product, 1);

            if ($result['success']) {
                // Remove from wishlist
                Wishlist::where('user_id', auth()->id())
                    ->where('product_id', $productId)
                    ->delete();

                $this->dispatch('wishlist-updated');
                $this->dispatch('cart-updated', cartCount: $result['cartCount']);
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Product moved to cart successfully.',
                ]);
            }
        }
    }

    /**
     * Clear entire wishlist.
     */
    public function clearWishlist(): void
    {
        Wishlist::where('user_id', auth()->id())->delete();

        $this->dispatch('wishlist-updated');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Wishlist cleared successfully.',
        ]);
    }

    public function render()
    {
        $wishlistItems = Wishlist::with('product.primaryImage')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.wishlist.wishlist-page', [
            'wishlistItems' => $wishlistItems,
        ]);
    }
}
