@php
    $addToCartBehavior = isset($siteSettings) && isset($siteSettings['add_to_cart_behavior']) 
        ? $siteSettings['add_to_cart_behavior'] 
        : 'page';
@endphp

<div id="cart-drawer" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title">
    <!-- Backdrop -->
    <div 
        id="cart-drawer-backdrop"
        class="absolute inset-0 bg-black/50 transition-opacity duration-300 ease-in-out opacity-0"
        onclick="closeCartDrawer()"
    ></div>

    <!-- Drawer Panel -->
    <div 
        id="cart-drawer-panel"
        class="fixed top-0 right-0 h-full w-full max-w-md bg-light dark:bg-dark shadow-xl flex flex-col transition-transform duration-300 ease-in-out transform translate-x-full"
    >
        <header class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 id="cart-drawer-title" class="text-xl font-bold text-secondary">
                Your Cart (<span id="cart-drawer-count">0</span>)
            </h2>
            <button
                onclick="closeCartDrawer()"
                class="p-2 rounded-full text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                aria-label="Close cart drawer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        <!-- Loading State -->
        <div id="cart-drawer-loading" class="flex-1 flex items-center justify-center p-4">
            <div class="text-center">
                <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-primary border-r-transparent"></div>
                <p class="mt-2 text-sm text-gray-500">Loading cart...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="cart-drawer-empty" class="flex-1 hidden flex-col items-center justify-center text-center p-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-24 w-24 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <p class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Your cart is empty</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Add some delicious chicken to get started!</p>
            <a
                href="{{ route('shop.index') }}"
                onclick="closeCartDrawer()"
                class="mt-6 inline-block rounded-full bg-primary px-6 py-2 font-bold text-white transition-colors hover:bg-secondary"
            >
                Start Shopping
            </a>
        </div>

        <!-- Cart Items -->
        <div id="cart-drawer-items" class="flex-1 hidden overflow-y-auto p-4 space-y-4">
            <!-- Items will be inserted here via JavaScript -->
        </div>

        <!-- Footer -->
        <footer id="cart-drawer-footer" class="hidden p-4 border-t border-gray-200 dark:border-gray-700 space-y-4">
            <div class="flex justify-between items-center text-lg">
                <span class="font-semibold">Subtotal:</span>
                <span id="cart-drawer-total" class="font-extrabold text-secondary">PKR 0.00</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <a
                    href="{{ route('shop.cart') }}"
                    onclick="closeCartDrawer()"
                    class="w-full text-center bg-gray-200 dark:bg-gray-700 text-dark dark:text-light font-bold py-3 px-4 rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
                >
                    View Cart
                </a>
                <a
                    href="{{ route('shop.checkout') }}"
                    onclick="closeCartDrawer()"
                    class="w-full text-center bg-primary text-white font-bold py-3 px-4 rounded-full hover:bg-secondary transition-colors"
                >
                    Checkout
                </a>
            </div>
        </footer>
    </div>
</div>

@push('scripts')
<script>
    const addToCartBehavior = '{{ $addToCartBehavior }}';

    async function loadCartDrawer() {
        const loading = document.getElementById('cart-drawer-loading');
        const empty = document.getElementById('cart-drawer-empty');
        const items = document.getElementById('cart-drawer-items');
        const footer = document.getElementById('cart-drawer-footer');

        // Show loading
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        items.classList.add('hidden');
        footer.classList.add('hidden');

        try {
            const response = await fetch('{{ route("shop.cart.data") }}', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If not authenticated, redirect to login immediately
                if (response.status === 401 || response.status === 403) {
                    closeCartDrawer();
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Invalid response from server');
            }

            const data = await response.json();

            if (!response.ok) {
                // If authentication required, redirect immediately
                if (data.requires_auth || response.status === 401 || response.status === 403) {
                    closeCartDrawer();
                    window.location.href = '/login';
                    return;
                }
                throw new Error(data.message || 'Failed to load cart');
            }

            // Hide loading
            loading.classList.add('hidden');

            if (data.is_empty || data.cart_items.length === 0) {
                empty.classList.remove('hidden');
                updateCartCount(0);
            } else {
                items.classList.remove('hidden');
                footer.classList.remove('hidden');
                
                // Render items
                renderCartItems(data.cart_items);
                updateCartDrawerTotal(data.cart_total);
                updateCartCount(data.cart_count);
            }
        } catch (error) {
            console.error('Cart drawer error:', error);
            loading.classList.add('hidden');
            
            // Show error message in empty state
            const emptyEl = document.getElementById('cart-drawer-empty');
            if (emptyEl) {
                emptyEl.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-24 w-24 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <p class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Unable to load cart</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">${error.message || 'Please try again later'}</p>
                    <a
                        href="{{ route('shop.cart') }}"
                        onclick="closeCartDrawer()"
                        class="mt-6 inline-block rounded-full bg-primary px-6 py-2 font-bold text-white transition-colors hover:bg-secondary"
                    >
                        View Cart Page
                    </a>
                `;
                emptyEl.classList.remove('hidden');
            }
        }
    }

    function renderCartItems(cartItems) {
        const container = document.getElementById('cart-drawer-items');
        if (!container) return;

        container.innerHTML = cartItems.map(item => `
            <div class="flex gap-4 rounded-md border p-4 dark:border-gray-700" data-cart-item-id="${item.id}">
                ${item.product_image ? `
                    <img src="${item.product_image}" alt="${item.product_name}" class="h-20 w-20 flex-shrink-0 rounded-md object-cover">
                ` : `
                    <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-700 text-xs text-gray-400">No Image</div>
                `}
                <div class="flex flex-1 flex-col gap-2">
                    <h3 class="font-bold text-dark dark:text-white">${item.product_name}</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button 
                                onclick="updateCartItemQuantity(${item.id}, ${item.quantity - 1})"
                                class="flex h-8 w-8 items-center justify-center rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                                ${item.quantity <= 1 ? 'disabled' : ''}
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="w-8 text-center font-semibold">${item.quantity}</span>
                            <button 
                                onclick="updateCartItemQuantity(${item.id}, ${item.quantity + 1})"
                                class="flex h-8 w-8 items-center justify-center rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-secondary" data-item-total="${item.id}">
                                PKR ${parseFloat(item.total).toFixed(2)}
                            </p>
                            <p class="text-xs text-gray-500">PKR ${parseFloat(item.price).toFixed(2)} each</p>
                        </div>
                    </div>
                    <button 
                        onclick="removeCartItemFromDrawer(${item.id})"
                        class="mt-2 self-start text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    >
                        Remove
                    </button>
                </div>
            </div>
        `).join('');
    }

    async function updateCartItemQuantity(itemId, quantity) {
        if (quantity < 1) return;

        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ quantity }),
            });

            // Check if authentication required
            if (response.status === 401 || response.status === 403) {
                closeCartDrawer();
                window.location.href = '/login';
                return;
            }

            const data = await response.json();

            if (!response.ok) {
                if (data.requires_auth) {
                    closeCartDrawer();
                    window.location.href = '/login';
                    return;
                }
                throw new Error(data.message || 'Failed to update item');
            }

            // Reload drawer
            await loadCartDrawer();
            
            // Update header cart count
            updateCartCount(data.cart_count);
            
            if (typeof window.showToast === 'function') {
                window.showToast(data.message || 'Cart updated', 'success');
            }
        } catch (error) {
            console.error('Update cart error:', error);
            if (typeof window.showToast === 'function') {
                window.showToast(error.message || 'Failed to update item', 'error');
            }
        }
    }

    async function removeCartItemFromDrawer(itemId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

        try {
            const response = await fetch(`/cart/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            // Check if authentication required
            if (response.status === 401 || response.status === 403) {
                closeCartDrawer();
                window.location.href = '/login';
                return;
            }

            const data = await response.json();

            if (!response.ok) {
                if (data.requires_auth) {
                    closeCartDrawer();
                    window.location.href = '/login';
                    return;
                }
                throw new Error(data.message || 'Failed to remove item');
            }

            // Reload drawer
            await loadCartDrawer();
            
            // Update header cart count
            updateCartCount(data.cart_count);
            
            if (window.showToast) {
                showToast(data.message || 'Item removed', 'success');
            }
        } catch (error) {
            console.error('Remove cart error:', error);
            if (window.showToast) {
                showToast(error.message || 'Failed to remove item', 'error');
            }
        }
    }

    function updateCartDrawerTotal(total) {
        const totalEl = document.getElementById('cart-drawer-total');
        if (totalEl) {
            totalEl.textContent = `PKR ${parseFloat(total).toFixed(2)}`;
        }
    }

    function updateCartCount(count) {
        const drawerCount = document.getElementById('cart-drawer-count');
        if (drawerCount) {
            drawerCount.textContent = count;
        }

        // Update header cart count
        const headerCount = document.querySelector('[href*="cart"] .absolute');
        if (headerCount) {
            headerCount.textContent = count;
            if (count > 0) {
                headerCount.parentElement.classList.remove('hidden');
            } else {
                headerCount.parentElement.classList.add('hidden');
            }
        }
    }

    // Make functions globally available
    window.loadCartDrawer = loadCartDrawer;
    window.updateCartItemQuantity = updateCartItemQuantity;
    window.removeCartItemFromDrawer = removeCartItemFromDrawer;
</script>
@endpush

