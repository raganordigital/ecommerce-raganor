@props(['product', 'buttonId' => 'buy-now-btn'])

<div class="flex items-center border rounded-md">
    <button type="button" 
            class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 decrement-qty"
            data-product="{{ $product->id }}">
        -
    </button>
    <input type="number" 
           value="1" 
           min="1" 
           max="{{ $product->quantity }}" 
           class="w-16 text-center border-0 focus:ring-0 qty-input-{{ $product->id }}"
           data-product="{{ $product->id }}">
    <button type="button" 
            class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 increment-qty"
            data-product="{{ $product->id }}">
        +
    </button>
</div>