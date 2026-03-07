@extends('layouts.app')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row items-center">
                        <div class="lg:w-1/2 mb-6 lg:mb-0">
                            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-4">
                                Discover Amazing Products
                            </h1>
                            <p class="text-lg text-gray-600 mb-6">
                                Shop the latest trends and find everything you need in one place.
                            </p>
                            <a href="{{ route('products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                Shop Now
                            </a>
                        </div>
                        <div class="lg:w-1/2">
                            <img src="https://via.placeholder.com/600x400/4F46E5/FFFFFF?text=E-Commerce+Store" alt="Hero Image" class="w-full h-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Section -->
            @if($categories->count() > 0)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Shop by Category</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="block">
                            <div class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center transition duration-300">
                                <div class="w-16 h-16 bg-blue-600 rounded-full mx-auto mb-2 flex items-center justify-center">
                                    <span class="text-white text-2xl">{{ substr($category->name, 0, 1) }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Featured Products Section -->
            @if($featuredProducts->count() > 0)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            View All →
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredProducts as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->thumbnail_path) }}" alt="{{ $product->primaryImage->alt_text ?? $product->name }}" class="w-full h-48 object-cover">
                                @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center mb-2">
                                    <span class="text-2xl font-bold text-gray-900">{{ $product->formatted_price }}</span>
                                    @if($product->on_sale)
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                @if($product->reviews_count > 0)
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++) <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-1">({{ $product->reviews_count }})</span>
                                </div>
                                @endif
                                <livewire:cart.add-to-cart :product="$product" :key="'featured-'.$product->id" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Latest Products Section -->
            @if($featuredProducts->count() > 0)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Latest Products</h2>
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            View All →
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredProducts->take(4) as $product)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-300">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->thumbnail_path) }}" alt="{{ $product->primaryImage->alt_text ?? $product->name }}" class="w-full h-48 object-cover">
                                @else
                                <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-500">No Image</span>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-blue-600">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center mb-2">
                                    <span class="text-2xl font-bold text-gray-900">{{ $product->formatted_price }}</span>
                                    @if($product->on_sale)
                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                @if($product->reviews_count > 0)
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++) <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-1">({{ $product->reviews_count }})</span>
                                </div>
                                @endif
                                <livewire:cart.add-to-cart :product="$product" :key="'latest-'.$product->id" />
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Promotional Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg shadow-xl mb-8">
                <div class="p-8 text-center">
                    <h2 class="text-3xl font-bold mb-4">Special Offer!</h2>
                    <p class="text-xl mb-6">Get 20% off on your first order. Use code: WELCOME20</p>
                    <a href="{{ route('products.index') }}" class="bg-white text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-300">
                        Shop Now
                    </a>
                </div>
            </div>

            <!-- Newsletter Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Stay Updated</h2>
                        <p class="text-gray-600 mb-6">Subscribe to our newsletter for the latest products and exclusive offers.</p>
                        <form class="max-w-md mx-auto flex gap-4">
                            <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
