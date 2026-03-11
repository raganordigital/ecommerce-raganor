@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-6 md:py-8">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-gray-700">Products</a>
        <span class="mx-2">/</span>
        @foreach($product->categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="hover:text-gray-700">{{ $category->name }}</a>
            @if(!$loop->last)<span class="mx-2">/</span>@endif
        @endforeach
    </nav>

    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
        <!-- Image Gallery (Left) -->
        <div class="lg:w-1/2">
            <div class="sticky top-24">
                <!-- Main Image Slider -->
                <div class="relative overflow-hidden rounded-2xl bg-gray-50 border border-gray-100 shadow-sm mb-4">
                    <div class="swiper main-swiper">
                        <div class="swiper-wrapper">
                            @forelse($product->images as $image)
                                <div class="swiper-slide">
                                    <div class="relative pt-[100%]">
                                        <img src="{{ Storage::url($image->path) }}"
                                             alt="{{ $image->alt_text ?? $product->name }}"
                                             class="absolute inset-0 w-full h-full object-cover">
                                    </div>
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <div class="pt-[100%] relative">
                                        <div class="absolute inset-0 flex items-center justify-center text-gray-300">
                                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <!-- Navigation Buttons -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>

                <!-- Thumbnail Slider -->
                @if($product->images->count() > 1)
                <div class="swiper thumb-swiper">
                    <div class="swiper-wrapper">
                        @foreach($product->images as $image)
                            <div class="swiper-slide cursor-pointer">
                                <div class="relative pt-[100%] rounded-lg overflow-hidden border-2 border-transparent transition hover:border-blue-500">
                                    <img src="{{ Storage::url($image->thumbnail_path) }}"
                                         alt="{{ $image->alt_text ?? $product->name }}"
                                         class="absolute inset-0 w-full h-full object-cover">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info (Right) -->
        <div class="lg:w-1/2">
            <div class="mb-4">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
            </div>

            <!-- Rating -->
            @if($product->reviews_count > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-600">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} reviews)</span>
            </div>
            @endif

            <!-- Price -->
            <div class="mb-6">
                @if($product->on_sale)
                    <div class="flex items-center gap-3">
                        <span class="text-4xl font-bold text-red-600">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-xl text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                        @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-lg">-{{ $discount }}%</span>
                    </div>
                @else
                    <span class="text-4xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            <!-- Short Description -->
            @if($product->short_description)
                <div class="prose prose-sm text-gray-600 mb-6">
                    {{ $product->short_description }}
                </div>
            @endif

            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->inStock())
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-green-600 font-medium">In Stock</span>
                        @if($product->manage_stock && $product->stock_quantity <= 10)
                            <span class="text-sm text-amber-600">(Only {{ $product->stock_quantity }} left)</span>
                        @endif
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-red-600 font-medium">Out of Stock</span>
                    </div>
                @endif
            </div>

            <!-- Shipping & Tax Info (Cards) -->
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-gray-50 p-3 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Shipping</div>
                    <div class="font-medium">
                        @if($product->free_shipping)
                            <span class="text-green-600">Free</span>
                        @elseif($product->shipping_cost > 0)
                            ${{ number_format($product->shipping_cost, 2) }}
                        @else
                            Calculated
                        @endif
                    </div>
                </div>
                @if($product->tax_rate)
                <div class="bg-gray-50 p-3 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Tax</div>
                    <div class="font-medium">{{ $product->tax_rate }}%</div>
                </div>
                @endif
                <div class="bg-gray-50 p-3 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">Payment</div>
                    <div class="font-medium">
                        @if($product->allow_cod)
                            <span class="text-green-600">COD Available</span>
                        @else
                            <span class="text-gray-500">Card Only</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quantity & Add to Cart (Livewire) -->
            <div class="mb-8">
                @livewire('cart.add-to-cart', [
                    'product' => $product,
                    'showQuantityInput' => true,
                    'buttonClass' => 'bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl'
                ], key('add-to-cart-'.$product->id))
            </div>

            <!-- Categories -->
            @if($product->categories->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Categories:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Full Description & Reviews Tabs -->
    <div class="mt-16">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8">
                <button class="tab-link active py-4 px-1 border-b-2 border-blue-600 text-blue-600 font-medium text-sm" data-tab="description">Description</button>
                <button class="tab-link py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" data-tab="reviews">Reviews ({{ $product->reviews_count }})</button>
            </nav>
        </div>

        <div id="description" class="tab-content block mt-8">
            <div class="prose max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <div id="reviews" class="tab-content hidden mt-8">
            @include('public.products.partials.reviews', ['product' => $product])
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">You May Also Like</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($relatedProducts as $related)
                @include('public.products.partials.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Mobile Sticky Add to Cart (visible on small screens) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-10">
    <div class="container mx-auto flex items-center gap-3">
        <div class="flex-1">
            <span class="text-xl font-bold {{ $product->on_sale ? 'text-red-600' : 'text-gray-900' }}">
                ${{ number_format($product->current_price, 2) }}
            </span>
            @if($product->on_sale)
                <span class="text-sm text-gray-400 line-through ml-2">${{ number_format($product->price, 2) }}</span>
            @endif
        </div>
        <button id="stickyAddToCart" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Add
        </button>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper-button-next, .swiper-button-prev {
        color: #374151;
        background: rgba(255,255,255,0.9);
        width: 40px;
        height: 40px;
        border-radius: 9999px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        opacity: 0;
        transition: opacity 0.2s;
    }
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 18px;
    }
    .swiper-button-next:hover, .swiper-button-prev:hover {
        background: white;
    }
    .main-swiper:hover .swiper-button-next,
    .main-swiper:hover .swiper-button-prev {
        opacity: 1;
    }
    .thumb-swiper .swiper-slide {
        opacity: 0.6;
        transition: opacity 0.2s, border-color 0.2s;
    }
    .thumb-swiper .swiper-slide-thumb-active {
        opacity: 1;
    }
    .thumb-swiper .swiper-slide-thumb-active .border-2 {
        border-color: #3b82f6;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper thumbnails first if they exist
        const thumbSwiperEl = document.querySelector('.thumb-swiper');
        let thumbSwiper = null;
        if (thumbSwiperEl) {
            thumbSwiper = new Swiper(thumbSwiperEl, {
                slidesPerView: 4,
                spaceBetween: 10,
                watchSlidesProgress: true,
            });
        }

        // Initialize main swiper
        const mainSwiper = new Swiper('.main-swiper', {
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: thumbSwiper ? {
                swiper: thumbSwiper
            } : undefined,
        });

        // Tabs
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabContents = document.querySelectorAll('.tab-content');
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.dataset.tab;
                tabLinks.forEach(l => {
                    l.classList.remove('active', 'border-blue-600', 'text-blue-600');
                    l.classList.add('border-transparent', 'text-gray-500');
                });
                this.classList.add('active', 'border-blue-600', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                tabContents.forEach(content => {
                    if (content.id === tabId) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });
        });

        // Sticky add to cart - trigger the Livewire button's click
        const stickyBtn = document.getElementById('stickyAddToCart');
        if (stickyBtn) {
            stickyBtn.addEventListener('click', function() {
                // Find the add-to-cart button inside the Livewire component
                const addBtn = document.querySelector('[wire\\:click="addToCart"]');
                if (addBtn) {
                    addBtn.click();
                }
            });
        }
    });
</script>
@endpush