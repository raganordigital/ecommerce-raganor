<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Get cart instance for current user/session
     *
     * @return \Darryldecode\Cart\Cart
     */
    protected function getCart()
    {
        $sessionKey = Auth::check() ? 'cart_'.Auth::id() : 'cart_guest_'.session()->getId();

        return \Cart::session($sessionKey);
    }

    /**
     * Add item to cart
     */
    public function add(Product $product, int $quantity = 1, array $options = []): array
    {
        $cart = $this->getCart();

        // Check if item already exists
        $existingItem = $cart->get($product->id);

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            // Check stock
            if ($product->manage_stock && $newQuantity > $product->stock_quantity) {
                return [
                    'success' => false,
                    'message' => 'Not enough stock available.',
                ];
            }

            $cart->update($product->id, [
                'quantity' => $newQuantity,
            ]);

            return [
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cartCount' => $cart->getTotalQuantity(),
            ];
        }

        // Check stock
        if ($product->manage_stock && $quantity > $product->stock_quantity) {
            return [
                'success' => false,
                'message' => 'Not enough stock available.',
            ];
        }

        // Add new item
        $cart->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->current_price,
            'quantity' => $quantity,
            'attributes' => array_merge([
                'sku' => $product->sku,
                'image' => $product->primaryImage?->thumbnail_path,
                'slug' => $product->slug,
            ], $options),
        ]);

        return [
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'cartCount' => $cart->getTotalQuantity(),
        ];
    }

    /**
     * Update cart item quantity
     */
    public function update(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        $item = $cart->get($productId);

        if (! $item) {
            return [
                'success' => false,
                'message' => 'Item not found in cart.',
            ];
        }

        // Check stock
        $product = Product::find($productId);
        if ($product && $product->manage_stock && $quantity > $product->stock_quantity) {
            return [
                'success' => false,
                'message' => 'Not enough stock available.',
            ];
        }

        if ($quantity <= 0) {
            $cart->remove($productId);
            $message = 'Item removed from cart.';
        } else {
            $cart->update($productId, [
                'quantity' => $quantity,
            ]);
            $message = 'Cart updated successfully.';
        }

        return [
            'success' => true,
            'message' => $message,
            'cartCount' => $cart->getTotalQuantity(),
            'subtotal' => $cart->getSubTotal(),
            'total' => $cart->getTotal(),
        ];
    }

    /**
     * Remove item from cart
     */
    public function remove(int $productId): array
    {
        $cart = $this->getCart();
        $cart->remove($productId);

        return [
            'success' => true,
            'message' => 'Item removed from cart.',
            'cartCount' => $cart->getTotalQuantity(),
            'subtotal' => $cart->getSubTotal(),
            'total' => $cart->getTotal(),
        ];
    }

    /**
     * Get cart contents
     *
     * @return \Darryldecode\Cart\ItemCollection
     */
    public function getContent()
    {
        return $this->getCart()->getContent();
    }

    /**
     * Get cart total quantity
     */
    public function getTotalQuantity(): int
    {
        return $this->getCart()->getTotalQuantity();
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotal(): float
    {
        return $this->getCart()->getSubTotal();
    }

    /**
     * Get cart total
     */
    public function getTotal(): float
    {
        return $this->getCart()->getTotal();
    }

    /**
     * Clear cart
     */
    public function clear(): void
    {
        $this->getCart()->clear();
    }
}
