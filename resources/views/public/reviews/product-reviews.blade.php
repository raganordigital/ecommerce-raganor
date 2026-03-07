@props(['product'])

<div class="mt-12">
    <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>
    
    <!-- Rating Summary -->
    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <div class="flex items-center space-x-8">
            <div class="text-center">
                <div class="text-5xl font-bold text-gray-900">{{ number_format($product->average_rating, 1) }}</div>
                <div class="flex items-center justify-center mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <div class="text-sm text-gray-500 mt-1">Based on {{ $product->reviews_count }} reviews</div>
            </div>
            
            <!-- Rating Distribution -->
            <div class="flex-1">
                @foreach(range(5, 1) as $star)
                    @php
                        $count = $product->rating_distribution[$star] ?? 0;
                        $percentage = $product->reviews_count > 0 ? ($count / $product->reviews_count) * 100 : 0;
                    @endphp
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 w-8">{{ $star }} ★</span>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Write Review Button -->
    @auth
        <div class="mb-8 text-right">
            <a href="{{ route('reviews.create', $product) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Write a Review
            </a>
        </div>
    @endauth

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($product->approvedReviews()->with('user')->latest()->get() as $review)
            <div class="border-b pb-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-4">
                        <div class="font-semibold">{{ $review->user->name }}</div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($review->is_verified_purchase)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Verified Purchase
                            </span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $review->created_at->diffForHumans() }}
                    </div>
                </div>
                
                @if($review->title)
                    <h3 class="font-semibold mb-2">{{ $review->title }}</h3>
                @endif
                
                <p class="text-gray-700 mb-4">{{ $review->comment }}</p>
                
                <!-- Helpful/Unhelpful Buttons -->
                <div class="flex items-center space-x-4 text-sm">
                    <span class="text-gray-500">Was this review helpful?</span>
                    <form action="{{ route('reviews.helpful', $review) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-green-600">
                            Helpful ({{ $review->helpful_count }})
                        </button>
                    </form>
                    <form action="{{ route('reviews.unhelpful', $review) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600">
                            Not Helpful ({{ $review->unhelpful_count }})
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No reviews yet</h3>
                <p class="mt-1 text-sm text-gray-500">Be the first to review this product!</p>
            </div>
        @endforelse
    </div>
</div>