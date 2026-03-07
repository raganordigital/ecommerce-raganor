<?php

declare(strict_types=1);

namespace App\Livewire\Wishlist;

use App\Models\Wishlist;
use Livewire\Component;

class WishlistCounter extends Component
{
    public int $count = 0;

    protected $listeners = ['wishlist-updated' => 'updateCount'];

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount(): void
    {
        if (auth()->check()) {
            $this->count = Wishlist::where('user_id', auth()->id())->count();
        } else {
            $this->count = 0;
        }
    }

    public function render()
    {
        return view('livewire.wishlist.wishlist-counter');
    }
}
