<button wire:click="openPdf({{ $id }})" class="cursor-pointer flex items-center justify-center w-8 h-8 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors group">
    <svg wire:loading.remove wire:target="openPdf({{ $id }})" class="w-6 h-6 text-red-500 group-hover:text-red-600 transition-colors" fill="none"
        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
        </path>
    </svg>
    <svg wire:loading wire:target="openPdf({{ $id }})" class="animate-spin w-6 h-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</button>
