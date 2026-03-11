<div>
    @if($cartItems && $cartItems->count() > 0)
    <div class="container mx-auto px-4 py-8">
        <!-- Header with Progress Steps -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Checkout</h1>

            <!-- Progress Steps -->
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                    <span class="ml-2 text-sm font-medium text-gray-900">Shipping</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold">2</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Payment</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-semibold">3</div>
                    <span class="ml-2 text-sm font-medium text-gray-500">Confirmation</span>
                </div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </p>
        </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Checkout Form -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Form Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Shipping Information</h2>
                        <p class="text-sm text-gray-500 mt-1">Enter your shipping details below</p>
                    </div>

                    <div class="p-6">
                        <!-- Error Display -->
                        @if($errors->has('checkout'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-600 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $errors->first('checkout') }}
                            </p>
                        </div>
                        @endif

                        <!-- Form -->
                        <form wire:submit.prevent="processCheckout">
                            @csrf
                            <input type="hidden" name="checkout_type" value="{{ $checkoutType }}">

                            <!-- Contact Information -->
                            <div class="mb-6">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="shipping_name" wire:model="shipping_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_name') border-red-300 @enderror" placeholder="John Doe" required>
                                        @error('shipping_name')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" id="shipping_email" wire:model="shipping_email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_email') border-red-300 @enderror" placeholder="john@example.com" required>
                                        @error('shipping_email')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" id="shipping_phone" wire:model="shipping_phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_phone') border-red-300 @enderror" placeholder="+1 (555) 000-0000" required>
                                        @error('shipping_phone')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Address -->
                            <div class="mb-6">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Shipping Address</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">
                                            Street Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="shipping_address" wire:model="shipping_address" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_address') border-red-300 @enderror" placeholder="123 Main St" required>
                                        @error('shipping_address')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">
                                            City <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="shipping_city" wire:model="shipping_city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_city') border-red-300 @enderror" placeholder="New York" required>
                                        @error('shipping_city')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">
                                            State / Province
                                        </label>
                                        <input type="text" id="shipping_state" wire:model="shipping_state" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_state') border-red-300 @enderror" placeholder="NY">
                                        @error('shipping_state')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_zipcode" class="block text-sm font-medium text-gray-700 mb-1">
                                            ZIP / Postal Code <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="shipping_zipcode" wire:model="shipping_zipcode" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_zipcode') border-red-300 @enderror" placeholder="10001" required>
                                        @error('shipping_zipcode')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">
                                            Country <span class="text-red-500">*</span>
                                        </label>
                                        <select id="shipping_country" wire:model="shipping_country" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('shipping_country') border-red-300 @enderror" required>
                                            <option value="">Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                        @error('shipping_country')
                                        <p class="mt-1 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Billing Address</h3>

                                <label class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                                    <input type="checkbox" wire:model="billing_same" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-3 text-sm text-gray-700">Same as shipping address</span>
                                </label>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-6 border-t border-gray-200 pt-6">
                                <h3 class="text-md font-medium text-gray-900 mb-4">Payment Method</h3>

                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors {{ $paymentMethod === 'stripe' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <input type="radio" wire:model="paymentMethod" value="stripe" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Credit / Debit Card</span>
                                            <span class="text-xs text-gray-500">Pay securely with Stripe</span>
                                        </span>
                                    </label>

                                    @if($this->codAvailable)
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors {{ $paymentMethod === 'cod' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <input type="radio" wire:model="paymentMethod" value="cod" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Cash on Delivery</span>
                                            <span class="text-xs text-gray-500">Pay when you receive your order</span>
                                        </span>
                                    </label>
                                    @endif
                                </div>
                                @error('paymentMethod') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full {{ $checkoutType == 'buy_now' ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white py-4 px-6 rounded-lg font-medium transition-colors flex items-center justify-center group" wire:loading.attr="disabled" wire:target="processCheckout">
                                <span wire:loading.remove wire:target="processCheckout">Proceed to Payment (${{ number_format($subtotal + $totalShipping + $totalTax, 2) }})</span>
                                <span wire:loading wire:target="processCheckout" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                                <svg wire:loading.remove wire:target="processCheckout" class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </button>

                            <!-- Secure Checkout Notice -->
                            <div class="mt-4 flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>Your information is secure and encrypted</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary (Desktop) -->
            <div class="lg:w-1/3 hidden lg:block">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-4 mb-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($cartItems as $item)
                        <div class="flex items-start gap-3 border-b border-gray-100 pb-4">
                            @if($item->attributes->image ?? false)
                            <div class="flex-shrink-0">
                                <img src="{{ Storage::url($item->attributes->image) }}" alt="{{ $item->name }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $item->name }}</span>
                                        <p class="text-xs text-gray-500 mt-1">SKU: {{ $item->attributes->sku ?? 'N/A' }}</p>
                                    </div>
                                    <span class="font-semibold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>

                                <!-- Quantity Controls for Desktop -->
                                <div class="flex justify-between items-center mt-3">
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button type="button" wire:click="decrementQuantity({{ $item->id }})" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded-l-lg transition-colors">
                                            -
                                        </button>
                                        <span class="w-12 text-center px-2 py-1.5 text-gray-900 font-medium border-x border-gray-300">
                                            {{ $item->quantity }}
                                        </span>
                                        <button type="button" wire:click="incrementQuantity({{ $item->id }})" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded-r-lg transition-colors">
                                            +
                                        </button>
                                    </div>
                                    <button type="button" wire:click="removeItem({{ $item->id }})" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium text-gray-900">${{ number_format($totalShipping, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium text-gray-900">${{ number_format($totalTax, 2) }}</span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="flex justify-between font-semibold text-gray-900">
                                <span class="text-base">Total</span>
                                <span class="text-xl {{ $checkoutType == 'buy_now' ? 'text-green-600' : 'text-blue-600' }}">
                                    ${{ number_format($subtotal + $totalShipping + $totalTax, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 flex flex-wrap gap-3 justify-center">
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span>Secure Payment</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>30-Day Returns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="container mx-auto px-4 py-16">
        <div class="text-center max-w-md mx-auto">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h2 class="mt-4 text-2xl font-semibold text-gray-900">Your cart is empty</h2>
            <p class="mt-2 text-gray-500">Looks like you haven't added any items to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Start Shopping
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Simple JavaScript Test - NO BLADE DIRECTIVES -->
<!-- Simple JavaScript Test - NO BLADE DIRECTIVES -->
<script>
    console.log('🔵 SIMPLE SCRIPT TEST - This should appear in console');

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔵 DOM Content Loaded');

        // Check if Livewire is available
        if (typeof Livewire !== 'undefined') {
            console.log('🔵 Livewire is available!');
        } else {
            console.log('🔵 Livewire NOT available yet');
        }
    });

    // Try to catch Livewire events
    document.addEventListener('livewire:init', function() {
        console.log('🔵 LIVE WIRE INIT EVENT FIRED');

        Livewire.on('process-checkout', function(event) {
            console.log('🔵 PROCESS CHECKOUT EVENT RECEIVED!', event);

            // Get the data from the event
            const eventData = Array.isArray(event) ? event[0] : event;
            const formData = eventData.data || eventData;

            console.log('Form data to submit:', formData);

            // Create a form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("checkout.process") }}';
            form.style.display = 'none';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add all form fields
            Object.keys(formData).forEach(key => {
                if (formData[key] !== undefined && formData[key] !== null) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = String(formData[key]);
                    form.appendChild(input);
                    console.log(`Added field: ${key} = ${formData[key]}`);
                }
            });

            // Append form to body
            document.body.appendChild(form);
            console.log('Form created, submitting now...');

            // Submit the form
            form.submit();
            console.log('Form submitted!');
        });
    });

</script>
