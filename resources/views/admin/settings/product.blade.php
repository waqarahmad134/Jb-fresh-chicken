@extends('layouts.admin')

@section('title', 'Product Settings - Admin Panel')

@section('content')
    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Product Settings</h1>

    <div class="max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.settings.product.update') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Product Card Style --}}
            <fieldset>
                <legend class="mb-3 text-lg font-medium">Product Card Style</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach (['style1', 'style2', 'style3'] as $style)
                        <label class="flex cursor-pointer flex-col items-center gap-3 rounded-lg border-2 p-4 transition-colors {{ old('product_card_style', $settings['product_card_style']) === $style ? 'border-primary bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600' }}">
                            <input
                                type="radio"
                                name="product_card_style"
                                value="{{ $style }}"
                                {{ old('product_card_style', $settings['product_card_style']) === $style ? 'checked' : '' }}
                                class="sr-only"
                            >
                            
                            {{-- Visual representation --}}
                            <div class="flex h-32 w-full items-center justify-center rounded-md bg-gray-100 dark:bg-gray-700">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-2 text-xs text-gray-500">Style {{ substr($style, -1) }}</p>
                                </div>
                            </div>
                            
                            <span class="text-center text-sm font-semibold">Style {{ substr($style, -1) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('product_card_style')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            {{-- Features --}}
            <fieldset class="border-t pt-6 dark:border-gray-700">
                <legend class="mb-3 text-lg font-medium">Features</legend>
                <div class="space-y-3">
                    <label class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="quick_view_enabled"
                            value="1"
                            {{ old('quick_view_enabled', $settings['quick_view_enabled']) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="text-sm">Enable Quick View</span>
                    </label>
                </div>
            </fieldset>

            {{-- Add to Cart Behavior --}}
            <fieldset class="border-t pt-6 dark:border-gray-700">
                <legend class="mb-3 text-lg font-medium">"Add to Cart" Behavior</legend>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="add_to_cart_behavior"
                            value="drawer"
                            {{ old('add_to_cart_behavior', $settings['add_to_cart_behavior']) === 'drawer' ? 'checked' : '' }}
                            class="h-4 w-4 border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="text-sm">Open cart drawer (slide-in panel)</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="add_to_cart_behavior"
                            value="page"
                            {{ old('add_to_cart_behavior', $settings['add_to_cart_behavior']) === 'page' ? 'checked' : '' }}
                            class="h-4 w-4 border-gray-300 text-primary focus:ring-primary"
                        >
                        <span class="text-sm">Navigate to cart page</span>
                    </label>
                </div>
                @error('add_to_cart_behavior')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <div class="border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                    Save Product Settings
                </button>
            </div>
        </form>
    </div>
@endsection

