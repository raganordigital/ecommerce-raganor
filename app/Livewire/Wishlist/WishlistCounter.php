<?php

declare(strict_types=1);

namespace App\Livewire\Wishlist;

use App\Models\Wishlist;
use Livewire\Attributes\On;
use Livewire\Component;

class WishlistCounter extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refresh();
    }

    #[On('wishlist-updated')]
    public function refresh(): void
    {
        $this->count = auth()->check()
            ? Wishlist::where('user_id', auth()->id())->count()
            : 0;
    }

    public function render()
    {
        return view('livewire.wishlist.wishlist-counter');
    }
}
