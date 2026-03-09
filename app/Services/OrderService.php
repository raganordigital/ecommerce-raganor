<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create a new order from cart after successful payment.
     *
     * @throws \Exception
     */
    public function createOrder(array $data, string $stripeSessionId): Order
    {
        try {
            DB::beginTransaction();

            $cartItems = $this->cartService->getContent();
            $subtotal  = $this->cartService->getSubtotal();
            $tax       = $subtotal * 0.10;
            $total     = $subtotal + $tax;

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
                'tax'           => $tax,
                'shipping_cost' => 0,
                'discount'      => 0,
                'total'         => $total,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->id,
                    'product_name'=> $item->name,
                    'product_sku' => $item->attributes->sku,
                    'price'       => $item->price,
                    'quantity'    => $item->quantity,
                    'subtotal'    => $item->price * $item->quantity,
                ]);

                $product = Product::find($item->id);
                if ($product && $product->manage_stock) {
                    $product->decreaseStock($item->quantity);
                }
            }

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getOrder(int $orderId): ?Order
    {
        return Order::with(['items', 'user'])->find($orderId);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return Order::with(['items', 'user'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)
            ->with('items')
            ->latest()
            ->paginate(10);
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $order = Order::find($orderId);

        if (! $order) {
            return false;
        }

        $order->status = $status;

        if ($status === 'shipped' && ! $order->shipped_at) {
            $order->shipped_at = now();
        }

        if ($status === 'delivered' && ! $order->delivered_at) {
            $order->delivered_at = now();
        }

        return $order->save();
    }
}