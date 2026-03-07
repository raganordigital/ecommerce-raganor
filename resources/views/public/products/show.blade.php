@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Product Images -->
        <div class="md:w-1/2">
            @if($product->primaryImage)
            <img src="{{ Storage::url($product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-lg">
            @else
            <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                <span class="text-gray-400">No image available</span>
            </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="md:w-1/2">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

            <!-- Price -->
            <div class="mb-6">
                @if($product->on_sale)
                <span class="text-3xl font-bold text-red-600">
                    ${{ number_format($product->sale_price, 2) }}
                </span>
                <span class="text-xl text-gray-400 line-through ml-2">
                    ${{ number_format($product->price, 2) }}
                </span>
                @else
                <span class="text-3xl font-bold text-gray-900">
                    ${{ number_format($product->price, 2) }}
                </span>
                @endif
            </div>

            <!-- Description -->
            <div class="prose max-w-none mb-6">
                {{ $product->description }}
            </div>

            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->inStock())
                <span class="text-green-600 font-semibold">✓ In Stock</span>
                @else
                <span class="text-red-600 font-semibold">✗ Out of Stock</span>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4 mb-6">
                @if($product->inStock())
                <livewire:cart.add-to-cart :product="$product" :show-quantity-input="true" button-class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700" />
                @endif

                <livewire:wishlist.wishlist-button :product="$product" />
            </div>

            <!-- Product Meta -->
            <div class="border-t pt-6">
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">SKU:</span> {{ $product->sku }}
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-semibold">Categories:</span>
                    @foreach($product->categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $category->name }}@if(!$loop->last), @endif
                    </a>
                    @endforeach
                </p>
            </div>

            <!-- Product Reviews Component -->
            @include('public.reviews.product-reviews', ['product' => $product])
        </div>
    </div>
</div>
@endsection
