<?php

declare(strict_types=1);

namespace App\Livewire\Wishlist;

use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use Livewire\Component;

class WishlistPage extends Component
{
    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function remove(int $productId): void
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        $this->dispatch('wishlist-count-updated', count: $this->wishlistCount());
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $result = $this->cartService->add($product, 1);

        if ($result['success']) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->delete();

            $this->dispatch('wishlist-count-updated', count: $this->wishlistCount());
            $this->dispatch('cart-count-updated', count: $result['cartCount']);
        }
    }

    public function clearWishlist(): void
    {
        Wishlist::where('user_id', auth()->id())->delete();
        $this->dispatch('wishlist-count-updated', count: 0);
    }

    private function wishlistCount(): int
    {
        return Wishlist::where('user_id', auth()->id())->count();
    }

    public function render()
    {
        $wishlistItems = Wishlist::with('product.primaryImage')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('livewire.wishlist.wishlist-page', compact('wishlistItems'));
    }
}