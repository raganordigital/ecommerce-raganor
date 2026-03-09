<div>
    @if($cartItems->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Mobile View (Cards) -->
                    <div class="block lg:hidden divide-y divide-gray-200">
                        @foreach($cartItems as $item)
                            <div wire:key="cart-{{ $item->id }}" class="p-4">
                                <div class="flex gap-4">
                                    @if($item->attributes->image)
                                        <img src="{{ Storage::url($item->attributes->image) }}" 
                                             alt="{{ $item->name }}"
                                             class="w-24 h-24 object-cover rounded-lg">
                                    @endif
                                    <div class="flex-1">
                                        <a href="{{ route('products.show', $item->attributes->slug) }}" 
                                           class="font-medium text-gray-900 hover:text-blue-600">
                                            {{ $item->name }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $item->attributes->sku }}</p>
                                        
                                        <div class="flex justify-between items-center mt-3">
                                            <span class="font-semibold text-gray-900">${{ number_format($item->price, 2) }}</span>
                                            <button wire:click="removeItem({{ $item->id }})" 
                                                    class="text-red-500 hover:text-red-700 p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div class="flex justify-between items-center mt-3">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button wire:click="decrementQuantity({{ $item->id }})" 
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg">
                                                    -
                                                </button>
                                                <span class="px-4 py-1 text-gray-900 font-medium border-x border-gray-300">
                                                    {{ $item->quantity }}
                                                </span>
                                                <button wire:click="incrementQuantity({{ $item->id }})" 
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg">
                                                    +
                                                </button>
                                            </div>
                                            <span class="font-semibold text-gray-900">
                                                ${{ number_format($item->price * $item->quantity, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop View (Table) -->
                    <table class="hidden lg:table w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($cartItems as $item)
                                <tr wire:key="cart-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($item->attributes->image)
                                                <div class="flex-shrink-0">
                                                    <img src="{{ Storage::url($item->attributes->image) }}" 
                                                         alt="{{ $item->name }}"
                                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <a href="{{ route('products.show', $item->attributes->slug) }}" 
                                                   class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                                    {{ $item->name }}
                                                </a>
                                                <p class="text-sm text-gray-500 mt-1">SKU: {{ $item->attributes->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button wire:click="decrementQuantity({{ $item->id }})" 
                                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-700 rounded-l-lg transition-colors">
                                                    -
                                                </button>
                                                <span class="w-12 text-center px-2 py-1.5 text-gray-900 font-medium border-x border-gray-300">
                                                    {{ $item->quantity }}
                                                </span>
                                                <button wire:click="incrementQuantity({{ $item->id }})" 
                                                        class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-700 rounded-r-lg transition-colors">
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button wire:click="removeItem({{ $item->id }})" 
                                                class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors group">
                        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Continue Shopping
                    </a>
                    
                    <button wire:click="clearCart" 
                            wire:confirm="Are you sure you want to clear your cart?"
                            class="inline-flex items-center text-gray-600 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear Cart
                    </button>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-gray-500">Calculated at checkout</span>
                        </div>
                        
                        @if($cartItems->count() > 5)
                            <div class="flex justify-between text-gray-600">
                                <span>Quantity Discount</span>
                                <span class="text-green-600">-${{ number_format($bulkDiscount ?? 0, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-gray-900">Total</span>
                                <span class="text-xl font-bold text-blue-600">
                                    ${{ number_format($subtotal, 2) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Inclusive of all taxes</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('checkout.livewire') }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-6 py-3 rounded-lg font-medium transition-colors">
                            Proceed to Checkout
                        </a>
                        
                        <div class="flex items-center justify-center gap-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Secure Checkout
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                SSL Protected
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 px-4">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-32 w-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="mt-6 text-2xl font-semibold text-gray-900">Your cart is empty</h2>
                <p class="mt-2 text-gray-500">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('products.index') }}" 
                   class="mt-8 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    Start Shopping
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    @endif
</div>