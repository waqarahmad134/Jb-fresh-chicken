@extends('layouts.app')

@section('title', 'Shop - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Browse our menu of delicious chicken dishes.')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile filter toggle
        const filterBtn = document.getElementById('filter-toggle');
        const sidebar = document.getElementById('filter-sidebar');
        const closeSidebar = document.getElementById('close-sidebar');
        
        if (filterBtn && sidebar) {
            filterBtn.addEventListener('click', () => {
                sidebar.classList.remove('hidden');
            });
        }
        
        if (closeSidebar && sidebar) {
            closeSidebar.addEventListener('click', () => {
                sidebar.classList.add('hidden');
            });
        }

        // Update price display
        const priceInput = document.getElementById('price-filter');
        const priceDisplay = document.getElementById('price-display');
        if (priceInput && priceDisplay) {
            priceInput.addEventListener('input', (e) => {
                priceDisplay.textContent = `PKR ${e.target.value}`;
            });
        }

        // View toggle (Grid/List)
        document.querySelectorAll('[data-view-toggle]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const view = button.getAttribute('data-view-toggle');
                const url = new URL(window.location.href);
                url.searchParams.set('view', view);
                // Preserve other query parameters
                window.location.href = url.toString();
            });
        });
    });
</script>
@endpush

@section('content')
    <div class="flex flex-col gap-8 lg:flex-row">
        {{-- Mobile Filter Button --}}
        <div class="flex items-center justify-between lg:hidden">
            <h1 class="text-2xl font-extrabold text-secondary">Our Menu</h1>
            <button 
                id="filter-toggle"
                class="flex items-center gap-2 rounded-md border border-gray-300 bg-white p-2 dark:border-gray-600 dark:bg-gray-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                </svg>
                <span>Filters</span>
            </button>
        </div>

        {{-- Filter Sidebar --}}
        <aside 
            id="filter-sidebar"
            class="fixed inset-0 z-50 hidden w-full overflow-y-auto bg-white p-6 dark:bg-gray-800 lg:relative lg:block lg:w-72 lg:flex-shrink-0 lg:rounded-lg lg:border lg:border-gray-200 lg:shadow-md lg:dark:border-gray-700"
        >
            <div class="flex items-center justify-between lg:hidden">
                <h2 class="text-xl font-bold">Filters</h2>
                <button id="close-sidebar" class="text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="GET" action="{{ route('shop.index') }}" class="space-y-6">
                {{-- Category Filter --}}
                <div>
                    <h3 class="mb-3 font-bold text-dark dark:text-light">Category</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="category" value="all" {{ request('category', 'all') === 'all' ? 'checked' : '' }} class="mr-2">
                            <span>All</span>
                        </label>
                        @foreach ($categories as $category)
                            <label class="flex items-center">
                                <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} class="mr-2">
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Price Filter --}}
                <div>
                    <h3 class="mb-3 font-bold text-dark dark:text-light">
                        Price: <span id="price-display">PKR {{ request('price', $maxPriceAvailable) }}</span>
                    </h3>
                    <input 
                        type="range" 
                        id="price-filter"
                        name="price" 
                        min="0" 
                        max="{{ $maxPriceAvailable }}" 
                        value="{{ request('price', $maxPriceAvailable) }}" 
                        class="w-full"
                    >
                </div>

                {{-- Sort By --}}
                <div>
                    <h3 class="mb-3 font-bold text-dark dark:text-light">Sort By</h3>
                    <select name="sort" class="w-full rounded-md border border-gray-300 bg-light px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                        <option value="default" {{ request('sort') === 'default' ? 'selected' : '' }}>Default</option>
                        <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price-desc" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name-asc" {{ request('sort') === 'name-asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="name-desc" {{ request('sort') === 'name-desc' ? 'selected' : '' }}>Name: Z to A</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-md bg-primary px-4 py-2 font-bold text-white hover:bg-secondary">
                        Apply
                    </button>
                    <a href="{{ route('shop.index') }}" class="flex-1 rounded-md border border-gray-300 px-4 py-2 text-center font-bold hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700">
                        Reset
                    </a>
                </div>
            </form>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1">
            <div class="mb-8 hidden rounded-lg border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800 lg:block">
                <h1 class="mb-2 text-center text-4xl font-extrabold text-secondary">Our Menu</h1>
                <p class="text-center text-gray-600 dark:text-gray-400">Discover your next crispy craving.</p>
            </div>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('shop.index') }}" class="relative mb-6">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if (request('price'))
                    <input type="hidden" name="price" value="{{ request('price') }}">
                @endif
                @if (request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search products..." 
                    value="{{ request('search') }}"
                    class="w-full rounded-md border border-gray-300 bg-light px-4 py-3 pl-10 focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </form>

            {{-- Product Count and Controls --}}
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing <span class="font-bold text-dark dark:text-light">{{ $products->count() }}</span> of <span class="font-bold text-dark dark:text-light">{{ $products->total() }}</span> products
                </p>
                <div class="flex items-center gap-4">
                    {{-- View Toggle (Grid/List) --}}
                    <div class="hidden items-center gap-1 rounded-md bg-gray-100 p-1 dark:bg-gray-700 sm:flex">
                        <button 
                            type="button"
                            data-view-toggle="grid"
                            class="view-toggle p-1.5 rounded transition {{ request('view', 'grid') === 'grid' ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }}"
                            aria-label="Grid View"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button 
                            type="button"
                            data-view-toggle="list"
                            class="view-toggle p-1.5 rounded transition {{ request('view') === 'list' ? 'bg-white dark:bg-gray-800 shadow-sm' : 'text-gray-500 dark:text-gray-400' }}"
                            aria-label="List View"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                    {{-- Sort Dropdown --}}
                    <form method="GET" action="{{ route('shop.index') }}" class="inline-block">
                        @if (request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if (request('price'))
                            <input type="hidden" name="price" value="{{ request('price') }}">
                        @endif
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select 
                            name="sort" 
                            onchange="this.form.submit()"
                            class="rounded-md border border-gray-300 bg-light px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                        >
                            <option value="default" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Default Sorting</option>
                            <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price-desc" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name-asc" {{ request('sort') === 'name-asc' ? 'selected' : '' }}>Name: A-Z</option>
                            <option value="name-desc" {{ request('sort') === 'name-desc' ? 'selected' : '' }}>Name: Z-A</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- Products Display --}}
            @if ($products->count() > 0)
                @php
                    $viewMode = request('view', 'grid');
                @endphp
                
                @if ($viewMode === 'list')
                    {{-- List View --}}
                    <div class="mb-8 flex flex-col gap-6">
                        @foreach ($products as $product)
                            <div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800 sm:flex-row">
                                <a 
                                    href="{{ route('product.show', $product->slug ?? $product->id) }}"
                                    class="flex-shrink-0"
                                >
                                    <img 
                                        src="{{ $product->image_url ? (str_starts_with($product->image_url, 'http') ? $product->image_url : asset('public'.$product->image_url)) : 'https://picsum.photos/id/10/200/200' }}" 
                                        alt="{{ $product->name }}" 
                                        class="h-32 w-full rounded-lg object-cover transition-transform hover:scale-105 sm:h-40 sm:w-40"
                                        loading="lazy"
                                    >
                                </a>
                                <div class="flex flex-1 flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                    <div class="flex-1">
                                        <a 
                                            href="{{ route('product.show', $product->slug ?? $product->id) }}"
                                            class="text-xl font-bold text-dark transition hover:text-primary dark:text-white"
                                        >
                                            {{ $product->name }}
                                        </a>
                                        @if ($product->category)
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->category->name }}
                                            </p>
                                        @endif
                                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                                            {{ \Illuminate\Support\Str::limit($product->description, 150) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-secondary">
                                                PKR {{ number_format($product->price, 2) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button 
                                                type="button"
                                                data-add-to-cart 
                                                data-product-id="{{ $product->id }}"
                                                class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Grid View --}}
                    <div class="mb-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($products as $product)
                            @include('components.product.card', ['product' => $product])
                        @endforeach
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <p class="py-20 text-center text-gray-600 dark:text-gray-400">No products found.</p>
            @endif
        </div>
    </div>
@endsection

