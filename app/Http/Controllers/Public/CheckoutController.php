<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function index(): View|RedirectResponse
    {
        if ($this->cartService->getTotalQuantity() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $cartItems = $this->cartService->getContent();
        $subtotal  = $this->cartService->getSubtotal();
        $user      = auth()->user();

        return view('public.checkout.index', compact('cartItems', 'subtotal', 'user'));
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
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

        try {
            $order = $this->orderService->createOrder($request->all());

            Stripe::setApiKey(config('cashier.secret'));

            $lineItems  = [];
            $cartItems  = $this->cartService->getContent();

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

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('checkout.success', ['order' => $order->order_number]),
                'cancel_url'           => route('checkout.cancel', ['order' => $order->order_number]),
                'customer_email'       => $request->shipping_email,
                'metadata'             => [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);

            $order->update(['payment_id' => $session->id]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process checkout: ' . $e->getMessage());
        }
    }

    /**
     * Stripe redirects here after successful payment.
     * We verify the session directly with Stripe API as a reliable fallback
     * in case the webhook hasn't fired yet (race condition).
     */
    public function success(Request $request, string $orderNumber): View|RedirectResponse
    {
        Log::info('✅ SUCCESS PAGE ACCESSED', ['order_number' => $orderNumber]);
        
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        Log::info('📦 Order found in success page', [
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'has_payment_id' => !empty($order->payment_id)
        ]);

        // Verify payment directly with Stripe if still pending
        if ($order->payment_status === PaymentStatus::PENDING && $order->payment_id) {
            Log::info('🔄 Order is pending, checking with Stripe');
            
            try {
                Stripe::setApiKey(config('cashier.secret'));
                $session = Session::retrieve($order->payment_id);

                Log::info('💳 Stripe session retrieved', [
                    'payment_status' => $session->payment_status
                ]);

                if ($session->payment_status === 'paid') {
                    Log::info('✅ Payment confirmed, updating order and sending emails');
                    
                    $order->update([
                        'payment_status' => 'paid',
                        'status'         => 'processing',
                    ]);
                    $order->refresh()->load(['items', 'user']);
                    
                    Log::info('📧 About to call sendOrderEmails from success page');
                    $this->sendOrderEmails($order);
                }
            } catch (\Exception $e) {
                Log::error('❌ Stripe verification failed', ['error' => $e->getMessage()]);
            }
        } else {
            Log::info('⏭️ Skipping Stripe verification', [
                'reason' => $order->payment_status !== PaymentStatus::PENDING ? 'already_paid' : 'no_payment_id'
            ]);
        }

        // Clear cart only after confirmed payment
        if ($order->payment_status === PaymentStatus::PAID) {
            Log::info('🧹 Clearing cart for paid order');
            $this->cartService->clear();
        }

        return view('public.checkout.success', compact('order'));
    }

    public function cancel(Request $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $order->update(['status' => 'cancelled']);

        return redirect()->route('cart.index')
            ->with('error', 'Checkout was cancelled. Please try again.');
    }

    /**
     * Stripe webhook — primary payment confirmation path.
     * Requires STRIPE_WEBHOOK_SECRET in .env to work.
     * Run: stripe listen --forward-to localhost:8000/stripe/webhook
     */
    public function webhook(Request $request)
    {
        Log::info('🔔 WEBHOOK RECEIVED', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);
        
        $payload       = $request->getContent();
        $sigHeader     = $request->header('Stripe-Signature');
        $endpointSecret = config('cashier.webhook.secret');

        // If no webhook secret configured, skip signature verification in local dev
        if (empty($endpointSecret)) {
            Log::warning('Stripe webhook secret not configured. Set STRIPE_WEBHOOK_SECRET in .env');
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } else {
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\UnexpectedValueException $e) {
                Log::error('Invalid webhook payload', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::error('Invalid webhook signature', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        Log::info('📨 Webhook event', ['type' => $event->type]);

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleSuccessfulPayment($event->data->object);
                break;

            case 'checkout.session.expired':
                $this->handleExpiredPayment($event->data->object);
                break;
            
            default:
                Log::info('Unhandled webhook type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleSuccessfulPayment($session): void
    {
        Log::info('💰 handleSuccessfulPayment CALLED', [
            'session_id' => $session->id,
            'payment_status' => $session->payment_status,
            'metadata' => $session->metadata ?? []
        ]);
        
        $order = Order::where('payment_id', $session->id)->first();

        Log::info('📦 Order lookup result', [
            'found' => $order ? 'yes' : 'no',
            'order_id' => $order?->id,
            'current_payment_status' => $order?->payment_status
        ]);

        if ($order && $order->payment_status !== PaymentStatus::PAID) {
            Log::info('✅ Conditions met, updating order and sending emails');
            
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
            ]);

            $order->load(['items', 'user']);

            Log::info('📧 About to call sendOrderEmails from webhook');
            $this->sendOrderEmails($order);
            Log::info('📧 Finished calling sendOrderEmails from webhook');
        } else {
            Log::info('❌ Conditions NOT met', [
                'order_exists' => $order ? 'yes' : 'no',
                'payment_status_match' => $order ? ($order->payment_status !== PaymentStatus::PAID ? 'yes' : 'no') : 'n/a',
                'order_payment_status' => $order?->payment_status
            ]);
        }
    }

    /**
     * Send order confirmation to customer and notification to admin.
     * Called from both the webhook handler and the success() fallback
     * to ensure emails fire exactly once regardless of which path confirms payment.
     * The upstream callers are responsible for guarding against double-sends
     * by only calling this when payment_status transitions to 'paid'.
     */
    protected function sendOrderEmails(Order $order): void
{
    Log::info('📧 sendOrderEmails STARTED', [
        'order_id' => $order->id,
        'customer_email' => $order->user->email,
        'admin_email' => config('mail.admin_address'),
        'admin_email_exists' => !empty(config('mail.admin_address')) ? 'yes' : 'no'
    ]);

    // Customer confirmation
    try {
        Log::info('📨 Attempting to send customer email', [
            'to' => $order->user->email
        ]);
        
        Mail::to($order->user->email)
            ->send(new OrderConfirmation($order));
        
        Log::info('✅ Customer email sent successfully', [
            'order_id' => $order->id,
            'email' => $order->user->email
        ]);
    } catch (\Throwable $e) {
        Log::error('❌ Customer email failed', [
            'order_id' => $order->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    // Add a small delay to avoid Mailtrap rate limiting
    sleep(2); // Wait 2 seconds

    // Admin new-order notification
    $adminEmail = config('mail.admin_address');
    if ($adminEmail) {
        try {
            Log::info('📨 Attempting to send admin email', [
                'order_id' => $order->id,
                'admin_email' => $adminEmail
            ]);
            
            Mail::to($adminEmail)
                ->send(new AdminNewOrderNotification($order));
            
            Log::info('✅ Admin email sent successfully', [
                'order_id' => $order->id,
                'admin_email' => $adminEmail
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Admin order notification email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    } else {
        Log::warning('⚠️ Admin email not configured');
    }
    
    Log::info('📧 sendOrderEmails COMPLETED', ['order_id' => $order->id]);
}

    protected function handleExpiredPayment($session): void
    {
        Log::info('⏰ handleExpiredPayment CALLED', ['session_id' => $session->id]);
        
        $order = Order::where('payment_id', $session->id)->first();

        if ($order) {
            $order->update(['status' => 'cancelled']);
            Log::info('Order cancelled due to expired payment', ['order_id' => $order->id]);
        }
    }
}