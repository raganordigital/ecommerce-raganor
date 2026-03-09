@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div>
            @if($product->primaryImage)
                <img src="{{ Storage::url($product->primaryImage->image_path) }}" 
                     alt="{{ $product->name }}"
                     class="w-full rounded-lg shadow-lg">
            @endif
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            <p class="text-2xl font-bold text-blue-600 mb-4">${{ number_format($product->current_price, 2) }}</p>
            
            <div class="prose mb-6">
                {{ $product->description }}
            </div>
            
            @if($product->quantity > 0)
                <p class="text-green-600 mb-4">In Stock ({{ $product->quantity }} available)</p>
                
                <!-- Quantity Selector -->
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <div class="flex items-center border rounded-md w-32">
                        <button type="button" 
                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 decrement-qty"
                                data-product="{{ $product->id }}">
                            -
                        </button>
                        <input type="number" 
                               id="quantity" 
                               value="1" 
                               min="1" 
                               max="{{ $product->quantity }}" 
                               class="w-16 text-center border-0 focus:ring-0 quantity-input"
                               data-product="{{ $product->id }}">
                        <button type="button" 
                                class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 increment-qty"
                                data-product="{{ $product->id }}">
                            +
                        </button>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1" id="add-to-cart-quantity">
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </form>
                    
                    <!-- Buy Now Form -->
                    <form action="{{ route('cart.buy-now') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1" id="buy-now-quantity">
                        <button type="submit" 
                                class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Buy Now
                        </button>
                    </form>
                </div>
            @else
                <p class="text-red-600">Out of Stock</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.querySelector('.quantity-input');
    const addToCartQty = document.getElementById('add-to-cart-quantity');
    const buyNowQty = document.getElementById('buy-now-quantity');
    const productId = quantityInput.dataset.product;
    const maxQty = parseInt(quantityInput.max) || 999;

    // Update both hidden inputs with current quantity
    function updateQuantityInputs(value) {
        addToCartQty.value = value;
        buyNowQty.value = value;
    }

    // Decrement button
    document.querySelector('.decrement-qty').addEventListener('click', function() {
        let value = parseInt(quantityInput.value) || 1;
        if (value > 1) {
            value--;
            quantityInput.value = value;
            updateQuantityInputs(value);
        }
    });

    // Increment button
    document.querySelector('.increment-qty').addEventListener('click', function() {
        let value = parseInt(quantityInput.value) || 1;
        if (value < maxQty) {
            value++;
            quantityInput.value = value;
            updateQuantityInputs(value);
        }
    });

    // Manual input change
    quantityInput.addEventListener('change', function() {
        let value = parseInt(this.value) || 1;
        
        if (value < 1) value = 1;
        if (value > maxQty) value = maxQty;
        
        this.value = value;
        updateQuantityInputs(value);
    });
});
</script>
@endpush
@endsection