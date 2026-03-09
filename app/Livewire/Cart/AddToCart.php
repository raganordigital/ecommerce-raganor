<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;

    public int $quantity = 1;

    public bool $showQuantityInput = false;

    public string $buttonClass = 'btn-primary';

    protected CartService $cartService;

    protected $rules = [
        'quantity' => 'required|integer|min:1|max:999',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function addToCart(): void
    {
        $this->validate();

        $result = $this->cartService->add($this->product, $this->quantity);

        if ($result['success']) {
            // Dispatch browser event with the new count in payload
            // Alpine.js on cart-counter catches this instantly — no Livewire round-trip
            $this->dispatch('cart-count-updated', count: $result['cartCount']);
        }
    }

    public function quickAdd(): void
    {
        $this->quantity = 1;
        $this->addToCart();
    }

    public function buyNow(): void
    {
        $this->quantity = 1;
        $this->validate();

        $result = $this->cartService->add($this->product, $this->quantity);

        if ($result['success']) {
            $this->dispatch('cart-count-updated', count: $result['cartCount']);
            $this->redirect(route('checkout.index'));
        }
    }

    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}