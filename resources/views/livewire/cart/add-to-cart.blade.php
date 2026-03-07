<div>
    @if($showQuantityInput)
        <div class="flex items-center space-x-2">
            <input type="number" 
                   wire:model="quantity" 
                   min="1" 
                   max="999"
                   class="w-20 px-2 py-1 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button wire:click="addToCart" 
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove>Add to Cart</span>
                <span wire:loading>Adding...</span>
            </button>
        </div>
        @error('quantity') 
            <span class="text-sm text-red-600">{{ $message }}</span> 
        @enderror
    @else
        <button wire:click="quickAdd" 
                wire:loading.attr="disabled"
                class="{{ $buttonClass }} disabled:opacity-50">
            <span wire:loading.remove>Add to Cart</span>
            <span wire:loading>Adding...</span>
        </button>
    @endif
</div>