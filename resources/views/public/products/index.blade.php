@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Products</h1>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Products</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or description..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Category Filter -->
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                    Apply Filters
                </button>
                <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition duration-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <!-- Product Image -->
            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                @if($product->primaryImage)
                <img src="{{ Storage::url($product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                        {{ $product->name }}
                    </a>
                </h3>

                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($product->short_description, 100) }}</p>

                <!-- Price and Add to Cart -->
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        @if($product->on_sale)
                        <span class="text-lg font-bold text-red-600">
                            ${{ number_format($product->sale_price, 2) }}
                        </span>
                        <span class="text-sm text-gray-400 line-through ml-2">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        @else
                        <span class="text-lg font-bold text-gray-900">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        @endif
                    </div>

                    @if($product->inStock())
                    <livewire:cart.add-to-cart :product="$product" :key="'product-'.$product->id" button-class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" />
                    @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-600 rounded cursor-not-allowed">
                        Out of Stock
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-600">Try adjusting your search or filter criteria.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
