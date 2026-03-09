<?php

declare(strict_types=1);

namespace App\Livewire\Wishlist;

use App\Models\Product;
use App\Models\Wishlist;
use Livewire\Component;

class WishlistButton extends Component
{
    public Product $product;

    public bool $inWishlist = false;

    public function mount(): void
    {
        $this->inWishlist = $this->isInWishlist();
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        if ($this->inWishlist) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->delete();
            $this->inWishlist = false;
        } else {
            Wishlist::firstOrCreate([
                'user_id'    => auth()->id(),
                'product_id' => $this->product->id,
            ]);
            $this->inWishlist = true;
        }

        $newCount = Wishlist::where('user_id', auth()->id())->count();

        // Dispatch browser event — Alpine on wishlist-counter.blade.php catches this instantly
        $this->dispatch('wishlist-count-updated', count: $newCount);
    }

    private function isInWishlist(): bool
    {
        return auth()->check()
            && Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->exists();
    }

    public function render()
    {
        return view('livewire.wishlist.wishlist-button');
    }
}