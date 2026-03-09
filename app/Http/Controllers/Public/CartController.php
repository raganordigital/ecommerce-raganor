<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): View
    {
        $cartItems = $this->cartService->getContent();
        $subtotal = $this->cartService->getSubtotal();

        return view('public.cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        $result = $this->cartService->add($product, $request->quantity);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Buy Now - Stores product in session and redirects to Livewire checkout
     */
    public function buyNow(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::with('primaryImage')->findOrFail($request->product_id);

        // Store in session for the checkout page
        session(['buy_now_item' => [
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'price' => $product->current_price,
            'name' => $product->name,
            'sku' => $product->sku,
            'image' => $product->primaryImage?->thumbnail_path,
        ]]);

        // Redirect to Livewire checkout page
        return redirect()->route('checkout.livewire')
            ->with('success', 'Proceeding to checkout with your selected item.');
    }

    public function updateQuantity(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $result = $this->cartService->update((int) $id, $request->quantity);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function removeItem($id): RedirectResponse
    {
        $result = $this->cartService->remove((int) $id);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->back()->with('success', 'Cart cleared successfully.');
    }
}