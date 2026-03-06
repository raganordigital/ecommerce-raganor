@extends('admin.layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create New Product</h1>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-lg shadow">
        @csrf

        <div class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="border-b pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Product Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                            SKU <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="sku" 
                               id="sku" 
                               value="{{ old('sku') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               required>
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Unique product identifier</p>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Regular Price <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" 
                                   name="price" 
                                   id="price" 
                                   value="{{ old('price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sale Price -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Sale Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" 
                                   name="sale_price" 
                                   id="sale_price" 
                                   value="{{ old('sale_price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        @error('sale_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sale Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4" id="saleDates" style="display: none;">
                    <div>
                        <label for="sale_price_from" class="block text-sm font-medium text-gray-700 mb-1">
                            Sale Start Date
                        </label>
                        <input type="datetime-local" 
                               name="sale_price_from" 
                               id="sale_price_from" 
                               value="{{ old('sale_price_from') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('sale_price_from')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sale_price_to" class="block text-sm font-medium text-gray-700 mb-1">
                            Sale End Date
                        </label>
                        <input type="datetime-local" 
                               name="sale_price_to" 
                               id="sale_price_to" 
                               value="{{ old('sale_price_to') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('sale_price_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Short Description -->
                <div class="mt-4">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Short Description
                    </label>
                    <textarea name="short_description" 
                              id="short_description" 
                              rows="2"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Description -->
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Full Description <span class="text-red-600">*</span>
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="6"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Categories -->
            <div class="border-b pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Categories</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($categories as $category)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" 
                               name="categories[]" 
                               value="{{ $category->id }}"
                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Inventory -->
            <div class="border-b pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Inventory</h2>
                
                <div class="space-y-4">
                    <!-- Manage Stock Checkbox -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="manage_stock" 
                               id="manage_stock" 
                               value="1"
                               {{ old('manage_stock', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="manage_stock" class="ml-2 text-sm text-gray-700">
                            Track stock quantity
                        </label>
                    </div>

                    <!-- Stock Quantity -->
                    <div id="stockQuantityField" class="{{ old('manage_stock', true) ? '' : 'hidden' }}">
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock Quantity <span class="text-red-600">*</span>
                        </label>
                        <input type="number" 
                               name="stock_quantity" 
                               id="stock_quantity" 
                               value="{{ old('stock_quantity', 0) }}"
                               min="0"
                               class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            <div class="border-b pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Product Images</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-center w-full">
                        <label for="images" class="w-full flex flex-col items-center px-4 py-6 bg-white rounded-lg border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-50">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                            <span class="mt-2 text-sm text-gray-500">Click to upload images</span>
                            <span class="text-xs text-gray-400">PNG, JPG, GIF, WEBP up to 2MB (Max 5 images)</span>
                        </label>
                        <input type="file" 
                               name="images[]" 
                               id="images" 
                               multiple 
                               accept="image/*"
                               class="hidden">
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4"></div>

                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- SEO -->
            <div class="border-b pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Title
                        </label>
                        <input type="text" 
                               name="meta_title" 
                               id="meta_title" 
                               value="{{ old('meta_title') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Description
                        </label>
                        <textarea name="meta_description" 
                                  id="meta_description" 
                                  rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Keywords
                        </label>
                        <input type="text" 
                               name="meta_keywords" 
                               id="meta_keywords" 
                               value="{{ old('meta_keywords') }}"
                               placeholder="keyword1, keyword2, keyword3"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('meta_keywords')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Status</h2>
                
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active (visible to customers)</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_featured" 
                               value="1"
                               {{ old('is_featured') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Featured product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Product
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide sale dates based on sale price
    const salePriceInput = document.getElementById('sale_price');
    const saleDatesDiv = document.getElementById('saleDates');
    
    function toggleSaleDates() {
        if (salePriceInput.value && parseFloat(salePriceInput.value) > 0) {
            saleDatesDiv.style.display = 'grid';
        } else {
            saleDatesDiv.style.display = 'none';
        }
    }
    
    salePriceInput.addEventListener('input', toggleSaleDates);
    toggleSaleDates();
    
    // Show/hide stock quantity based on manage stock checkbox
    const manageStockCheckbox = document.getElementById('manage_stock');
    const stockQuantityField = document.getElementById('stockQuantityField');
    
    manageStockCheckbox.addEventListener('change', function() {
        if (this.checked) {
            stockQuantityField.classList.remove('hidden');
        } else {
            stockQuantityField.classList.add('hidden');
        }
    });
    
    // Image preview
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative';
                previewDiv.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                    <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                imagePreview.appendChild(previewDiv);
            }
            
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush
@endsection