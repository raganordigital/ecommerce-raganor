<div>
    @if($showQuantityInput)
        <div class="flex items-center gap-2 mb-3">
            <input type="number"
                   wire:model="quantity"
                   min="1"
                   max="999"
                   class="w-20 px-2 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
            <button wire:click="addToCart"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors">
                <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                <span wire:loading wire:target="addToCart">Adding...</span>
            </button>
        </div>
        @error('quantity')
            <span class="text-xs text-red-600">{{ $message }}</span>
        @enderror
        <button wire:click="buyNow"
                wire:loading.attr="disabled"
                class="w-full py-2 border-2 border-gray-900 text-gray-900 text-sm font-semibold rounded-lg hover:bg-gray-900 hover:text-white disabled:opacity-50 transition-colors">
            <span wire:loading.remove wire:target="buyNow">Buy Now</span>
            <span wire:loading wire:target="buyNow">Redirecting...</span>
        </button>
    @else
        <div class="flex gap-2">
            <button wire:click="quickAdd"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-gray-700 disabled:opacity-50 transition-colors {{ $buttonClass }}">
                <span wire:loading.remove wire:target="quickAdd">Add to Cart</span>
                <span wire:loading wire:target="quickAdd">Adding...</span>
            </button>
            <button wire:click="buyNow"
                    wire:loading.attr="disabled"
                    class="flex-1 py-2.5 border-2 border-gray-900 text-gray-900 text-sm font-semibold rounded-xl hover:bg-gray-900 hover:text-white disabled:opacity-50 transition-colors">
                <span wire:loading.remove wire:target="buyNow">Buy Now</span>
                <span wire:loading wire:target="buyNow">Going...</span>
            </button>
        </div>
    @endif
</div>