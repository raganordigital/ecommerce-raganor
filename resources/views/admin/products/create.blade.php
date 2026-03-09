@extends('admin.layouts.admin')

@section('title', 'Create Product')

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Create Product</h1>
            <p class="text-sm text-gray-500 mt-0.5">Add a new product to your store</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:border-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Products
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
            </div>
            <ul class="space-y-1 ml-6">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700 list-disc">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="productForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column (main fields) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Basic Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Basic Information</h2>

                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Wireless Headphones Pro"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('name') border-red-400 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU --}}
                        <div>
                            <label for="sku" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sku" id="sku"
                                   value="{{ old('sku') }}"
                                   placeholder="e.g. WHP-001"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('sku') border-red-400 @enderror font-mono"
                                   required>
                            @error('sku')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-400">Unique identifier for this product</p>
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label for="short_description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Short Description
                            </label>
                            <textarea name="short_description" id="short_description" rows="2"
                                      placeholder="Brief summary shown in product cards..."
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none @error('short_description') border-red-400 @enderror">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Full Description --}}
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="7"
                                      placeholder="Full product details, features, specifications..."
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('description') border-red-400 @enderror"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Pricing</h2>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Regular Price --}}
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Regular Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                                <input type="number" name="price" id="price"
                                       value="{{ old('price') }}"
                                       step="0.01" min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('price') border-red-400 @enderror"
                                       required>
                            </div>
                            @error('price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sale Price --}}
                        <div>
                            <label for="sale_price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Sale Price
                                <span class="text-xs font-normal text-gray-400 ml-1">optional</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                                <input type="number" name="sale_price" id="sale_price"
                                       value="{{ old('sale_price') }}"
                                       step="0.01" min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            </div>
                            @error('sale_price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Sale Date Range --}}
                    <div id="saleDates" class="grid grid-cols-2 gap-4 mt-4 hidden">
                        <div>
                            <label for="sale_price_from" class="block text-sm font-semibold text-gray-700 mb-1.5">Sale Start Date</label>
                            <input type="datetime-local" name="sale_price_from" id="sale_price_from"
                                   value="{{ old('sale_price_from') }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                            @error('sale_price_from')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sale_price_to" class="block text-sm font-semibold text-gray-700 mb-1.5">Sale End Date</label>
                            <input type="datetime-local" name="sale_price_to" id="sale_price_to"
                                   value="{{ old('sale_price_to') }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                            @error('sale_price_to')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Product Images</h2>

                    <label for="images"
                           class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors"
                           id="dropzone">
                        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-600">Click to upload images</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP · Max 2MB each · Up to 5 images</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                    </label>

                    <div id="imagePreview" class="grid grid-cols-5 gap-3 mt-4"></div>

                    @error('images')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">SEO</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-1.5">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title"
                                   value="{{ old('meta_title') }}"
                                   placeholder="Leave blank to use product name"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-1.5">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2"
                                      placeholder="Brief description for search engines..."
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none">{{ old('meta_description') }}</textarea>
                        </div>
                        <div>
                            <label for="meta_keywords" class="block text-sm font-semibold text-gray-700 mb-1.5">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords"
                                   value="{{ old('meta_keywords') }}"
                                   placeholder="keyword1, keyword2, keyword3"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (sidebar) --}}
            <div class="space-y-5">

                {{-- Publish --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Publish</h2>

                    <div class="space-y-3">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Active</p>
                                <p class="text-xs text-gray-400">Visible to customers</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1"
                                       id="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-gray-900 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>

                        <div class="border-t border-gray-100 pt-3">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Featured</p>
                                    <p class="text-xs text-gray-400">Show on homepage</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" name="is_featured" value="1"
                                           id="is_featured"
                                           {{ old('is_featured') ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-10 h-6 bg-gray-200 peer-checked:bg-amber-400 rounded-full transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-5 space-y-2">
                        <button type="submit"
                                class="w-full py-3 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition-colors">
                            Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                           class="block w-full text-center py-3 text-sm font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-4">Categories</h2>

                    <div class="space-y-2 max-h-56 overflow-y-auto">
                        @foreach($categories as $category)
                        <label class="flex items-center gap-2.5 py-1 cursor-pointer group">
                            <input type="checkbox"
                                   name="categories[]"
                                   value="{{ $category->id }}"
                                   {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Inventory --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Inventory</h2>

                    <div class="space-y-4">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Track Stock</p>
                                <p class="text-xs text-gray-400">Manage quantity</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" name="manage_stock" value="1"
                                       id="manage_stock"
                                       {{ old('manage_stock', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-gray-900 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>

                        <div id="stockQuantityField" class="{{ old('manage_stock', true) ? '' : 'hidden' }}">
                            <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock_quantity" id="stock_quantity"
                                   value="{{ old('stock_quantity', 0) }}"
                                   min="0"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                            @error('stock_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Sale dates toggle
    const salePriceInput = document.getElementById('sale_price');
    const saleDates = document.getElementById('saleDates');

    function toggleSaleDates() {
        if (salePriceInput.value && parseFloat(salePriceInput.value) > 0) {
            saleDates.classList.remove('hidden');
        } else {
            saleDates.classList.add('hidden');
        }
    }
    salePriceInput.addEventListener('input', toggleSaleDates);
    toggleSaleDates();

    // Manage stock toggle
    const manageStock = document.getElementById('manage_stock');
    const stockField  = document.getElementById('stockQuantityField');
    manageStock.addEventListener('change', function () {
        stockField.classList.toggle('hidden', !this.checked);
    });

    // Image preview
    const imageInput   = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    const MAX_SIZE     = 2 * 1024 * 1024;
    const MAX_COUNT    = 5;

    imageInput.addEventListener('change', function () {
        imagePreview.innerHTML = '';

        if (this.files.length > MAX_COUNT) {
            alert(`Maximum ${MAX_COUNT} images allowed.`);
            this.value = '';
            return;
        }

        Array.from(this.files).forEach(file => {
            if (file.size > MAX_SIZE) {
                alert(`"${file.name}" exceeds 2MB limit.`);
                return;
            }
            if (!file.type.match('image.*')) return;

            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-xl border border-gray-100">
                    <button type="button"
                            onclick="this.closest('.relative').remove()"
                            class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center font-bold">
                        ×
                    </button>
                    <p class="text-xs text-gray-400 mt-1 truncate">${(file.size/1024).toFixed(0)}KB</p>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Scroll to first error
    const firstError = document.querySelector('.text-red-600');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush

@endsection