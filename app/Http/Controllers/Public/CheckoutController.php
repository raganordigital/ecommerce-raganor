<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Display checkout page
     */
    public function index(): View|RedirectResponse
    {
        // Check if cart is empty
        if ($this->cartService->getTotalQuantity() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Get cart items for display
        $cartItems = $this->cartService->getContent();
        $subtotal = $this->cartService->getSubtotal();

        // Get authenticated user for pre-filling form
        $user = auth()->user();

        return view('public.checkout.index', compact('cartItems', 'subtotal', 'user'));
    }

    /**
     * Process checkout and create Stripe session
     */
    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_zipcode' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'size:2'],
            'billing_same' => ['boolean'],
        ]);

        try {
            // Create order in database
            $order = $this->orderService->createOrder($request->all());

            // Create Stripe checkout session
            Stripe::setApiKey(config('cashier.secret'));

            $lineItems = [];
            $cartItems = $this->cartService->getContent();

            foreach ($cartItems as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item->name,
                            'description' => "SKU: {$item->attributes->sku}",
                        ],
                        'unit_amount' => (int) ($item->price * 100), // Stripe uses cents
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['order' => $order->order_number]),
                'cancel_url' => route('checkout.cancel', ['order' => $order->order_number]),
                'customer_email' => $request->shipping_email,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            // Update order with Stripe session ID
            $order->update([
                'payment_id' => $session->id,
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process checkout: '.$e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request, string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        // Clear the cart
        $this->cartService->clear();

        return view('public.checkout.success', compact('order'));
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Update order status to cancelled
        $order->update([
            'status' => 'cancelled',
        ]);

        return redirect()->route('cart.index')
            ->with('error', 'Checkout was cancelled. Please try again.');
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleSuccessfulPayment($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->handleExpiredPayment($session);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment from webhook
     */
    protected function handleSuccessfulPayment($session): void
    {
        $order = Order::where('payment_id', $session->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            // Send order confirmation email
            // Mail::to($order->shipping_email)->send(new OrderConfirmation($order));
        }
    }

    /**
     * Handle expired payment from webhook
     */
    protected function handleExpiredPayment($session): void
    {
        $order = Order::where('payment_id', $session->id)->first();

        if ($order) {
            $order->update([
                'status' => 'cancelled',
            ]);
        }
    }
}
