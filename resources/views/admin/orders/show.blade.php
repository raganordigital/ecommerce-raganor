@extends('admin.layouts.admin')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h1>
            <p class="text-gray-600">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.orders.edit', $order) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg">
                Edit Order
            </a>
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg">
                Back to Orders
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Subtotal:</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Tax:</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">${{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium">Shipping:</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">${{ number_format($order->shipping_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-lg">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Name</label>
                        <p class="font-medium">{{ $order->shipping_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="font-medium">{{ $order->shipping_email }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Phone</label>
                        <p class="font-medium">{{ $order->shipping_phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Shipping Address</h2>
                
                <address class="not-italic">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                    {{ $order->shipping_country }}
                </address>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Status</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <p>
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                                {{ $order->status->label() }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm text-gray-500">Payment Status</label>
                        <p>
                            <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($order->payment_status->value === 'paid') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $order->payment_status->label() }}
                            </span>
                        </p>
                    </div>
                    
                    @if($order->tracking_number)
                    <div>
                        <label class="text-sm text-gray-500">Tracking Number</label>
                        <p class="font-medium">{{ $order->tracking_number }}</p>
                    </div>
                    @endif
                    
                    @if($order->shipping_carrier)
                    <div>
                        <label class="text-sm text-gray-500">Shipping Carrier</label>
                        <p class="font-medium">{{ $order->shipping_carrier }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Status Update -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Status Update</h2>
                
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf
                    
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm mb-3">
                        @foreach(App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ $order->status == $status ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection