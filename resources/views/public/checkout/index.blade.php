@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Checkout Form -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                    @csrf

                    <!-- Shipping Information -->
                    <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name *
                            </label>
                            <input type="text" 
                                   name="shipping_name" 
                                   id="shipping_name" 
                                   value="{{ old('shipping_name', $user?->name) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address *
                            </label>
                            <input type="email" 
                                   name="shipping_email" 
                                   id="shipping_email" 
                                   value="{{ old('shipping_email', $user?->email) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number *
                            </label>
                            <input type="tel" 
                                   name="shipping_phone" 
                                   id="shipping_phone" 
                                   value="{{ old('shipping_phone', $user?->phone) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">
                                Street Address *
                            </label>
                            <input type="text" 
                                   name="shipping_address" 
                                   id="shipping_address" 
                                   value="{{ old('shipping_address', $user?->address) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">
                                City *
                            </label>
                            <input type="text" 
                                   name="shipping_city" 
                                   id="shipping_city" 
                                   value="{{ old('shipping_city', $user?->city) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-1">
                                State / Province
                            </label>
                            <input type="text" 
                                   name="shipping_state" 
                                   id="shipping_state" 
                                   value="{{ old('shipping_state', $user?->state) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('shipping_state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_zipcode" class="block text-sm font-medium text-gray-700 mb-1">
                                ZIP / Postal Code *
                            </label>
                            <input type="text" 
                                   name="shipping_zipcode" 
                                   id="shipping_zipcode" 
                                   value="{{ old('shipping_zipcode', $user?->zipcode) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            @error('shipping_zipcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country *
                            </label>
                            <select name="shipping_country" 
                                    id="shipping_country" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                <option value="">Select Country</option>
                                <option value="US" {{ old('shipping_country', $user?->country) == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('shipping_country', $user?->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="GB" {{ old('shipping_country', $user?->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('shipping_country', $user?->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                            </select>
                            @error('shipping_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Billing Address (Same as Shipping) -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="billing_same" 
                                   value="1" 
                                   checked 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Billing address same as shipping</span>
                        </label>
                    </div>

                    <!-- Order Summary (Mobile) -->
                    <div class="lg:hidden mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Order Summary</h3>
                            @foreach($cartItems as $item)
                                <div class="flex justify-between text-sm py-2">
                                    <span>{{ $item->name }} x {{ $item->quantity }}</span>
                                    <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Total</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        Proceed to Payment (${{ number_format($subtotal, 2) }})
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary (Desktop) -->
        <div class="lg:w-1/3 hidden lg:block">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                
                <div class="space-y-4 mb-4">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <div>
                                <span class="font-medium">{{ $item->name }}</span>
                                <span class="text-gray-500"> x {{ $item->quantity }}</span>
                            </div>
                            <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Total</span>
                        <span class="text-blue-600">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Taxes and shipping calculated at payment</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection