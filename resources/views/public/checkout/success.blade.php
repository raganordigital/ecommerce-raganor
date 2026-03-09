@extends('layouts.app')

@section('title', 'Order Confirmed')

@section('content')
<div class="min-h-screen bg-gray-950 flex items-center justify-center px-4 py-16">
    <div class="max-w-lg w-full">

        {{-- Animated success icon --}}
        <div class="text-center mb-8">
            <div class="relative w-24 h-24 mx-auto mb-6">
                <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping" style="animation-duration:2s"></div>
                <div class="relative w-24 h-24 bg-green-500/15 border border-green-500/30 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Order Confirmed!</h1>
            <p class="text-gray-400 text-sm leading-relaxed">
                Thank you for your purchase. A confirmation email<br>
                has been sent to <span class="text-white font-semibold">{{ $order->shipping_email }}</span>
            </p>
        </div>

        {{-- Order card --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">

            {{-- Order number banner --}}
            <div class="bg-green-500/10 border-b border-green-500/15 px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-green-500/70 font-semibold uppercase tracking-wide">Order Number</p>
                    <p class="text-lg font-black text-white mt-0.5">{{ $order->order_number }}</p>
                </div>
                <span class="px-3 py-1 bg-green-500/15 border border-green-500/20 text-green-400 text-xs font-bold rounded-full">
                    {{ ucfirst($order->payment_status instanceof \BackedEnum ? $order->payment_status->value : $order->payment_status) }}
                </span>
            </div>

            {{-- Items --}}
            <div class="divide-y divide-white/5">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between px-6 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                    </div>
                    <p class="text-sm font-bold text-white">${{ number_format($item->subtotal, 2) }}</p>
                </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="px-6 py-4 bg-white/3 border-t border-white/8">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">Order Total</span>
                    <span class="text-xl font-black text-amber-400">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping summary --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl px-6 py-4 mb-6">
            <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold mb-2">Shipping To</p>
            <p class="text-sm text-gray-300 leading-relaxed">
                <span class="text-white font-semibold">{{ $order->shipping_name }}</span><br>
                {{ $order->shipping_address }},
                {{ $order->shipping_city }}@if($order->shipping_state), {{ $order->shipping_state }}@endif {{ $order->shipping_zipcode }}
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3">
            @auth
            <a href="{{ route('orders.show', $order) }}"
               class="flex-1 flex items-center justify-center gap-2 py-3 bg-amber-400 hover:bg-amber-300 text-gray-900 font-black text-sm rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Track Order
            </a>
            @endauth
            <a href="{{ route('products.index') }}"
               class="flex-1 flex items-center justify-center gap-2 py-3 bg-white/8 hover:bg-white/12 text-white font-bold text-sm rounded-xl transition-colors">
                Continue Shopping
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

    </div>
</div>
@endsection