@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="max-w-md mx-auto">
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You for Your Order!</h1>
        
        <p class="text-gray-600 mb-6">
            Your order #{{ $order->order_number }} has been placed successfully.
            We've sent a confirmation email to {{ $order->shipping_email }}.
        </p>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h2 class="font-semibold mb-3">Order Summary</h2>
            
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm py-2">
                    <span>{{ $item->product_name }} x {{ $item->quantity }}</span>
                    <span>${{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
            
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between font-semibold">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="space-x-4">
            <a href="{{ route('products.index') }}" 
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Continue Shopping
            </a>
            
            @auth
                <a href="{{ route('orders.index') }}" 
                   class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700">
                    View My Orders
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection