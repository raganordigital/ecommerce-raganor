<?php

declare(strict_types=1);

namespace App\Livewire\Checkout;

use App\Services\CartService;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderEmailVerification;

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
    public string $paymentMethod = 'stripe';
    public float $totalShipping = 0;
    public float $totalTax = 0;
    public bool $accountVerified = false; // will be loaded from session
    public string $accountVerificationCode = '';
    public bool $accountVerificationSent = false;

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
        'paymentMethod' => 'required|in:stripe,cod',
    ];

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        $this->accountVerified = session('account_email_verified', false);
        $this->loadCartData();
        $this->loadUserData();
    }

    protected function loadCartData(): void
    {
        // Reset totals
        $this->totalShipping = 0;
        $this->totalTax = 0;

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

                // Calculate shipping and tax for buy now
                $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
                $this->totalShipping = $shippingPerItem * $buyNowItem['quantity'];

                $taxRate = $product->tax_rate ?? 0;
                $this->totalTax = $this->subtotal * ($taxRate / 100);
            }
        } else {
            $this->checkoutType = 'cart';
            $this->cartItems = $this->cartService->getContent();
            $this->subtotal = $this->cartService->getSubtotal();

            // Calculate shipping and tax for cart
            foreach ($this->cartItems as $item) {
                $product = \App\Models\Product::find($item->id);
                if ($product) {
                    $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
                    $this->totalShipping += $shippingPerItem * $item->quantity;

                    $taxRate = $product->tax_rate ?? 0;
                    $this->totalTax += ($item->price * $item->quantity) * ($taxRate / 100);
                }
            }
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
        // After loading cart data, call a method to check cod availability
        $this->loadCartData();
        if ($this->paymentMethod === 'cod' && !$this->codAvailable) {
            $this->paymentMethod = 'stripe';
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
        // After loading cart data, call a method to check cod availability
        $this->loadCartData();
        if ($this->paymentMethod === 'cod' && !$this->codAvailable) {
            $this->paymentMethod = 'stripe';
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
        // If COD selected but not available, show error
        if ($this->paymentMethod === 'cod' && !$this->codAvailable) {
            $this->addError('paymentMethod', 'Cash on Delivery is not available for some items.');
            return;
        }

        if (!session('account_email_verified', false)) {
            $this->addError('account', 'Please verify your account email before proceeding.');
            return;
        }

        // Validate all fields using the $rules property
        $this->validate();

        if ($this->paymentMethod === 'cod') {
            // Create COD order
            try {
                $formData = [
                    'shipping_name' => $this->shipping_name,
                    'shipping_email' => $this->shipping_email,
                    'shipping_phone' => $this->shipping_phone,
                    'shipping_address' => $this->shipping_address,
                    'shipping_city' => $this->shipping_city,
                    'shipping_state' => $this->shipping_state,
                    'shipping_zipcode' => $this->shipping_zipcode,
                    'shipping_country' => $this->shipping_country,
                    'billing_same' => $this->billing_same,
                ];

                if ($this->checkoutType === 'buy_now') {
                    $buyNowItem = session('buy_now_item');
                    $order = app(\App\Services\OrderService::class)->createBuyNowCodOrder($formData, $buyNowItem);
                    session()->forget('buy_now_item');
                } else {
                    $order = app(\App\Services\OrderService::class)->createCodOrder($formData);
                    $this->cartService->clear();
                }

                return redirect()->route('checkout.cod-success', ['orderNumber' => $order->order_number]);
            } catch (\Exception $e) {
                Log::error('COD order creation failed', ['error' => $e->getMessage()]);
                $this->addError('checkout', 'Failed to place order. Please try again.');
                return;
            }
        } else {
            // Stripe: dispatch event for JavaScript
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
    }

    public function handleCheckoutError($message)
    {
        $this->addError('checkout', $message);
    }

    public function render()
    {
        // Recalculate shipping and tax based on current cart items
        $this->totalShipping = 0;
        $this->totalTax = 0;

        foreach ($this->cartItems as $item) {
            $product = \App\Models\Product::find($item->id);
            if ($product) {
                $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
                $this->totalShipping += $shippingPerItem * $item->quantity;

                $taxRate = $product->tax_rate ?? 0;
                $this->totalTax += ($item->price * $item->quantity) * ($taxRate / 100);
            }
        }

        return view('livewire.checkout.checkout-page', [
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'checkoutType' => $this->checkoutType,
            'totalShipping' => $this->totalShipping,
            'totalTax' => $this->totalTax,
        ]);
    }

    public function getCodAvailableProperty(): bool
    {
        if ($this->checkoutType === 'buy_now') {
            $productId = session('buy_now_item')['product_id'] ?? null;
            if ($productId) {
                $product = \App\Models\Product::find($productId);
                return $product && $product->allow_cod;
            }
            return false;
        }

        $cartItems = $this->cartItems;
        if (!$cartItems || $cartItems->isEmpty()) {
            return false;
        }

        foreach ($cartItems as $item) {
            $product = \App\Models\Product::find($item->id);
            if (!$product || !$product->allow_cod) {
                return false;
            }
        }
        return true;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['cartItems', 'checkoutType']) || str_starts_with($propertyName, 'cartItems.')) {
            if ($this->paymentMethod === 'cod' && !$this->codAvailable) {
                $this->paymentMethod = 'stripe';
            }
        }
    }

    public function sendAccountVerification()
    {
        $user = auth()->user();
        if (!$user) {
            $this->addError('account', 'You must be logged in.');
            return;
        }

        $code = random_int(100000, 999999);

        session([
            'account_verification_code' => (string) $code,
            'account_verification_expires' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OrderEmailVerification($user->email, (string) $code));

        $this->accountVerificationSent = true;
        $this->dispatch('verification-sent');
    }

    public function verifyAccountCode()
    {
        $this->validate([
            'accountVerificationCode' => 'required|string|size:6',
        ]);

        $storedCode = session('account_verification_code');
        $expires = session('account_verification_expires');

        if (!$storedCode || !$expires || now()->gt($expires)) {
            $this->addError('accountVerificationCode', 'Verification code has expired. Please request a new one.');
            return;
        }

        if ($this->accountVerificationCode !== $storedCode) {
            $this->addError('accountVerificationCode', 'Invalid verification code.');
            return;
        }

        session(['account_email_verified' => true]);
        $this->accountVerified = true;
        $this->accountVerificationCode = '';
        $this->accountVerificationSent = false;

        session()->forget(['account_verification_code', 'account_verification_expires']);

        $this->dispatch('account-verified');
    }
}
