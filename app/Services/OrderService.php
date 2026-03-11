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
            $subtotal = 0;
            $totalShipping = 0;
            $totalTax = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                if (!$product) continue;

                $itemSubtotal = $item->price * $item->quantity;
                $subtotal += $itemSubtotal;

                $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
                $itemShipping = $shippingPerItem * $item->quantity;
                $totalShipping += $itemShipping;

                $taxRate = $product->tax_rate ?? 0;
                $itemTax = $itemSubtotal * ($taxRate / 100);
                $totalTax += $itemTax;

                $orderItems[] = [
                    'product_id'  => $item->id,
                    'product_name' => $item->name,
                    'product_sku' => $item->attributes->sku,
                    'price'       => $item->price,
                    'quantity'    => $item->quantity,
                    'subtotal'    => $itemSubtotal,
                    'shipping_cost' => $itemShipping,
                    'tax'         => $itemTax,
                ];
            }

            $total = $subtotal + $totalShipping + $totalTax;

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
                'tax'           => $totalTax,
                'shipping_cost' => $totalShipping,
                'discount'      => 0,
                'total'         => $total,
            ]);

            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));

                $product = Product::find($itemData['product_id']);
                if ($product && $product->manage_stock) {
                    $product->decreaseStock($itemData['quantity']);
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

    public function createCodOrder(array $data): Order
    {
        try {
            DB::beginTransaction();

            $cartItems = $this->cartService->getContent();
            $subtotal = 0;
            $totalShipping = 0;
            $totalTax = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                if (!$product) continue;

                $itemSubtotal = $item->price * $item->quantity;
                $subtotal += $itemSubtotal;

                $shippingPerItem = $product->free_shipping ? 0 : ($product->shipping_cost ?? 0);
                $itemShipping = $shippingPerItem * $item->quantity;
                $totalShipping += $itemShipping;

                $taxRate = $product->tax_rate ?? 0;
                $itemTax = $itemSubtotal * ($taxRate / 100);
                $totalTax += $itemTax;

                $orderItems[] = [
                    'product_id'  => $item->id,
                    'product_name' => $item->name,
                    'product_sku' => $item->attributes->sku,
                    'price'       => $item->price,
                    'quantity'    => $item->quantity,
                    'subtotal'    => $itemSubtotal,
                    'shipping_cost' => $itemShipping,
                    'tax'         => $itemTax,
                ];
            }

            $total = $subtotal + $totalShipping + $totalTax;

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
                'tax'           => $totalTax,
                'shipping_cost' => $totalShipping,
                'discount'      => 0,
                'total'         => $total,
            ]);

            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));

                $product = Product::find($itemData['product_id']);
                if ($product && $product->manage_stock) {
                    $product->decreaseStock($itemData['quantity']);
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

    public function createBuyNowCodOrder(array $data, array $buyNowItem): Order
    {
        try {
            DB::beginTransaction();

            $subtotal = $buyNowItem['price'] * $buyNowItem['quantity'];
            $tax = $subtotal * 0.10;
            $total = $subtotal + $tax;

            $product = Product::find($buyNowItem['product_id']);

            $order = Order::create([
                'user_id'        => auth()->id(),
                'status'         => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
                'stripe_session_id' => null,

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

            $order->items()->create([
                'product_id'  => $buyNowItem['product_id'],
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'price'       => $buyNowItem['price'],
                'quantity'    => $buyNowItem['quantity'],
                'subtotal'    => $subtotal,
            ]);

            if ($product && $product->manage_stock) {
                $product->decreaseStock($buyNowItem['quantity']);
            }

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create buy now COD order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
