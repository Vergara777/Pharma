<div class="flex items-center justify-center p-2">
    @php
        $state = $getState();
        $image = $state ?? null;
        $name = $getRecord() ? $getRecord()->name : ($getContainer()->getParentComponent()->getState()['product_name'] ?? '');
    @endphp

    @if($image)
        <img src="{{ str_starts_with($image, 'http') ? $image : \Storage::disk('public')->url($image) }}" 
             alt="Product" 
             class="h-24 w-24 object-cover rounded-lg shadow-md">
    @else
         <div class="h-24 w-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-gray-400">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
    @endif
</div>
