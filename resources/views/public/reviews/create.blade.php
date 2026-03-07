@extends('layouts.app')

@section('title', 'Write a Review - ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h1 class="text-2xl font-bold text-gray-900">Write a Review</h1>
                <p class="text-gray-600">for {{ $product->name }}</p>
            </div>

            @if($existingReview)
                <div class="p-6">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        You have already reviewed this product. You can only submit one review per product.
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('products.show', $product) }}" 
                           class="text-blue-600 hover:text-blue-800">
                            Back to Product
                        </a>
                    </div>
                </div>
            @else
                <form action="{{ route('reviews.store', $product) }}" method="POST" class="p-6">
                    @csrf

                    <!-- Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Rating <span class="text-red-600">*</span>
                        </label>
                        <div class="flex items-center space-x-2" x-data="{ rating: {{ old('rating', 0) }} }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="rating = {{ $i }}"
                                        class="focus:outline-none">
                                    <svg class="w-8 h-8 {{ $i <= old('rating', 0) ? 'text-yellow-400' : 'text-gray-300' }}"
                                         :class="{ 'text-yellow-400': rating >= {{ $i }}, 'text-gray-300': rating < {{ $i }} }"
                                         fill="currentColor"
                                         viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating" value="{{ old('rating') }}">
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Review Title -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Review Title
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title') }}"
                               placeholder="Summarize your experience"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Review Comment -->
                    <div class="mb-6">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Review
                        </label>
                        <textarea name="comment" 
                                  id="comment" 
                                  rows="5"
                                  placeholder="What did you like or dislike? What was your experience with this product?"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($hasPurchased)
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded">
                            <p class="text-green-700">
                                <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                You've purchased this product. Your review will be marked as "Verified Purchase".
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <a href="{{ route('products.show', $product) }}" 
                           class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700">
                            Submit Review
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection