<div id="quick-view-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="quick-view-title">
    <div class="relative flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-lg bg-white shadow-xl dark:bg-gray-800 md:flex-row animate-fade-in-up">
        <button 
            id="close-quick-view" 
            class="absolute right-3 top-3 z-10 rounded-full p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:hover:bg-gray-700"
            aria-label="Close modal"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        {{-- Loading State --}}
        <div id="quick-view-loading" class="flex w-full items-center justify-center p-12 md:w-1/2">
            <div class="flex flex-col items-center gap-4">
                <div class="h-12 w-12 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
                <p class="text-gray-600 dark:text-gray-400">Loading product...</p>
            </div>
        </div>

        {{-- Product Image --}}
        <div id="quick-view-image" class="hidden w-full md:w-1/2">
            <img id="quick-view-image-src" src="" alt="" class="h-full w-full object-cover rounded-t-lg md:rounded-l-lg md:rounded-t-none">
        </div>

        {{-- Product Content --}}
        <div id="quick-view-content" class="hidden w-full overflow-y-auto p-8 md:w-1/2">
            <h2 id="quick-view-title" class="mb-2 text-3xl font-extrabold text-secondary"></h2>
            <p id="quick-view-price" class="mb-4 text-3xl font-bold text-dark dark:text-light"></p>
            <p id="quick-view-description" class="mb-6 text-gray-600 dark:text-gray-400"></p>
            
            <div class="mb-6 flex items-center gap-4">
                <div class="flex items-center rounded-full border dark:border-gray-600">
                    <button 
                        id="quick-view-decrease" 
                        class="px-4 py-2 font-bold rounded-l-full hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Decrease quantity"
                    >-</button>
                    <span id="quick-view-quantity" class="px-5 py-2">1</span>
                    <button 
                        id="quick-view-increase" 
                        class="px-4 py-2 font-bold rounded-r-full hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Increase quantity"
                    >+</button>
                </div>
                <button 
                    id="quick-view-add-to-cart" 
                    class="flex-1 rounded-full bg-primary px-6 py-3 font-bold text-white transition-colors hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Add to Cart
                </button>
            </div>

            <a 
                id="quick-view-full-details" 
                href="#" 
                class="font-semibold text-primary transition-colors hover:underline"
            >
                View Full Product Details &rarr;
            </a>
        </div>
    </div>
</div>

