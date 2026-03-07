@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Product Images -->
        <div class="lg:w-1/2">
            <div class="mb-4">
                @if($product->primaryImage)
                <img src="{{ Storage::url($product->primaryImage->path) }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-lg" id="main-image">
                @else
                <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400">No image available</span>
                </div>
                @endif
            </div>

            <!-- Image Gallery Thumbnails -->
            @if($product->images && $product->images->count() > 1)
            <div class="flex space-x-2 overflow-x-auto">
                @foreach($product->images as $image)
                <img src="{{ Storage::url($image->thumbnail_path) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded cursor-pointer border-2 border-gray-200 hover:border-blue-500" onclick="changeMainImage('{{ Storage::url($image->path) }}')">
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="lg:w-1/2">
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
                {!! $product->description !!}
            </div>

            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->inStock())
                <span class="text-green-600 font-semibold">✓ In Stock</span>
                @else
                <span class="text-red-600 font-semibold">✗ Out of Stock</span>
                @endif
            </div>

            <!-- Quantity Selector and Add to Cart -->
            @if($product->inStock())
            <div class="mb-6">
                <livewire:cart.add-to-cart :product="$product" :show-quantity-input="true" button-class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700" />
            </div>
            @endif

            <!-- Wishlist Button -->
            <div class="mb-6">
                <livewire:wishlist.wishlist-button :product="$product" />
            </div>

            <!-- Product Meta -->
            <div class="border-t pt-6">
                <p class="text-sm text-gray-600 mb-2">
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
        </div>
    </div>

    <!-- Product Reviews Component -->
    <div class="mt-12">
        @include('public.reviews.product-reviews', ['product' => $product])
    </div>

    <!-- Related Products -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($product->categories->first()->products->where('id', '!=', $product->id)->take(4) ?? collect() as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                    @if($relatedProduct->primaryImage)
                    <img src="{{ Storage::url($relatedProduct->primaryImage->path) }}" alt="{{ $relatedProduct->name }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-blue-600">
                            {{ $relatedProduct->name }}
                        </a>
                    </h3>
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-gray-900">
                            ${{ number_format($relatedProduct->price, 2) }}
                        </span>
                        @if($relatedProduct->inStock())
                        <livewire:cart.add-to-cart :product="$relatedProduct" :key="'related-'.$relatedProduct->id" button-class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" />
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-600">No related products found.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function changeMainImage(src) {
        document.getElementById('main-image').src = src;
    }

</script>
@endsection
