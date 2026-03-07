<div>
    @if($wishlistItems->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlistItems as $item)
                <div wire:key="wishlist-{{ $item->id }}" class="bg-white rounded-lg shadow overflow-hidden">
                    <a href="{{ route('products.show', $item->product) }}">
                        @if($item->product->primaryImage)
                            <img src="{{ Storage::url($item->product->primaryImage->thumbnail_path) }}" 
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No image</span>
                            </div>
                        @endif
                    </a>
                    
                    <div class="p-4">
                        <a href="{{ route('products.show', $item->product) }}" 
                           class="text-lg font-semibold text-gray-800 hover:text-blue-600">
                            {{ $item->product->name }}
                        </a>
                        
                        <p class="text-sm text-gray-600 mt-1">
                            {{ Str::limit($item->product->short_description ?? $item->product->description, 100) }}
                        </p>
                        
                        <div class="mt-4">
                            @if($item->product->on_sale)
                                <span class="text-lg font-bold text-red-600">
                                    ${{ number_format($item->product->sale_price, 2) }}
                                </span>
                                <span class="text-sm text-gray-400 line-through ml-2">
                                    ${{ number_format($item->product->price, 2) }}
                                </span>
                            @else
                                <span class="text-lg font-bold text-gray-900">
                                    ${{ number_format($item->product->price, 2) }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            @if($item->product->inStock())
                                <button wire:click="addToCart({{ $item->product->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50">
                                    <span wire:loading.remove wire:target="addToCart({{ $item->product->id }})">
                                        Move to Cart
                                    </span>
                                    <span wire:loading wire:target="addToCart({{ $item->product->id }})">
                                        Moving...
                                    </span>
                                </button>
                            @else
                                <span class="flex-1 bg-gray-300 text-gray-600 py-2 px-4 rounded cursor-not-allowed text-center">
                                    Out of Stock
                                </span>
                            @endif
                            
                            <button wire:click="remove({{ $item->product->id }})"
                                    wire:loading.attr="disabled"
                                    class="bg-red-100 text-red-600 py-2 px-4 rounded hover:bg-red-200 disabled:opacity-50"
                                    title="Remove from wishlist">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <button wire:click="clearWishlist" 
                    wire:confirm="Are you sure you want to clear your wishlist?"
                    class="bg-red-600 text-white py-2 px-6 rounded-lg hover:bg-red-700">
                Clear Wishlist
            </button>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h2 class="mt-4 text-lg font-medium text-gray-900">Your wishlist is empty</h2>
            <p class="mt-2 text-gray-500">Save items you like to your wishlist for later purchase.</p>
            <a href="{{ route('products.index') }}" 
               class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Browse Products
            </a>
        </div>
    @endif
</div>