<?php

declare(strict_types=1);

namespace App\Livewire\Checkout;

use App\Services\CartService;
use Livewire\Component;

class CheckoutPage extends Component
{
    protected CartService $cartService;
    
    public string $checkoutType = 'cart';
    public $cartItems;
    public float $subtotal = 0;
    
    // Form properties
    public string $shipping_name = '';
    public string $shipping_email = '';
    public string $shipping_phone = '';
    public string $shipping_address = '';
    public string $shipping_city = '';
    public ?string $shipping_state = '';
    public string $shipping_zipcode = '';
    public string $shipping_country = '';
    public bool $billing_same = true;
    
    protected $listeners = [
        'cart-updated' => 'refreshCart',
        'cart-count-updated' => '$refresh',
        'checkout-error' => 'handleCheckoutError',
    ];

    protected $rules = [
        'shipping_name' => 'required|string|max:255',
        'shipping_email' => 'required|email|max:255',
        'shipping_phone' => 'required|string|max:20',
        'shipping_address' => 'required|string|max:500',
        'shipping_city' => 'required|string|max:100',
        'shipping_state' => 'nullable|string|max:100',
        'shipping_zipcode' => 'required|string|max:20',
        'shipping_country' => 'required|string|size:2',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->loadCartData();
        $this->loadUserData();
    }

    protected function loadCartData(): void
    {
        // Check if this is a buy now checkout (has session data)
        $buyNowItem = session('buy_now_item');
        
        if ($buyNowItem) {
            $this->checkoutType = 'buy_now';
            $product = \App\Models\Product::with('primaryImage')->find($buyNowItem['product_id']);
            
            if ($product) {
                $this->cartItems = collect([
                    (object) [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->current_price,
                        'quantity' => $buyNowItem['quantity'],
                        'attributes' => (object) [
                            'sku' => $product->sku,
                            'image' => $product->primaryImage?->thumbnail_path,
                            'slug' => $product->slug,
                        ],
                    ]
                ]);
                $this->subtotal = $product->current_price * $buyNowItem['quantity'];
            }
        } else {
            $this->checkoutType = 'cart';
            $this->cartItems = $this->cartService->getContent();
            $this->subtotal = $this->cartService->getSubtotal();
        }
    }

    protected function loadUserData(): void
    {
        $user = auth()->user();
        
        if ($user) {
            $this->shipping_name = $user->name ?? '';
            $this->shipping_email = $user->email ?? '';
            $this->shipping_phone = $user->phone ?? '';
            $this->shipping_address = $user->address ?? '';
            $this->shipping_city = $user->city ?? '';
            $this->shipping_state = $user->state ?? '';
            $this->shipping_zipcode = $user->zipcode ?? '';
            $this->shipping_country = $user->country ?? '';
        }
    }

    public function refreshCart(): void
    {
        $this->loadCartData();
    }

    public function incrementQuantity(int $productId): void
    {
        if ($this->checkoutType === 'buy_now') {
            // Update session for buy now
            $buyNowItem = session('buy_now_item');
            if ($buyNowItem && $buyNowItem['product_id'] == $productId) {
                $buyNowItem['quantity']++;
                session(['buy_now_item' => $buyNowItem]);
                $this->loadCartData();
                $this->dispatch('cart-updated');
            }
        } else {
            // Update cart service for regular cart
            $currentQty = $this->getItemQuantity($productId);
            $this->cartService->update($productId, $currentQty + 1);
            $this->loadCartData();
            $this->dispatch('cart-count-updated', count: $this->cartService->getTotalQuantity());
        }
    }

    public function decrementQuantity(int $productId): void
    {
        if ($this->checkoutType === 'buy_now') {
            // Update session for buy now
            $buyNowItem = session('buy_now_item');
            if ($buyNowItem && $buyNowItem['product_id'] == $productId && $buyNowItem['quantity'] > 1) {
                $buyNowItem['quantity']--;
                session(['buy_now_item' => $buyNowItem]);
                $this->loadCartData();
                $this->dispatch('cart-updated');
            }
        } else {
            // Update cart service for regular cart
            $currentQty = $this->getItemQuantity($productId);
            if ($currentQty > 1) {
                $this->cartService->update($productId, $currentQty - 1);
                $this->loadCartData();
                $this->dispatch('cart-count-updated', count: $this->cartService->getTotalQuantity());
            }
        }
    }

    protected function getItemQuantity(int $productId): int
    {
        $item = $this->cartItems->firstWhere('id', $productId);
        return $item ? (int) $item->quantity : 0;
    }

    public function removeItem(int $productId): void
    {
        if ($this->checkoutType === 'buy_now') {
            // For buy now, clear session and redirect
            session()->forget('buy_now_item');
            $this->redirectRoute('products.index', navigate: true);
        } else {
            // Remove from cart service
            $this->cartService->remove($productId);
            $this->loadCartData();
            $this->dispatch('cart-count-updated', count: $this->cartService->getTotalQuantity());
            
            if ($this->cartItems->isEmpty()) {
                $this->redirectRoute('cart.index', navigate: true);
            }
        }
    }

public function processCheckout()
{
    // Validate the form data
    $this->validate();
    
    // Dispatch event with form data
    $this->dispatch('process-checkout', data: [
        'shipping_name' => $this->shipping_name,
        'shipping_email' => $this->shipping_email,
        'shipping_phone' => $this->shipping_phone,
        'shipping_address' => $this->shipping_address,
        'shipping_city' => $this->shipping_city,
        'shipping_state' => $this->shipping_state,
        'shipping_zipcode' => $this->shipping_zipcode,
        'shipping_country' => $this->shipping_country,
        'billing_same' => $this->billing_same ? '1' : '0',
    ]);
}

    public function handleCheckoutError($message)
    {
        $this->addError('checkout', $message);
    }

    public function render()
    {
        return view('livewire.checkout.checkout-page', [
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'checkoutType' => $this->checkoutType,
        ]);
    }
}