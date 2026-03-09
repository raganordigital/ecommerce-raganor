@extends('admin.layouts.admin')

@section('title', 'Edit Order #' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Edit Order</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">#{{ $order->order_number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.show', $order) }}"
               class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Order
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

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
        </div>
        <ul class="space-y-1 ml-6">
            @foreach($errors->all() as $error)
            <li class="text-sm text-red-700 list-disc">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column (main) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Order Items Summary --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Order Items</h2>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }} × {{ $item->quantity }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium">${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-medium text-green-600">-${{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-base pt-2 border-t border-gray-100 mt-2">
                            <span>Total</span>
                            <span class="text-blue-600">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Customer Details (read‑only) --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Customer Information</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Name</p>
                            <p class="text-sm font-medium">{{ $order->shipping_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="text-sm font-medium">{{ $order->shipping_email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Phone</p>
                            <p class="text-sm font-medium">{{ $order->shipping_phone }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs text-gray-500 mb-1">Shipping Address</p>
                        <p class="text-sm text-gray-900">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                            {{ $order->shipping_country }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right Column (update fields) --}}
            <div class="space-y-5">

                {{-- Order Status --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Order Status</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                                @foreach(App\Enums\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $order->status->value) == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-1.5">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                                @foreach(App\Enums\PaymentStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('payment_status', $order->payment_status->value) == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-1.5">Payment Method</label>
                            <input type="text" id="payment_method" value="{{ ucfirst($order->payment_method) }}" readonly disabled
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-500">
                        </div>
                    </div>
                </div>

                {{-- Tracking Information --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Tracking</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="tracking_number" class="block text-sm font-semibold text-gray-700 mb-1.5">Tracking Number</label>
                            <input type="text" name="tracking_number" id="tracking_number"
                                   value="{{ old('tracking_number', $order->tracking_number) }}"
                                   placeholder="e.g. USPS 9400 1234 5678"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>

                        <div>
                            <label for="shipping_carrier" class="block text-sm font-semibold text-gray-700 mb-1.5">Shipping Carrier</label>
                            <input type="text" name="shipping_carrier" id="shipping_carrier"
                                   value="{{ old('shipping_carrier', $order->shipping_carrier) }}"
                                   placeholder="e.g. FedEx, UPS, DHL"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                    </div>
                </div>

                {{-- Admin Notes --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Notes</h2>

                    <div>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="Internal notes about this order..."
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <button type="submit"
                            class="w-full py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition-colors">
                        Update Order
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection