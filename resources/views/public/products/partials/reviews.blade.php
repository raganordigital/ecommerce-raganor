@if($product->reviews->count() > 0)
    <div class="space-y-4">
        @foreach($product->reviews as $review)
            <div class="border-b border-gray-100 pb-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-gray-900">{{ $review->user->name }}</span>
                    <span class="text-xs text-gray-400">• {{ $review->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-gray-600">{{ $review->comment }}</p>
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500">No reviews yet.</p>
@endif