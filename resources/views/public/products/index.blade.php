@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Our Products</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-1/4">
            @include('public.products.partials.filters')
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                    @include('public.products.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection