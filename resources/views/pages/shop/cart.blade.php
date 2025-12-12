@extends('layouts.app')

@section('title', 'Shopping Cart - ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    @if ($cartItems->count() === 0)
        <div class="py-20 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-6 h-24 w-24 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <h1 class="mb-4 text-3xl font-bold">Your Cart is Empty</h1>
            <p class="mb-8 text-gray-600 dark:text-gray-400">
                Looks like you haven't added anything to your cart yet.
            </p>
            <a 
                href="{{ route('shop.index') }}" 
                class="inline-block rounded-full bg-primary px-6 py-3 font-bold text-white transition-colors hover:bg-secondary"
            >
                Start Shopping
            </a>
        </div>
    @else
        <div class="mx-auto max-w-4xl rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <h1 class="mb-6 border-b pb-4 text-3xl font-extrabold text-secondary dark:border-gray-600">
                Your Cart ({{ $cartCount }} {{ $cartCount > 1 ? 'items' : 'item' }})
            </h1>
            
            <div class="space-y-4">
                @foreach ($cartItems as $item)
                    <div 
                        class="flex flex-col gap-4 rounded-md border p-4 dark:border-gray-700 sm:flex-row sm:items-center"
                        data-cart-item-id="{{ $item->id }}"
                    >
                        <img 
                            src="{{ $item->product->image_url ? (str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('public'.$item->product->image_url)) : 'https://picsum.photos/id/10/100/100' }}" 
                            alt="{{ $item->product->name }}" 
                            class="h-20 w-20 flex-shrink-0 rounded-md object-cover sm:h-24 sm:w-24"
                        >
                        <div class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex-1">
                                <h3 class="font-bold text-dark dark:text-white">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ $item->quantity }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 sm:hidden">
                                    PKR {{ number_format($item->price, 2) }} each
                                </p>
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <div class="text-right">
                                    <p class="text-lg font-bold text-secondary" data-item-total="{{ $item->id }}">
                                        PKR {{ number_format($item->price * $item->quantity, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                                        PKR {{ number_format($item->price, 2) }} each
                                    </p>
                                </div>
                                <button 
                                    type="button"
                                    data-cart-remove
                                    data-item-id="{{ $item->id }}"
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                    aria-label="Remove item"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between border-t pt-4 dark:border-gray-600">
                <span class="text-xl font-bold text-dark dark:text-white">Total:</span>
                <span class="text-2xl font-extrabold text-secondary" data-cart-total>
                    PKR {{ number_format($cartTotal, 2) }}
                </span>
            </div>

            <div class="mt-6 flex justify-end">
                <a 
                    href="{{ route('shop.checkout') }}" 
                    class="rounded-full bg-primary px-8 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-secondary"
                >
                    Proceed to Checkout
                </a>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Remove item
    document.querySelectorAll('[data-cart-remove]').forEach(button => {
        button.addEventListener('click', async (e) => {
            const itemId = button.getAttribute('data-item-id');
            await removeCartItem(itemId);
        });
    });

    async function removeCartItem(itemId) {
        const itemElement = document.querySelector(`[data-cart-item-id="${itemId}"]`);
        if (!itemElement) return;

        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        itemElement.style.opacity = '0.5';
        itemElement.style.pointerEvents = 'none';

        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to remove item');
            }

            // Remove item with animation
            itemElement.style.transition = 'all 0.3s ease';
            itemElement.style.transform = 'translateX(-100%)';
            itemElement.style.opacity = '0';
            
            setTimeout(() => {
                itemElement.remove();
                
                // Check if cart is empty
                const remainingItems = document.querySelectorAll('[data-cart-item-id]');
                if (remainingItems.length === 0) {
                    location.reload();
                    return;
                }

                // Update totals
                const cartTotalEl = document.querySelector('[data-cart-total]');
                if (cartTotalEl && data.cart_total !== undefined) {
                    cartTotalEl.textContent = `PKR ${parseFloat(data.cart_total).toFixed(2)}`;
                }

                // Update cart count in header
                const cartCountBadge = document.querySelector('[href*="cart"] .absolute');
                if (cartCountBadge && data.cart_count !== undefined) {
                    if (data.cart_count > 0) {
                        cartCountBadge.textContent = data.cart_count;
                        cartCountBadge.parentElement.classList.remove('hidden');
                    } else {
                        cartCountBadge.parentElement.classList.add('hidden');
                    }
                }
            }, 300);

            if (window.showToast) {
                showToast(data.message || 'Item removed', 'success');
            }
        } catch (error) {
            console.error('Remove cart error:', error);
            itemElement.style.opacity = '1';
            itemElement.style.pointerEvents = 'auto';
            if (window.showToast) {
                showToast(error.message || 'Failed to remove item', 'error');
            }
        }
    }
});
</script>
@endpush
