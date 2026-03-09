@extends('admin.layouts.admin')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Order #{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.edit', $order) }}"
               class="flex items-center gap-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Order
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Order Items --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400">Order Items</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    ${{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    ${{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Subtotal</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Tax</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Shipping</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-gray-900">${{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Discount</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold text-green-600">-${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="px-6 py-4 text-right text-base font-bold text-gray-900">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-black text-blue-600">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Customer Information --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Customer</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Name</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->shipping_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Email</p>
                        <p class="text-sm text-gray-700">{{ $order->shipping_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Phone</p>
                        <p class="text-sm text-gray-700">{{ $order->shipping_phone }}</p>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Shipping Address</h2>

                <address class="not-italic text-sm text-gray-700 leading-relaxed">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                    {{ $order->shipping_country }}
                </address>
            </div>

            {{-- Order Status & Payment --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Status</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Order Status</p>
                        @php
                            $statusColor = $order->status->color();
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                              style="background-color: {{ $statusColor }}10; color: {{ $statusColor }};">
                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $statusColor }}"></span>
                            {{ $order->status->label() }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Payment Status</p>
                        @php
                            $paymentColor = match($order->payment_status->value) {
                                'paid' => 'green',
                                'pending' => 'yellow',
                                'failed' => 'red',
                                'refunded' => 'gray',
                                default => 'gray'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-{{ $paymentColor }}-100 text-{{ $paymentColor }}-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $paymentColor }}-500"></span>
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                        <p class="text-sm font-medium text-gray-900 uppercase">{{ $order->payment_method }}</p>
                    </div>
                </div>
            </div>

            {{-- Tracking --}}
            @if($order->tracking_number || $order->shipping_carrier)
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Tracking</h2>

                <div class="space-y-3">
                    @if($order->tracking_number)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tracking Number</p>
                        <p class="text-sm font-mono font-medium text-gray-900">{{ $order->tracking_number }}</p>
                    </div>
                    @endif
                    @if($order->shipping_carrier)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Carrier</p>
                        <p class="text-sm text-gray-700">{{ $order->shipping_carrier }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin Notes --}}
            @if($order->notes)
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Notes</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $order->notes }}</p>
            </div>
            @endif

            {{-- Quick Status Update (optional) --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-4">Quick Status</h2>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    <select name="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 mb-3">
                        @foreach(App\Enums\OrderStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ $order->status == $status ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="w-full py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition-colors">
                        Update Status
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection