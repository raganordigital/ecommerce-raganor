@extends('admin.layouts.admin')

@section('title', 'Orders Management')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Orders</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $orders->total() }} total orders</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-5">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by order #, name, email..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            {{-- Status --}}
            <select name="status" class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            {{-- Payment Status --}}
            <select name="payment_status" class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white">
                <option value="">All Payments</option>
                @foreach($paymentStatuses as $status)
                    <option value="{{ $status->value }}" {{ request('payment_status') == $status->value ? 'selected' : '' }}>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            {{-- Date From --}}
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">

            {{-- Date To --}}
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">

            <button type="submit"
                    class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}"
                   class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Order #</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Customer</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Total</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Payment</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Method</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Date</th>
                        <th class="px-5 py-4 text-right text-xs font-bold tracking-widest uppercase text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors group">

                        <td class="px-5 py-4 whitespace-nowrap text-sm font-mono font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $order->shipping_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->shipping_email }}</div>
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            ${{ number_format($order->total, 2) }}
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                                  style="background-color: {{ $order->status->color() }}10; color: {{ $order->status->color() }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $order->status->color() }}"></span>
                                {{ $order->status->label() }}
                            </span>
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap">
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
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-600 uppercase">
                            {{ $order->payment_method }}
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="View order">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order) }}"
                                   class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                   title="Edit order">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">No orders found</p>
                            <p class="text-xs text-gray-400">
                                @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                                    Try adjusting your filters or
                                    <a href="{{ route('admin.orders.index') }}" class="text-gray-900 underline">clear them</a>
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection