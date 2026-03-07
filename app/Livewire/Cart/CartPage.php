<?php

declare(strict_types=1);

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Component;

class CartPage extends Component
{
    protected CartService $cartService;

    protected $listeners = [
        'cart-updated' => '$refresh',
    ];

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function updateQuantity($productId, $quantity)
    {
        $this->cartService->update($productId, $quantity);
        $this->dispatch('cart-updated', cartCount: $this->cartService->getTotalQuantity());
    }

    public function removeItem($productId)
    {
        $this->cartService->remove($productId);
        $this->dispatch('cart-updated', cartCount: $this->cartService->getTotalQuantity());
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Item removed from cart.',
        ]);
    }

    public function clearCart()
    {
        $this->cartService->clear();
        $this->dispatch('cart-updated', cartCount: 0);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Cart cleared successfully.',
        ]);
    }

    public function render()
    {
        $cartItems = $this->cartService->getContent();
        $subtotal = $this->cartService->getSubtotal();

        return view('livewire.cart.cart-page', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
        ]);
    }
}
