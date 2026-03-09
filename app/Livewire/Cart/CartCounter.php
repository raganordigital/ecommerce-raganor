<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    public int $count = 0;

    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->count = $this->cartService->getTotalQuantity();
    }

    #[On('cart-updated')]
    public function refresh(): void
    {
        $this->count = $this->cartService->getTotalQuantity();
    }

    public function render()
    {
        return view('livewire.cart.cart-counter');
    }
}