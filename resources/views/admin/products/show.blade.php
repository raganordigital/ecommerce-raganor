@extends('admin.layouts.admin')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $product->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.products.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Product Details</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">SKU</p>
                        <p class="font-medium">{{ $product->sku }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Price</p>
                        <p class="font-medium">
                            @if($product->on_sale)
                                <span class="line-through text-gray-400">${{ number_format($product->price, 2) }}</span>
                                <span class="text-red-600 ml-2">${{ number_format($product->sale_price, 2) }}</span>
                            @else
                                ${{ number_format($product->price, 2) }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="font-medium">
                            @if($product->manage_stock)
                                {{ $product->stock_quantity }} units
                            @else
                                Not managed
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($product->is_featured)
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Featured
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Short Description</p>
                    <p class="text-gray-700">{{ $product->short_description ?: 'No short description' }}</p>
                </div>
                
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Full Description</p>
                    <div class="text-gray-700 prose max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Categories</h2>
                
                @if($product->categories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->categories as $category)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No categories assigned</p>
                @endif
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Images -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Images</h2>
                
                @if($product->images->count() > 0)
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative {{ $image->is_primary ? 'ring-2 ring-blue-500 rounded-lg' : '' }}">
                                <img src="{{ Storage::url($image->thumbnail_path) }}" 
                                     alt="{{ $image->alt_text ?: $product->name }}"
                                     class="w-full h-24 object-cover rounded-lg">
                                @if($image->is_primary)
                                    <span class="absolute top-0 right-0 bg-blue-500 text-white text-xs px-1 rounded-bl">
                                        Primary
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No images uploaded</p>
                @endif
            </div>
            
            <!-- Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Statistics</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Views</span>
                        <span class="font-medium">{{ number_format($product->views_count) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sales</span>
                        <span class="font-medium">{{ number_format($product->sales_count) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="font-medium">{{ $product->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Updated</span>
                        <span class="font-medium">{{ $product->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection