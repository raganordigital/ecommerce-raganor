<div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col group">

    {{-- Image --}}
    <div class="relative overflow-hidden bg-gray-100 aspect-square">
        <a href="{{ route('products.show', $product->slug) }}">
            @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->thumbnail_path) }}"
                     alt="{{ $product->primaryImage->alt_text ?? $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </a>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if($product->on_sale)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-lg">SALE</span>
            @endif
            @if($product->is_featured ?? false)
                <span class="bg-amber-400 text-gray-900 text-xs font-bold px-2 py-0.5 rounded-lg">FEATURED</span>
            @endif
        </div>

        {{-- Wishlist button --}}
        <div class="absolute top-3 right-3">
            <livewire:wishlist.wishlist-button :product="$product" :key="$keyPrefix.'-wish-'.$product->id" />
        </div>

        {{-- Out of stock overlay --}}
        @if(!$product->inStock())
            <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                <span class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Out of Stock</span>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('products.show', $product->slug) }}"
           class="font-semibold text-gray-900 hover:text-gray-600 text-sm leading-snug mb-1 line-clamp-2">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        @if($product->reviews_count > 0)
        <div class="flex items-center gap-1 mb-2">
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-200' }}"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            <span class="text-xs text-gray-400">({{ $product->reviews_count }})</span>
        </div>
        @endif

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mt-auto mb-3">
            <span class="text-lg font-black text-gray-900">{{ $product->formatted_price }}</span>
            @if($product->on_sale)
                <span class="text-sm text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                @php
                    $discount = round((($product->price - $product->sale_price) / $product->price) * 100);
                @endphp
                <span class="text-xs font-bold text-red-500">-{{ $discount }}%</span>
            @endif
        </div>

        {{-- Add to Cart --}}
        @if($product->inStock())
            <livewire:cart.add-to-cart :product="$product" :key="$keyPrefix.'-cart-'.$product->id" />
        @else
            <button disabled class="w-full py-2.5 text-sm font-semibold bg-gray-100 text-gray-400 rounded-xl cursor-not-allowed">
                Out of Stock
            </button>
        @endif
    </div>
</div>