@extends('layouts.app')

@section('title', 'E-Shop — Quality Products, Great Prices')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gray-950 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-20"
         style="background-image: radial-gradient(circle at 20% 50%, #6366f1 0%, transparent 50%), radial-gradient(circle at 80% 20%, #f59e0b 0%, transparent 40%);">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="max-w-2xl">
            <span class="inline-block text-xs font-semibold tracking-widest text-amber-400 uppercase mb-4">
                New arrivals every week
            </span>
            <h1 class="text-5xl lg:text-7xl font-black tracking-tight leading-none mb-6">
                Find What<br>
                <span class="text-amber-400">You Love.</span>
            </h1>
            <p class="text-lg text-gray-300 mb-10 max-w-lg">
                Thousands of quality products, fast shipping, and prices you'll actually like. Start exploring today.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 bg-amber-400 text-gray-950 font-bold px-8 py-4 rounded-xl hover:bg-amber-300 transition-colors">
                    Shop Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 border border-white/30 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-colors">
                    Create Account
                </a>
                @endguest
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="relative border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-3 gap-8 max-w-lg">
                <div>
                    <p class="text-2xl font-black text-white">{{ $totalProducts ?? '100+' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Products</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-white">{{ $categories->count() }}+</p>
                    <p class="text-xs text-gray-400 mt-0.5">Categories</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-white">Free</p>
                    <p class="text-xs text-gray-400 mt-0.5">Shipping $50+</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Trust Bar --}}
<section class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'label' => 'Free Shipping', 'sub' => 'On orders over $50'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Secure Payment', 'sub' => '100% protected'],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'Easy Returns', 'sub' => '30-day policy'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'label' => '24/7 Support', 'sub' => 'Always here to help'],
            ] as $feature)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gray-900 rounded-lg flex-shrink-0 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $feature['label'] }}</p>
                    <p class="text-xs text-gray-500">{{ $feature['sub'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Categories --}}
@if($categories->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-bold tracking-widest text-amber-500 uppercase mb-2">Browse</p>
                <h2 class="text-3xl font-black text-gray-900">Shop by Category</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center gap-1">
                All products
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($categories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}"
               class="group flex flex-col items-center p-4 bg-gray-50 rounded-2xl hover:bg-gray-900 transition-all duration-200 text-center">
                <div class="w-12 h-12 bg-gray-200 group-hover:bg-amber-400 rounded-xl flex items-center justify-center mb-3 transition-colors">
                    <span class="text-xl font-black text-gray-700 group-hover:text-gray-900">
                        {{ strtoupper(substr($category->name, 0, 1)) }}
                    </span>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors leading-tight">
                    {{ $category->name }}
                </span>
                @if($category->products_count ?? false)
                <span class="text-xs text-gray-400 group-hover:text-gray-400 mt-0.5">
                    {{ $category->products_count }} items
                </span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Products --}}
@if($featuredProducts->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-bold tracking-widest text-amber-500 uppercase mb-2">Handpicked</p>
                <h2 class="text-3xl font-black text-gray-900">Featured Products</h2>
            </div>
            <a href="{{ route('products.index', ['featured' => 1]) }}"
               class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredProducts as $product)
            @include('public.products.product-card', ['product' => $product, 'keyPrefix' => 'featured'])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Promo Banner --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-950 rounded-3xl overflow-hidden relative">
            <div class="absolute inset-0 opacity-30"
                 style="background-image: radial-gradient(circle at 70% 50%, #f59e0b 0%, transparent 60%);">
            </div>
            <div class="relative px-8 py-14 md:px-16 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <span class="text-amber-400 text-sm font-bold tracking-widest uppercase">Limited Time</span>
                    <h2 class="text-4xl font-black text-white mt-2 mb-3">First Order? <br>Get 20% Off.</h2>
                    <p class="text-gray-400">Use code <span class="text-white font-mono font-bold bg-white/10 px-2 py-0.5 rounded">WELCOME20</span> at checkout.</p>
                </div>
                <a href="{{ route('products.index') }}"
                   class="flex-shrink-0 bg-amber-400 text-gray-950 font-bold px-8 py-4 rounded-xl hover:bg-amber-300 transition-colors text-sm">
                    Shop Now
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Latest Products --}}
@if($latestProducts->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <p class="text-xs font-bold tracking-widest text-amber-500 uppercase mb-2">Just In</p>
                <h2 class="text-3xl font-black text-gray-900">Latest Products</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'latest']) }}"
               class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center gap-1">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($latestProducts as $product)
            @include('public.products.product-card', ['product' => $product, 'keyPrefix' => 'latest'])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Newsletter --}}
<section class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto text-center">
            <p class="text-xs font-bold tracking-widest text-amber-500 uppercase mb-3">Stay in the loop</p>
            <h2 class="text-3xl font-black text-gray-900 mb-3">Get Exclusive Deals</h2>
            <p class="text-gray-500 mb-8">Subscribe and be the first to know about new products and special offers.</p>
            <form class="flex gap-2 max-w-md mx-auto">
                <input type="email"
                       placeholder="your@email.com"
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <button type="submit"
                        class="bg-gray-900 text-white font-semibold px-6 py-3 rounded-xl hover:bg-gray-700 transition-colors text-sm whitespace-nowrap">
                    Subscribe
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-3">No spam, ever. Unsubscribe anytime.</p>
        </div>
    </div>
</section>

@endsection