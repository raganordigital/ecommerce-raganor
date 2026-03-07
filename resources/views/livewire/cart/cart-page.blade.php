<div>
    @if($cartItems->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($cartItems as $item)
                                <tr wire:key="cart-{{ $item->id }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($item->attributes->image)
                                                <img src="{{ Storage::url($item->attributes->image) }}" 
                                                     alt="{{ $item->name }}"
                                                     class="w-16 h-16 object-cover rounded">
                                            @endif
                                            <div class="ml-4">
                                                <a href="{{ route('products.show', $item->attributes->slug) }}" 
                                                   class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                    {{ $item->name }}
                                                </a>
                                                <p class="text-sm text-gray-500">SKU: {{ $item->attributes->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-900">${{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" 
                                               wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                               value="{{ $item->quantity }}"
                                               min="1"
                                               max="999"
                                               class="w-20 px-2 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-gray-900">
                                            ${{ number_format($item->price * $item->quantity, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button wire:click="removeItem({{ $item->id }})" 
                                                class="text-red-600 hover:text-red-800">
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
                
                <div class="mt-4 flex justify-between">
                    <a href="{{ route('products.index') }}" 
                       class="text-blue-600 hover:text-blue-800">
                        ← Continue Shopping
                    </a>
                    
                    <button wire:click="clearCart" 
                            wire:confirm="Are you sure you want to clear your cart?"
                            class="text-red-600 hover:text-red-800">
                        Clear Cart
                    </button>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-600">Calculated at checkout</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-lg font-bold text-blue-600">
                                    ${{ number_format($subtotal, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" 
                       class="mt-6 block w-full bg-blue-600 text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h2 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h2>
            <p class="mt-2 text-gray-500">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products.index') }}" 
               class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Start Shopping
            </a>
        </div>
    @endif
</div>