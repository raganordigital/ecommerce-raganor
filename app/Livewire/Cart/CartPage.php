<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Component;

class CartPage extends Component
{
    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $result = $this->cartService->update($productId, $quantity);
        $this->dispatch('cart-count-updated', count: $result['cartCount']);
    }

    public function removeItem(int $productId): void
    {
        $result = $this->cartService->remove($productId);
        $this->dispatch('cart-count-updated', count: $result['cartCount']);
    }

    public function clearCart(): void
    {
        $this->cartService->clear();
        $this->dispatch('cart-count-updated', count: 0);
    }

    public function render()
    {
        return view('livewire.cart.cart-page', [
            'cartItems' => $this->cartService->getContent(),
            'subtotal'  => $this->cartService->getSubtotal(),
        ]);
    }
}