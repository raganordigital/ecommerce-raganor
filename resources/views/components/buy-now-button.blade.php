@props(['product'])

<form action="{{ route('cart.buy-now') }}" method="POST" class="inline">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="quantity" value="1" class="buy-now-quantity-{{ $product->id }}">
    <button type="submit" 
            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        Buy Now
    </button>
</form>