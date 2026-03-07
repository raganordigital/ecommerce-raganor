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

    protected $listeners = ['wishlist-updated' => 'checkWishlistStatus'];

    public function mount()
    {
        $this->checkWishlistStatus();
    }

    /**
     * Check if product is in user's wishlist.
     */
    public function checkWishlistStatus(): void
    {
        if (auth()->check()) {
            $this->inWishlist = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->exists();
        }
    }

    /**
     * Toggle wishlist status.
     */
    public function toggle()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->inWishlist) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $this->product->id)
                ->delete();

            $this->inWishlist = false;
            $this->dispatch('wishlist-updated');
            // Notification removed - implement toast system if needed
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
            ]);

            $this->inWishlist = true;
            $this->dispatch('wishlist-updated');
            // Notification removed - implement toast system if needed
        }
    }

    public function render()
    {
        return view('livewire.wishlist.wishlist-button');
    }
}
