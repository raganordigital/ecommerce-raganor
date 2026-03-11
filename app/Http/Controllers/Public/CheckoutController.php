<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    /**
     * Handle Buy Now request - stores product in session and redirects to checkout
     */
    public function buyNow(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('primaryImage')->findOrFail($request->product_id);

        // Store in session - this bypasses the cart completely
        session(['buy_now_item' => [
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->current_price,
            'name' => $product->name,
            'sku' => $product->sku,
            'image' => $product->primaryImage?->thumbnail_path,
        ]]);

        // Redirect to the Livewire checkout page
        return redirect()->route('checkout.livewire')
            ->with('success', 'Proceeding to checkout with your selected item.');
    }

    /**
     * Display checkout page - works for both cart and buy now
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Check if this is a buy now checkout (has session data)
        $buyNowItem = session('buy_now_item');

        if ($buyNowItem) {
            // Buy now checkout - use session data
            $product = Product::with('primaryImage')->find($buyNowItem['product_id']);
            if (!$product) {
                session()->forget('buy_now_item');
                return redirect()->route('cart.index')->with('error', 'Product not found.');
            }

            // Create a collection with the buy now item
            $cartItems = collect([
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

            $subtotal = $product->current_price * $buyNowItem['quantity'];
            $checkoutType = 'buy_now';
        } else {
            // Regular cart checkout
            if ($this->cartService->getTotalQuantity() === 0) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty.');
            }

            $cartItems = $this->cartService->getContent();
            $subtotal = $this->cartService->getSubtotal();
            $checkoutType = 'cart';
        }

        $user = auth()->user();

        return view('public.checkout.livewire-index', compact('cartItems', 'subtotal', 'user', 'checkoutType'));
    }

    /**
     * Process checkout - creates Stripe session but NOT the order yet
     */
    /**
     * Process checkout - creates Stripe session but NOT the order yet
     */
    /**
     * Process checkout - creates Stripe session but NOT the order yet
     */
    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name'     => ['required', 'string', 'max:255'],
            'shipping_email'    => ['required', 'email', 'max:255'],
            'shipping_phone'    => ['required', 'string', 'max:20'],
            'shipping_address'  => ['required', 'string', 'max:500'],
            'shipping_city'     => ['required', 'string', 'max:100'],
            'shipping_state'    => ['nullable', 'string', 'max:100'],
            'shipping_zipcode'  => ['required', 'string', 'max:20'],
            'shipping_country'  => ['required', 'string', 'size:2'],
            'billing_same'      => ['boolean'],
        ]);

        // Store shipping info in session
        session(['checkout_shipping' => $validated]);

        // Determine checkout type from session
        $buyNowItem = session('buy_now_item');
        $checkoutType = $buyNowItem ? 'buy_now' : 'cart';

        try {
            // Create a temporary checkout session ID
            $checkoutSessionId = uniqid('checkout_', true);
            session(['checkout_session_id' => $checkoutSessionId]);

            // Create Stripe session
            Stripe::setApiKey(config('cashier.secret'));

            $lineItems = [];

            if ($checkoutType === 'buy_now' && $buyNowItem) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => $buyNowItem['name'],
                            'description' => "SKU: {$buyNowItem['sku']}",
                        ],
                        'unit_amount' => (int) ($buyNowItem['price'] * 100),
                    ],
                    'quantity' => $buyNowItem['quantity'],
                ];

                $metadata = [
                    'checkout_type' => 'buy_now',
                    'checkout_session_id' => $checkoutSessionId,
                    'product_id' => $buyNowItem['product_id'],
                    'quantity' => $buyNowItem['quantity'],
                    'price' => $buyNowItem['price'],
                ];
            } else {
                $cartItems = $this->cartService->getContent();
                foreach ($cartItems as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'usd',
                            'product_data' => [
                                'name'        => $item->name,
                                'description' => "SKU: {$item->attributes->sku}",
                            ],
                            'unit_amount' => (int) ($item->price * 100),
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                $metadata = [
                    'checkout_type' => 'cart',
                    'checkout_session_id' => $checkoutSessionId,
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                'customer_email'       => $validated['shipping_email'],
                'metadata'             => $metadata,
            ]);

            session(['stripe_session_id' => $session->id]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout.livewire')
                ->with('error', 'Failed to process checkout: ' . $e->getMessage());
        }
    }

    /**
     * Stripe redirects here after successful payment
     */
    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Invalid checkout session.');
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));
            $stripeSession = Session::retrieve($sessionId);

            // Check if order already exists for this session
            $order = Order::where('stripe_session_id', $sessionId)->first();

            if (!$order && $stripeSession->payment_status === 'paid') {
                // Create order only after successful payment
                $shippingData = session('checkout_shipping');

                if (!$shippingData) {
                    throw new \Exception('Shipping information not found in session.');
                }

                if ($stripeSession->metadata['checkout_type'] === 'buy_now') {
                    $order = $this->createBuyNowOrder($shippingData, [
                        'product_id' => $stripeSession->metadata['product_id'],
                        'quantity' => (int) $stripeSession->metadata['quantity'],
                        'price' => (float) $stripeSession->metadata['price'],
                    ], $sessionId);
                } else {
                    $order = $this->orderService->createOrder($shippingData, $sessionId);
                }

                // Clear cart and session data
                if ($stripeSession->metadata['checkout_type'] === 'cart') {
                    $this->cartService->clear();
                }

                session()->forget(['buy_now_item', 'checkout_shipping', 'stripe_session_id', 'checkout_session_id']);

                // Send confirmation emails
                $this->sendOrderEmails($order);
            }

            if (!$order) {
                throw new \Exception('Failed to create order.');
            }

            return view('public.checkout.success', compact('order'));
        } catch (\Exception $e) {
            Log::error('Failed to process successful payment', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);

            return redirect()->route('home')
                ->with('error', 'There was an issue processing your order. Please contact support.');
        }
    }

    public function cancel(Request $request): RedirectResponse
    {
        $sessionId = $request->get('session_id');

        // Clear session data
        session()->forget(['buy_now_item', 'checkout_shipping', 'stripe_session_id', 'checkout_session_id']);

        return redirect()->route('cart.index')
            ->with('error', 'Checkout was cancelled. Please try again.');
    }

    /**
     * Create order for buy now (bypasses cart)
     */
    /**
     * Create order for buy now (bypasses cart)
     */
    protected function createBuyNowOrder(array $data, array $buyNowItem, string $stripeSessionId): Order
    {
        $product = Product::find($buyNowItem['product_id']);
        $subtotal = $buyNowItem['price'] * $buyNowItem['quantity'];

        $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
        $itemShipping = $shippingPerItem * $buyNowItem['quantity'];

        $taxRate = $product->tax_rate ?? 0;
        $itemTax = $subtotal * ($taxRate / 100);

        $total = $subtotal + $itemShipping + $itemTax;

        $order = Order::create([
            'user_id'        => auth()->id(),
            'status'         => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'stripe_session_id' => $stripeSessionId,

            'shipping_name'     => $data['shipping_name'],
            'shipping_email'    => $data['shipping_email'],
            'shipping_phone'    => $data['shipping_phone'],
            'shipping_address'  => $data['shipping_address'],
            'shipping_city'     => $data['shipping_city'],
            'shipping_state'    => $data['shipping_state'] ?? null,
            'shipping_zipcode'  => $data['shipping_zipcode'],
            'shipping_country'  => $data['shipping_country'],

            'billing_name'     => $data['shipping_name'],
            'billing_email'    => $data['shipping_email'],
            'billing_phone'    => $data['shipping_phone'],
            'billing_address'  => $data['shipping_address'],
            'billing_city'     => $data['shipping_city'],
            'billing_state'    => $data['shipping_state'] ?? null,
            'billing_zipcode'  => $data['shipping_zipcode'],
            'billing_country'  => $data['shipping_country'],

            'subtotal'      => $subtotal,
            'tax'           => $itemTax,
            'shipping_cost' => $itemShipping,
            'discount'      => 0,
            'total'         => $total,
        ]);

        $order->items()->create([
            'product_id'  => $buyNowItem['product_id'],
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'price'       => $buyNowItem['price'],
            'quantity'    => $buyNowItem['quantity'],
            'subtotal'    => $subtotal,
            'shipping_cost' => $itemShipping,
            'tax'         => $itemTax,
        ]);

        if ($product && $product->manage_stock) {
            $product->decreaseStock($buyNowItem['quantity']);
        }

        return $order;
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        Log::info('🔔 WEBHOOK RECEIVED', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('cashier.webhook.secret');

        if (empty($endpointSecret)) {
            Log::warning('Stripe webhook secret not configured.');
            $event = \Stripe\Event::constructFrom(json_decode($payload, true));
        } else {
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\UnexpectedValueException $e) {
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleSuccessfulPayment($event->data->object);
                break;
            case 'checkout.session.expired':
                $this->handleExpiredPayment($event->data->object);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleSuccessfulPayment($stripeSession): void
    {
        // Check if order already exists
        $order = Order::where('stripe_session_id', $stripeSession->id)->first();

        if ($order) {
            Log::info('Order already exists for this session', ['session_id' => $stripeSession->id]);
            return;
        }

        try {
            // For webhook, we don't have session data, so we need to use metadata
            $shippingData = [
                'shipping_name' => $stripeSession->customer_details->name ?? 'Customer',
                'shipping_email' => $stripeSession->customer_email,
                'shipping_phone' => $stripeSession->customer_details->phone ?? '',
                'shipping_address' => $stripeSession->customer_details->address->line1 ?? '',
                'shipping_city' => $stripeSession->customer_details->address->city ?? '',
                'shipping_state' => $stripeSession->customer_details->address->state ?? '',
                'shipping_zipcode' => $stripeSession->customer_details->address->postal_code ?? '',
                'shipping_country' => $stripeSession->customer_details->address->country ?? '',
            ];

            if ($stripeSession->metadata['checkout_type'] === 'buy_now') {
                $order = $this->createBuyNowOrder($shippingData, [
                    'product_id' => $stripeSession->metadata['product_id'],
                    'quantity' => (int) $stripeSession->metadata['quantity'],
                    'price' => (float) $stripeSession->metadata['price'],
                ], $stripeSession->id);

                $this->sendOrderEmails($order);
            } else {
                // For cart checkout, we need to have stored cart data somewhere
                // This is a limitation - better to rely on the success redirect
                Log::warning('Cart checkout webhook not fully implemented', ['session_id' => $stripeSession->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create order from webhook', [
                'error' => $e->getMessage(),
                'session_id' => $stripeSession->id
            ]);
        }
    }

    protected function handleExpiredPayment($stripeSession): void
    {
        Log::info('Payment session expired', ['session_id' => $stripeSession->id]);
    }

    protected function sendOrderEmails(Order $order): void
    {
        // Customer confirmation
        try {
            Mail::to($order->shipping_email)
                ->send(new OrderConfirmation($order));
        } catch (\Throwable $e) {
            Log::error('❌ Customer email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        sleep(1); // Avoid rate limiting

        // Admin notification
        $adminEmail = config('mail.admin_address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)
                    ->send(new AdminNewOrderNotification($order));
            } catch (\Throwable $e) {
                Log::error('❌ Admin email failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }


    /**
     * Process checkout with data already in session (for Livewire)
     */
    public function processWithData(): RedirectResponse
    {
        // Get validated data from session
        $validated = session('checkout_validated_data');

        if (!$validated) {
            return redirect()->route('checkout.livewire')
                ->with('error', 'Checkout data not found. Please try again.');
        }

        // Clear the session data
        session()->forget('checkout_validated_data');

        // Store shipping info in session
        session(['checkout_shipping' => $validated]);

        // Determine checkout type from session
        $buyNowItem = session('buy_now_item');
        $checkoutType = $buyNowItem ? 'buy_now' : 'cart';

        try {
            // Create a temporary checkout session ID
            $checkoutSessionId = uniqid('checkout_', true);
            session(['checkout_session_id' => $checkoutSessionId]);

            // Create Stripe session
            Stripe::setApiKey(config('cashier.secret'));

            $lineItems = [];

            if ($checkoutType === 'buy_now' && $buyNowItem) {
                // Single product for buy now
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => $buyNowItem['name'],
                            'description' => "SKU: {$buyNowItem['sku']}",
                        ],
                        'unit_amount' => (int) ($buyNowItem['price'] * 100),
                    ],
                    'quantity' => $buyNowItem['quantity'],
                ];

                $metadata = [
                    'checkout_type' => 'buy_now',
                    'checkout_session_id' => $checkoutSessionId,
                    'product_id' => $buyNowItem['product_id'],
                    'quantity' => $buyNowItem['quantity'],
                    'price' => $buyNowItem['price'],
                ];
            } else {
                // Multiple products from cart
                $cartItems = $this->cartService->getContent();
                foreach ($cartItems as $item) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'usd',
                            'product_data' => [
                                'name'        => $item->name,
                                'description' => "SKU: {$item->attributes->sku}",
                            ],
                            'unit_amount' => (int) ($item->price * 100),
                        ],
                        'quantity' => $item->quantity,
                    ];
                }

                $metadata = [
                    'checkout_type' => 'cart',
                    'checkout_session_id' => $checkoutSessionId,
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                'customer_email'       => $validated['shipping_email'],
                'metadata'             => $metadata,
            ]);

            // Store Stripe session ID
            session(['stripe_session_id' => $session->id]);

            // Redirect to Stripe
            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout.livewire')
                ->with('error', 'Failed to process checkout: ' . $e->getMessage());
        }
    }

    public function successWithOrder(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('public.checkout.success', compact('order'));
    }

    /**
     * Show success page for COD orders.
     */
    public function codSuccess(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('public.checkout.cod-success', compact('order'));
    }
}
