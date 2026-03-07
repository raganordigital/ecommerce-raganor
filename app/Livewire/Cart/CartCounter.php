<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Component;

class CartCounter extends Component
{
    public int $count = 0;

    protected CartService $cartService;

    protected $listeners = [
        'cart-updated' => 'updateCount',
    ];

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->count = $this->cartService->getTotalQuantity();
    }

    public function updateCount($cartCount)
    {
        $this->count = $cartCount;
    }

    public function render()
    {
        return view('livewire.cart.cart-counter');
    }
}
