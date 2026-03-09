@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="min-h-screen bg-gray-950 py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">My Orders</h1>
                <p class="text-sm text-gray-500 mt-1">Track and manage your purchases</p>
            </div>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-400 hover:bg-amber-300 text-gray-900 font-bold text-sm rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Shop More
            </a>
        </div>

        @if($orders->count() > 0)

            <div class="space-y-4">
                @foreach($orders as $order)
                @php
                    $statusVal  = $order->status instanceof \BackedEnum ? $order->status->value : $order->status;
                    $paymentVal = $order->payment_status instanceof \BackedEnum ? $order->payment_status->value : $order->payment_status;
                    $statusBadge = match($statusVal) {
                        'processing' => 'bg-blue-500/15 text-blue-400 border-blue-500/20',
                        'shipped'    => 'bg-purple-500/15 text-purple-400 border-purple-500/20',
                        'delivered'  => 'bg-green-500/15 text-green-400 border-green-500/20',
                        'cancelled'  => 'bg-red-500/15 text-red-400 border-red-500/20',
                        default      => 'bg-amber-500/15 text-amber-400 border-amber-500/20',
                    };
                @endphp
                <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-colors">
                    {{-- Order header row --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 px-6 py-4 border-b border-white/5">
                        <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Order</p>
                                <p class="text-sm font-bold text-white mt-0.5">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Date</p>
                                <p class="text-sm text-gray-300 mt-0.5">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Total</p>
                                <p class="text-sm font-bold text-amber-400 mt-0.5">${{ number_format($order->total, 2) }}</p>
                            </div>
                            <div class="flex items-start gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadge }}">
                                    {{ ucfirst($statusVal) }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $paymentVal === 'paid' ? 'bg-green-500/15 text-green-400 border-green-500/20' : 'bg-yellow-500/15 text-yellow-400 border-yellow-500/20' }}">
                                    {{ ucfirst($paymentVal) }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('orders.show', $order) }}"
                           class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 bg-white/8 hover:bg-white/12 text-white text-sm font-semibold rounded-xl transition-colors">
                            View Details
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                    {{-- Items preview --}}
                    <div class="px-6 py-3">
                        <p class="text-xs text-gray-500">
                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}:
                            {{ $order->items->take(3)->pluck('product_name')->join(', ') }}{{ $order->items->count() > 3 ? ' +' . ($order->items->count() - 3) . ' more' : '' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links() }}
            </div>

        @else
            <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-white mb-2">No orders yet</h2>
                <p class="text-gray-500 text-sm mb-6">Start shopping to place your first order!</p>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-amber-400 hover:bg-amber-300 text-gray-900 font-black text-sm rounded-xl transition-colors">
                    Browse Products →
                </a>
            </div>
        @endif

    </div>
</div>
@endsection