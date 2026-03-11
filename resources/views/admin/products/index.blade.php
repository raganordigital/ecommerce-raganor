@extends('admin.layouts.admin')

@section('title', 'Products')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Products</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $products->total() }} total products</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="flex items-center gap-2 bg-gray-900 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Product
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-5">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            {{-- Status --}}
            <select name="status" class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            {{-- Featured --}}
            <select name="featured" class="px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white">
                <option value="">All Products</option>
                <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition-colors">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status', 'featured']))
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
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
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Product</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">SKU</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Price</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Stock</th>
                        <th class="px-5 py-4 text-left text-xs font-bold tracking-widest uppercase text-gray-400">Status</th>
                        <th class="px-5 py-4 text-right text-xs font-bold tracking-widest uppercase text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors group">

                        {{-- Product Name + Image --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($product->primaryImage)
                                    <img src="{{ Storage::url($product->primaryImage->thumbnail_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate max-w-48">{{ $product->name }}</p>
                                    @if($product->is_featured)
                                    <span class="text-xs font-bold text-amber-600">★ Featured</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- SKU --}}
                        <td class="px-5 py-4">
                            <span class="text-xs font-mono text-gray-500">{{ $product->sku }}</span>
                        </td>

                        {{-- Price --}}
                        <td class="px-5 py-4">
                            <div>
                                @if($product->sale_price)
                                <span class="text-sm font-bold text-gray-900">${{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-xs text-gray-400 line-through ml-1">${{ number_format($product->price, 2) }}</span>
                                @else
                                <span class="text-sm font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Stock --}}
                        <td class="px-5 py-4">
                            @if(!$product->manage_stock)
                            <span class="text-xs text-gray-400">Untracked</span>
                            @elseif($product->stock_quantity <= 0) <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Out of stock
                                </span>
                                @elseif($product->stock_quantity <= 10) <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    {{ $product->stock_quantity }} left
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        {{ $product->stock_quantity }} in stock
                                    </span>
                                    @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors
                                               {{ $product->is_active
                                                   ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                   : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}" title="Click to toggle">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-1">

                                {{-- View on store --}}
                                <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="View on store">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit product">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">No products found</p>
                            <p class="text-xs text-gray-400">
                                @if(request()->hasAny(['search', 'status', 'featured']))
                                Try adjusting your filters or
                                <a href="{{ route('admin.products.index') }}" class="text-gray-900 underline">clear them</a>
                                @else
                                <a href="{{ route('admin.products.create') }}" class="text-gray-900 underline">Create your first product</a>
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $products->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
