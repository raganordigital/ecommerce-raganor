@extends('admin.layouts.admin')

@section('title', 'Edit: ' . $product->name)

@section('content')

<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">Edit Product</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $product->sku }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('products.show', $product->slug) }}" target="_blank"
               class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                View Live
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl hover:border-gray-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <form action="{{ route('admin.products.update', $product) }}"
          method="POST"
          enctype="multipart/form-data"
          id="productForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Basic Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Basic Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $product->name) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 @error('name') border-red-400 @enderror"
                                   required>
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                SKU <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="sku" id="sku"
                                   value="{{ old('sku', $product->sku) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 font-mono @error('sku') border-red-400 @enderror"
                                   required>
                            @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Slug
                            </label>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $product->slug) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 font-mono @error('slug') border-red-400 @enderror">
                            @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">URL: /products/<span id="slugPreview" class="font-semibold">{{ $product->slug }}</span></p>
                        </div>

                        <div>
                            <label for="short_description" class="block text-sm font-semibold text-gray-700 mb-1.5">Short Description</label>
                            <textarea name="short_description" id="short_description" rows="2"
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none @error('short_description') border-red-400 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                            @error('short_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Full Description <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="description" rows="7"
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 @error('description') border-red-400 @enderror"
                                      required>{{ old('description', $product->description) }}</textarea>
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">Pricing</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Regular Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input type="number" name="price" id="price"
                                       value="{{ old('price', $product->price) }}"
                                       step="0.01" min="0"
                                       class="w-full pl-8 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 @error('price') border-red-400 @enderror"
                                       required>
                            </div>
                            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="sale_price" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Sale Price <span class="text-xs font-normal text-gray-400 ml-1">optional</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input type="number" name="sale_price" id="sale_price"
                                       value="{{ old('sale_price', $product->sale_price) }}"
                                       step="0.01" min="0"
                                       class="w-full pl-8 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                            </div>
                            @error('sale_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div id="saleDates" class="grid grid-cols-2 gap-4 mt-4 {{ old('sale_price', $product->sale_price) ? '' : 'hidden' }}">
                        <div>
                            <label for="sale_price_from" class="block text-sm font-semibold text-gray-700 mb-1.5">Sale Start</label>
                            <input type="datetime-local" name="sale_price_from" id="sale_price_from"
                                   value="{{ old('sale_price_from', $product->sale_price_from?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                        <div>
                            <label for="sale_price_to" class="block text-sm font-semibold text-gray-700 mb-1.5">Sale End</label>
                            <input type="datetime-local" name="sale_price_to" id="sale_price_to"
                                   value="{{ old('sale_price_to', $product->sale_price_to?->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                    </div>
                </div>

                {{-- Existing Images --}}
                @if($product->images->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">
                        Current Images
                        <span class="ml-2 text-gray-300 font-normal normal-case text-xs">{{ $product->images->count() }} image(s)</span>
                    </h2>

                    <div class="grid grid-cols-5 gap-3">
                        @foreach($product->images as $image)
                        <div class="relative group" id="image-{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="{{ $image->alt_text ?? $product->name }}"
                                 class="w-full aspect-square object-cover rounded-xl border border-gray-100">

                            {{-- Primary badge --}}
                            @if($image->is_primary ?? $loop->first)
                                <span class="absolute bottom-1 left-1 bg-amber-400 text-gray-900 text-xs font-bold px-1.5 py-0.5 rounded-lg">
                                    Primary
                                </span>
                            @endif

                            {{-- Delete button --}}
                            <button type="button"
                                    onclick="deleteImage({{ $image->id }}, this)"
                                    class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center font-bold hover:bg-red-600">
                                ×
                            </button>
                        </div>
                        @endforeach
                    </div>

                    {{-- Hidden inputs to track deleted images --}}
                    <div id="deletedImages"></div>
                </div>
                @endif

                {{-- Upload New Images --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-5">
                        Add New Images
                        <span class="text-gray-300 font-normal normal-case text-xs ml-2">These will be added to existing ones</span>
                    </h2>

                    <label for="images"
                           class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition-colors">
                        <svg class="w-7 h-7 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-500">Click to upload new images</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP · Max 2MB each</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                    </label>

                    <div id="imagePreview" class="grid grid-cols-5 gap-3 mt-4"></div>

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
                                   value="{{ old('meta_title', $product->meta_title) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-1.5">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2"
                                      class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>
                        <div>
                            <label for="meta_keywords" class="block text-sm font-semibold text-gray-700 mb-1.5">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords"
                                   value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                   placeholder="keyword1, keyword2"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
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
                                <input type="checkbox" name="is_active" value="1" id="is_active"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}
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
                                    <input type="checkbox" name="is_featured" value="1" id="is_featured"
                                           {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
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
                            Save Changes
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                           class="block w-full text-center py-3 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            Cancel
                        </a>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full py-2.5 text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                                Delete Product
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="text-sm font-bold tracking-widest uppercase text-gray-400 mb-4">Stats</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Views</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($product->views_count) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Sales</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($product->sales_count) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Created</span>
                            <span class="text-sm font-medium text-gray-700">{{ $product->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Updated</span>
                            <span class="text-sm font-medium text-gray-700">{{ $product->updated_at->diffForHumans() }}</span>
                        </div>
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
                                   {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="text-sm text-gray-700 group-hover:text-gray-900">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
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
                                <input type="checkbox" name="manage_stock" value="1" id="manage_stock"
                                       {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-gray-900 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            </div>
                        </label>

                        <div id="stockQuantityField" class="{{ old('manage_stock', $product->manage_stock) ? '' : 'hidden' }}">
                            <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Stock Quantity
                            </label>
                            <input type="number" name="stock_quantity" id="stock_quantity"
                                   value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                   min="0"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900">
                            @error('stock_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Stock status indicator --}}
                            @if($product->manage_stock)
                            <div class="mt-2 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-500' : ($product->stock_quantity > 0 ? 'bg-amber-400' : 'bg-red-500') }}"></span>
                                <span class="text-xs text-gray-500">
                                    {{ $product->stock_quantity > 10 ? 'In stock' : ($product->stock_quantity > 0 ? 'Low stock' : 'Out of stock') }}
                                    — {{ $product->stock_quantity }} units
                                </span>
                            </div>
                            @endif
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

    // Slug preview
    const slugInput   = document.getElementById('slug');
    const slugPreview = document.getElementById('slugPreview');
    if (slugInput && slugPreview) {
        slugInput.addEventListener('input', function () {
            slugPreview.textContent = this.value;
        });
    }

    // Sale dates toggle
    const salePriceInput = document.getElementById('sale_price');
    const saleDates      = document.getElementById('saleDates');
    function toggleSaleDates() {
        if (salePriceInput.value && parseFloat(salePriceInput.value) > 0) {
            saleDates.classList.remove('hidden');
        } else {
            saleDates.classList.add('hidden');
        }
    }
    salePriceInput.addEventListener('input', toggleSaleDates);

    // Manage stock toggle
    const manageStock = document.getElementById('manage_stock');
    const stockField  = document.getElementById('stockQuantityField');
    manageStock.addEventListener('change', function () {
        stockField.classList.toggle('hidden', !this.checked);
    });

    // New image preview
    const imageInput   = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function () {
        imagePreview.innerHTML = '';
        Array.from(this.files).forEach(file => {
            if (file.size > 2 * 1024 * 1024) {
                alert(`"${file.name}" exceeds 2MB limit.`);
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover rounded-xl border border-gray-100">
                    <div class="absolute top-1 right-1 bg-green-500 text-white text-xs rounded-lg px-1.5 py-0.5 font-bold">New</div>
                    <p class="text-xs text-gray-400 mt-1">${(file.size/1024).toFixed(0)}KB</p>
                `;
                imagePreview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // First error scroll
    const firstError = document.querySelector('.text-red-600');
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
});

// Delete existing image via AJAX
function deleteImage(imageId, button) {
    if (!confirm('Remove this image?')) return;

    fetch(`/admin/products/{{ $product->id }}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success ?? true) {
            document.getElementById(`image-${imageId}`).remove();
        }
    })
    .catch(() => {
        // Fallback: mark for deletion on form submit
        const deletedImages = document.getElementById('deletedImages');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_images[]';
        input.value = imageId;
        deletedImages.appendChild(input);
        button.closest('[id^="image-"]').remove();
    });
}
</script>
@endpush

@endsection