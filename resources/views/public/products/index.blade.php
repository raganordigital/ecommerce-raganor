@extends('layouts.app')

@section('title', 'Products — E-Shop')

@section('content')

{{-- Page Header --}}
<div class="bg-gray-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-4xl font-black tracking-tight mb-1">All Products</h1>
        <p class="text-gray-400 text-sm">
            {{ $products->total() }} {{ Str::plural('product', $products->total()) }} available
            @if(request('search'))
                for <span class="text-white font-semibold">"{{ request('search') }}"</span>
            @endif
        </p>
    </div>
</div>

<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Sidebar Filters --}}
            <aside class="lg:w-60 flex-shrink-0">
                <form method="GET" action="{{ route('products.index') }}" id="filter-form">

                    {{-- Search --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                        <h3 class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-3">Search</h3>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search products..."
                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                        <h3 class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-3">Category</h3>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2.5 py-1.5 cursor-pointer group">
                                <input type="radio" name="category" value=""
                                       {{ !request('category') ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 font-medium">All Categories</span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center gap-2.5 py-1.5 cursor-pointer group">
                                <input type="radio" name="category" value="{{ $category->slug }}"
                                       {{ request('category') == $category->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $category->name }}</span>
                                @if($category->children->count() > 0)
                                    <span class="ml-auto text-xs text-gray-400">{{ $category->children->count() }}</span>
                                @endif
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 mb-4">
                        <h3 class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-3">Sort By</h3>
                        <div class="space-y-1">
                            @foreach([
                                ['value' => 'newest',     'label' => 'Newest First'],
                                ['value' => 'price_low',  'label' => 'Price: Low to High'],
                                ['value' => 'price_high', 'label' => 'Price: High to Low'],
                                ['value' => 'name_asc',   'label' => 'Name: A → Z'],
                                ['value' => 'name_desc',  'label' => 'Name: Z → A'],
                            ] as $option)
                            <label class="flex items-center gap-2.5 py-1.5 cursor-pointer group">
                                <input type="radio" name="sort" value="{{ $option['value'] }}"
                                       {{ request('sort', 'newest') == $option['value'] ? 'checked' : '' }}
                                       onchange="this.form.submit()"
                                       class="w-4 h-4 text-gray-900 border-gray-300 focus:ring-gray-900">
                                <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $option['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Clear Filters --}}
                    @if(request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('products.index') }}"
                       class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear Filters
                    </a>
                    @endif
                </form>
            </aside>

            {{-- Products Grid --}}
            <div class="flex-1 min-w-0">

                {{-- Active Filters Bar --}}
                @if(request()->hasAny(['search', 'category', 'sort']))
                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Active:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1.5 bg-gray-900 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                            "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="hover:text-gray-300">×</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-1.5 bg-gray-900 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                            {{ $categories->firstWhere('slug', request('category'))?->name ?? request('category') }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="hover:text-gray-300">×</a>
                        </span>
                    @endif
                </div>
                @endif

                @forelse($products as $product)
                    @if($loop->first)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @endif

                    {{-- Product Card --}}
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col group">

                        {{-- Image --}}
                        <div class="relative overflow-hidden bg-gray-50 aspect-square">
                            <a href="{{ route('products.show', $product->slug) }}">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->thumbnail_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-200">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            {{-- Badges --}}
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                @if($product->on_sale)
                                    @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-lg">-{{ $discount }}%</span>
                                @endif
                                @if($product->is_featured)
                                    <span class="bg-amber-400 text-gray-900 text-xs font-bold px-2 py-0.5 rounded-lg">Featured</span>
                                @endif
                            </div>

                            {{-- Wishlist --}}
                            <div class="absolute top-3 right-3">
                                <livewire:wishlist.wishlist-button :product="$product" :key="'list-wish-'.$product->id" />
                            </div>

                            {{-- Out of stock overlay --}}
                            @if(!$product->inStock())
                            <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                <span class="bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Out of Stock</span>
                            </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-4 flex flex-col flex-1">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="font-semibold text-gray-900 hover:text-gray-600 text-sm leading-snug mb-1 line-clamp-2 transition-colors">
                                {{ $product->name }}
                            </a>

                            @if($product->short_description)
                                <p class="text-xs text-gray-400 line-clamp-2 mb-3">
                                    {{ $product->short_description }}
                                </p>
                            @endif

                            {{-- Price --}}
                            <div class="flex items-baseline gap-2 mt-auto mb-3">
                                @if($product->on_sale)
                                    <span class="text-lg font-black text-gray-900">${{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-sm text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-lg font-black text-gray-900">${{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>

                            {{-- Actions --}}
                            @if($product->inStock())
                                <div class="flex gap-2">
                                    <livewire:cart.add-to-cart
                                        :product="$product"
                                        :key="'list-cart-'.$product->id"
                                        buttonClass="flex-1 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 transition-colors" />
                                    <a href="{{ route('products.show', $product->slug) }}"
                                       class="flex items-center justify-center w-10 h-10 border-2 border-gray-200 rounded-xl hover:border-gray-900 transition-colors text-gray-500 hover:text-gray-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="block text-center py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded-xl">
                                    View Details
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($loop->last)
                    </div>
                    @endif

                @empty
                    <div class="bg-white rounded-2xl border border-gray-100 py-20 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">No products found</h3>
                        <p class="text-sm text-gray-500 mb-6">Try adjusting your search or filter criteria.</p>
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center gap-2 bg-gray-900 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-gray-700 transition-colors">
                            Clear all filters
                        </a>
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection