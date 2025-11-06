@extends('layouts.app')

@section('title', 'My Wishlist - ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    @if ($wishlistItems->count() === 0)
        <div class="py-20 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-6 h-24 w-24 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C4.099 3.75 2 5.765 2 8.25c0 7.22 9 12 10 12s10-4.78 10-12z" />
            </svg>
            <h1 class="mb-4 text-3xl font-bold">Your Wishlist is Empty</h1>
            <p class="mb-8 text-gray-600 dark:text-gray-400">
                You haven't added any favorites yet. Browse the shop to find something you'll love!
            </p>
            <a 
                href="{{ route('shop.index') }}" 
                class="inline-block rounded-full bg-primary px-6 py-3 font-bold text-white transition-colors hover:bg-secondary"
            >
                Go to Shop
            </a>
        </div>
    @else
        <div>
            <h1 class="mb-10 text-center text-4xl font-extrabold text-secondary">My Wishlist</h1>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($wishlistItems as $product)
                    @include('components.product.card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif
@endsection

