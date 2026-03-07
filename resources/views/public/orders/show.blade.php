<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                            <p class="text-gray-600">Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="text-sm text-gray-600 mt-1">Payment: {{ ucfirst($order->payment_status) }}</p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($order->orderItems as $item)
                            <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($item->product && $item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->thumbnail_path) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">No Image</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">
                                        @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-blue-600">
                                            {{ $item->product_name }}
                                        </a>
                                        @else
                                        {{ $item->product_name }}
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }}</p>
                                    <p class="text-sm text-gray-600">Subtotal: ${{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Billing & Shipping -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Billing & Shipping</h3>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">Billing Address</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $order->billing_name }}<br>
                                        {{ $order->billing_address }}<br>
                                        {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zipcode }}<br>
                                        {{ $order->billing_country }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Shipping Address</h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $order->shipping_name }}<br>
                                        {{ $order->shipping_address }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                                        {{ $order->shipping_country }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Totals -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-sm font-medium">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->tax > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Tax:</span>
                                    <span class="text-sm font-medium">${{ number_format($order->tax, 2) }}</span>
                                </div>
                                @endif
                                @if($order->shipping_cost > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Shipping:</span>
                                    <span class="text-sm font-medium">${{ number_format($order->shipping_cost, 2) }}</span>
                                </div>
                                @endif
                                @if($order->discount > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Discount:</span>
                                    <span class="text-sm font-medium text-red-600">-${{ number_format($order->discount, 2) }}</span>
                                </div>
                                @endif
                                <div class="border-t pt-2">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                                        <span class="text-lg font-semibold text-gray-900">${{ number_format($order->total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    @if($order->status === 'shipped' || $order->status === 'delivered')
                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Shipping Information</h3>
                        @if($order->tracking_number)
                        <p class="text-sm text-gray-600">
                            <strong>Tracking Number:</strong> {{ $order->tracking_number }}
                            @if($order->shipping_carrier)
                            ({{ $order->shipping_carrier }})
                            @endif
                        </p>
                        @endif
                        @if($order->shipped_at)
                        <p class="text-sm text-gray-600">
                            <strong>Shipped:</strong> {{ $order->shipped_at->format('M j, Y \a\t g:i A') }}
                        </p>
                        @endif
                        @if($order->delivered_at)
                        <p class="text-sm text-gray-600">
                            <strong>Delivered:</strong> {{ $order->delivered_at->format('M j, Y \a\t g:i A') }}
                        </p>
                        @endif
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to Orders
                        </a>
                        @if($order->status === 'delivered' && $order->orderItems->where('product_id', '!=', null)->count() > 0)
                        <a href="{{ route('reviews.create', $order->orderItems->first()->product->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Write Review
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
