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

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add product to cart
     */
    public function addToCart()
    {
        $this->validate();

        $result = $this->cartService->add($this->product, $this->quantity);

        if ($result['success']) {
            $this->dispatch('cart-updated', cartCount: $result['cartCount']);
            // Notification removed - implement toast system if needed
        } else {
            // Notification removed - implement toast system if needed
        }
    }

    /**
     * Quick add single item
     */
    public function quickAdd()
    {
        $this->quantity = 1;
        $this->addToCart();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}
