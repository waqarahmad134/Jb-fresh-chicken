@props(['product', 'placeholderImage' => 'https://picsum.photos/seed/jbfreshchicken/600/600'])

@php
    $productUrl = url('/product/' . ($product->slug ?? $product->id));
    $imageUrl = $product->image_url
        ?? ($product->image_urls[0] ?? $placeholderImage);
    $price = number_format((float) ($product->price ?? 0), 2);
    $compareAt = $product->compare_at_price ? number_format((float) $product->compare_at_price, 2) : null;
    $cardStyle = $siteSettings['product_card_style'] ?? 'style1';
    $quickViewEnabled = $siteSettings['quick_view_enabled'] ?? false;
    
    // Check if product is in wishlist
    $isInWishlist = false;
    if (auth()->check()) {
        $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->exists();
    }
    $productId = $product->id;
@endphp


@if ($cardStyle === 'style2')
    <article class="flex overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <a href="{{ $productUrl }}" class="relative block w-1/3 overflow-hidden">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" loading="lazy">
            @if ($quickViewEnabled)
                <button 
                    type="button"
                    data-quick-view 
                    data-product-id="{{ $productId }}"
                    class="absolute left-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="Quick view"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            @endif
            @auth
                <button 
                    type="button" 
                    data-wishlist-toggle 
                    data-product-id="{{ $productId }}"
                    class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-5 w-5 {{ $isInWishlist ? 'text-red-500 fill-current' : 'text-gray-600 dark:text-gray-400' }}" 
                         fill="{{ $isInWishlist ? 'currentColor' : 'none' }}" 
                         viewBox="0 0 24 24" 
                         stroke-width="1.5" 
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C4.099 3.75 2 5.765 2 8.25c0 7.22 9 12 10 12s10-4.78 10-12z" />
                    </svg>
                </button>
            @endauth
        </a>
        <div class="flex w-2/3 flex-col p-5">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'Category' }}</span>
            <a href="{{ $productUrl }}" class="mt-2 text-lg font-semibold text-dark transition-colors hover:text-primary dark:text-white">
                {{ $product->name }}
            </a>
            <a href="{{ $productUrl }}" class="mt-1 text-xs text-primary hover:underline">View Details →</a>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($product->description, 110) }}</p>
            <div class="mt-auto flex items-center justify-between pt-4">
                <div>
                    <p class="text-xl font-bold text-secondary">PKR {{ $price }}</p>
                    @if ($compareAt)
                        <p class="text-xs text-gray-500 line-through">PKR {{ $compareAt }}</p>
                    @endif
                </div>
                <button 
                    type="button" 
                    data-add-to-cart 
                    data-product-id="{{ $productId }}"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                </button>
            </div>
        </div>
    </article>
@elseif ($cardStyle === 'style3')
    <article class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl dark:border-gray-700 dark:bg-gray-800">
        <a href="{{ $productUrl }}" class="block">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-72 w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            @if ($quickViewEnabled)
                <button 
                    type="button"
                    data-quick-view 
                    data-product-id="{{ $productId }}"
                    class="absolute left-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="Quick view"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            @endif
            @auth
                <button 
                    type="button" 
                    data-wishlist-toggle 
                    data-product-id="{{ $productId }}"
                    class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-5 w-5 {{ $isInWishlist ? 'text-red-500 fill-current' : 'text-gray-600 dark:text-gray-400' }}" 
                         fill="{{ $isInWishlist ? 'currentColor' : 'none' }}" 
                         viewBox="0 0 24 24" 
                         stroke-width="1.5" 
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C4.099 3.75 2 5.765 2 8.25c0 7.22 9 12 10 12s10-4.78 10-12z" />
                    </svg>
                </button>
            @endauth
        </a>
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-5 text-white">
            <p class="text-xs uppercase tracking-widest text-white/70">{{ $product->category->name ?? 'Category' }}</p>
            <h3 class="mt-2 text-lg font-bold">{{ $product->name }}</h3>
            <a href="{{ $productUrl }}" class="mt-1 text-xs text-white/80 hover:text-white hover:underline">View Details →</a>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xl font-semibold text-primary">PKR {{ $price }}</span>
                <button 
                    type="button" 
                    data-add-to-cart 
                    data-product-id="{{ $productId }}"
                    class="inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-dark transition hover:bg-primary hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                </button>
            </div>
        </div>
    </article>
@else
    <article class="group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <a href="{{ $productUrl }}" class="relative block overflow-hidden">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
            @if ($quickViewEnabled)
                <button 
                    type="button"
                    data-quick-view 
                    data-product-id="{{ $productId }}"
                    class="absolute left-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="Quick view"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            @endif
            @auth
                <button 
                    type="button" 
                    data-wishlist-toggle 
                    data-product-id="{{ $productId }}"
                    class="absolute right-3 top-3 z-10 rounded-full bg-white/90 p-2 shadow-md transition-all hover:scale-110 hover:bg-white dark:bg-gray-900/90 dark:hover:bg-gray-900"
                    aria-label="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" 
                         class="h-5 w-5 {{ $isInWishlist ? 'text-red-500 fill-current' : 'text-gray-600 dark:text-gray-400' }}" 
                         fill="{{ $isInWishlist ? 'currentColor' : 'none' }}" 
                         viewBox="0 0 24 24" 
                         stroke-width="1.5" 
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C4.099 3.75 2 5.765 2 8.25c0 7.22 9 12 10 12s10-4.78 10-12z" />
                    </svg>
                </button>
            @endauth
        </a>
        <div class="flex flex-1 flex-col p-5">
            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'Category' }}</span>
            <a href="{{ $productUrl }}" class="mt-2 flex-1 text-lg font-semibold text-dark transition-colors hover:text-primary dark:text-white">
                {{ $product->name }} 1
            </a>
            <a href="{{ $productUrl }}" class="mt-1 text-xs text-primary hover:underline">View Details →</a>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($product->description, 110) }}</p>
            <div class="mt-auto border-t border-gray-200 pt-4 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xl font-bold text-secondary">PKR {{ $price }}</p>
                        @if ($compareAt)
                            <p class="text-xs text-gray-500 line-through">PKR {{ $compareAt }}</p>
                        @endif
                    </div>
                    <button 
                        type="button" 
                        data-add-to-cart 
                        data-product-id="{{ $productId }}"
                        class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cart
                    </button>
                </div>
            </div>
        </div>
    </article>
@endif

