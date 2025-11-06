import './bootstrap';

const THEME_STORAGE_KEY = 'theme';

const getSystemTheme = () => (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

const getStoredTheme = () => {
    try {
        const stored = localStorage.getItem(THEME_STORAGE_KEY);
        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
    } catch (error) {
        // ignore persistence errors
    }

    return null;
};

const applyTheme = (theme) => {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
};

const updateThemeIcons = (theme) => {
    document.querySelectorAll('[data-theme-icon]').forEach((icon) => {
        const targetTheme = icon.getAttribute('data-theme-icon');
        icon.classList.toggle('hidden', targetTheme !== theme);
    });
};

const updateThemeToggleLabels = (currentTheme) => {
    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
    const label = `Switch to ${nextTheme} mode`;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', label);
    });
};

const persistTheme = (theme) => {
    try {
        localStorage.setItem(THEME_STORAGE_KEY, theme);
    } catch (error) {
        // ignore persistence errors
    }
};

const initializeTheme = () => {
    const stored = getStoredTheme();
    const initialTheme = stored ?? getSystemTheme();
    applyTheme(initialTheme);
    updateThemeIcons(initialTheme);
    updateThemeToggleLabels(initialTheme);
    return initialTheme;
};

const setupThemeToggle = () => {
    // Initialize theme and get current state
    let currentTheme = initializeTheme();

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Get current theme from DOM state (most reliable)
            const root = document.documentElement;
            const isCurrentlyDark = root.classList.contains('dark');
            const nextTheme = isCurrentlyDark ? 'light' : 'dark';
            console.log("🚀 ~ setupThemeToggle ~ nextTheme:", nextTheme)

            // Apply the new theme
            applyTheme(nextTheme);
            updateThemeIcons(nextTheme);
            updateThemeToggleLabels(nextTheme);
            persistTheme(nextTheme);
            
            // Update currentTheme for next click
            currentTheme = nextTheme;
            console.log("🚀 ~ setupThemeToggle ~ currentTheme:", currentTheme)
        });
    });
};

const observeSystemTheme = () => {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    if (typeof mediaQuery.addEventListener !== 'function') {
        return;
    }

    mediaQuery.addEventListener('change', (event) => {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return;
        }

        const theme = event.matches ? 'dark' : 'light';
        applyTheme(theme);
        updateThemeIcons(theme);
        updateThemeToggleLabels(theme);
    });
};

const setupMobileMenus = () => {
    document.querySelectorAll('[data-mobile-menu-toggle]').forEach((button) => {
        const targetId = button.getAttribute('data-mobile-menu-target');
        if (!targetId) {
            return;
        }

        const target = document.getElementById(targetId);
        if (!target) {
            return;
        }

        const setMenuState = (isOpen) => {
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            target.classList.toggle('hidden', !isOpen);

            button.querySelectorAll('[data-menu-icon]').forEach((icon) => {
                const iconState = icon.getAttribute('data-menu-icon');
                const shouldShow = (iconState === 'open' && isOpen) || (iconState === 'closed' && !isOpen);
                icon.classList.toggle('hidden', !shouldShow);
            });
        };

        button.addEventListener('click', (event) => {
            event.preventDefault();
            const isExpanded = button.getAttribute('aria-expanded') === 'true';
            setMenuState(!isExpanded);
        });

        target.querySelectorAll('[data-mobile-menu-close]').forEach((element) => {
            element.addEventListener('click', () => setMenuState(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                setMenuState(false);
            }
        });
    });
};

const setupCarousels = () => {
    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
        if (!slides.length) {
            return;
        }

        let activeIndex = 0;
        const indicators = Array.from(carousel.querySelectorAll('[data-carousel-indicator]'));
        const prevButton = carousel.querySelector('[data-carousel-prev]');
        const nextButton = carousel.querySelector('[data-carousel-next]');
        const autoplay = carousel.getAttribute('data-carousel-autoplay') === 'true';
        const interval = Number(carousel.getAttribute('data-carousel-interval') ?? 6000);
        let timer;

        const showSlide = (index) => {
            slides.forEach((slide, idx) => {
                const isActive = idx === index;
                // Ensure all slides maintain absolute positioning
                slide.classList.remove('relative');
                slide.classList.add('absolute');
                
                if (isActive) {
                    slide.classList.remove('opacity-0', '-z-10');
                    slide.classList.add('opacity-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'z-10');
                    slide.classList.add('opacity-0', '-z-10');
                }
            });

            indicators.forEach((indicator, idx) => {
                if (idx === index) {
                    indicator.classList.remove('bg-white/60');
                    indicator.classList.add('bg-primary');
                } else {
                    indicator.classList.remove('bg-primary');
                    indicator.classList.add('bg-white/60');
                }
            });

            activeIndex = index;
        };

        const goTo = (index) => {
            const newIndex = (index + slides.length) % slides.length;
            showSlide(newIndex);
            if (autoplay) {
                restartTimer();
            }
        };

        const restartTimer = () => {
            if (!autoplay) {
                return;
            }
            if (timer) {
                window.clearInterval(timer);
            }
            timer = window.setInterval(() => {
                goTo(activeIndex + 1);
            }, interval);
        };

        // Prev/Next button handlers
        if (prevButton) {
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                goTo(activeIndex - 1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                goTo(activeIndex + 1);
            });
        }

        // Indicator dot handlers
        indicators.forEach((indicator, idx) => {
            indicator.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                goTo(idx);
            });
        });

        // Pause autoplay on hover (if enabled)
        if (autoplay) {
            carousel.addEventListener('mouseenter', () => {
                if (timer) {
                    window.clearInterval(timer);
                }
            });

            carousel.addEventListener('mouseleave', () => {
                restartTimer();
            });
        }

        // Initialize first slide
        showSlide(0);
        
        // Start autoplay only if enabled
        if (autoplay) {
            restartTimer();
        }
    });
};

const setupTabGroups = () => {
    document.querySelectorAll('[data-tab-group]').forEach((group) => {
        const triggers = Array.from(group.querySelectorAll('[data-tab-trigger]'));
        const panels = Array.from(group.querySelectorAll('[data-tab-panel]'));
        if (!triggers.length || !panels.length) {
            return;
        }

        const initial = group.getAttribute('data-tab-initial') ?? triggers[0]?.getAttribute('data-tab-target');

        const activate = (target) => {
            triggers.forEach((trigger) => {
                const isActive = trigger.getAttribute('data-tab-target') === target;
                trigger.classList.toggle('bg-primary', isActive);
                trigger.classList.toggle('text-white', isActive);
                trigger.classList.toggle('border-primary', isActive);
                trigger.classList.toggle('text-gray-700', !isActive);
                trigger.classList.toggle('dark:text-gray-300', !isActive);
            });

            panels.forEach((panel) => {
                const isActive = panel.getAttribute('data-tab-id') === target;
                panel.classList.toggle('hidden', !isActive);
            });
        };

        triggers.forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                const target = trigger.getAttribute('data-tab-target');
                if (!target) {
                    return;
                }
                activate(target);
            });
        });

        if (initial) {
            activate(initial);
        }
    });
};

const setupMealIdeaForms = () => {
    document.querySelectorAll('[data-meal-idea-form]').forEach((form) => {
        const submitButton = form.querySelector('[data-meal-idea-button]');
        const resultContainer = form.parentElement?.querySelector('[data-meal-idea-result]');
        if (!submitButton || !resultContainer) {
            return;
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const action = form.getAttribute('action');
            if (!action) {
                return;
            }

            const token = form.querySelector('input[name="_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                return;
            }

            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Thinking...';
            resultContainer.classList.remove('text-red-400');
            resultContainer.textContent = '';

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: new FormData(form),
                });

                if (!response.ok) {
                    throw new Error('Unable to whip up an idea right now. Please try again.');
                }

                const data = await response.json();
                if (data?.idea) {
                    resultContainer.textContent = data.idea;
                } else {
                    resultContainer.textContent = 'Our chef is speechless. Give it another go! 🍗';
                }
            } catch (error) {
                resultContainer.classList.add('text-red-400');
                resultContainer.textContent = error instanceof Error ? error.message : 'Something went wrong. Please try again later.';
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    });
};

// Toast notification system
const showToast = (message, type = 'success', duration = 3000) => {
    const container = document.getElementById('toast-container');
    if (!container) {
        // Try to create container if it doesn't exist
        const newContainer = document.createElement('div');
        newContainer.id = 'toast-container';
        newContainer.className = 'fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-full sm:max-w-md px-4 sm:px-0';
        newContainer.setAttribute('aria-live', 'polite');
        newContainer.setAttribute('aria-atomic', 'true');
        document.body.appendChild(newContainer);
        return showToast(message, type, duration); // Retry with new container
    }

    const toastId = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.className = `
        flex items-center gap-3 rounded-lg border px-4 py-3 shadow-lg
        transition-all duration-300 ease-in-out
        transform translate-x-full opacity-0
        ${type === 'success' 
            ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/40 dark:text-green-300' 
            : type === 'error'
            ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-300'
            : type === 'info'
            ? 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
            : 'border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-300'
        }
        w-full sm:min-w-[300px] sm:max-w-md
    `.replace(/\s+/g, ' ').trim();

    const icon = type === 'success' 
        ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        : type === 'error'
        ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

    toast.innerHTML = `
        ${icon}
        <span class="flex-1 text-sm font-medium break-words">${message}</span>
        <button type="button" onclick="removeToast('${toastId}')" class="flex-shrink-0 text-current opacity-60 hover:opacity-100 transition-opacity ml-2" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;

    container.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });
    });

    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            removeToast(toastId);
        }, duration);
    }

    return toastId;
};

const removeToast = (toastId) => {
    const toast = document.getElementById(toastId);
    if (!toast) {
        return;
    }

    toast.classList.add('translate-x-full', 'opacity-0');
    setTimeout(() => {
        toast.remove();
    }, 300);
};

const setupWishlistToggle = () => {
    document.querySelectorAll('[data-wishlist-toggle]').forEach((button) => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const productId = button.getAttribute('data-product-id');
            if (!productId) {
                return;
            }

            const svg = button.querySelector('svg');
            const originalClass = svg.className.baseVal || svg.className;
            const originalFill = svg.getAttribute('fill');
            const isCurrentlyFilled = originalFill === 'currentColor';

            // Optimistic UI update
            button.disabled = true;
            if (isCurrentlyFilled) {
                svg.setAttribute('fill', 'none');
                svg.classList.remove('text-red-500', 'fill-current');
                svg.classList.add('text-gray-600', 'dark:text-gray-400');
            } else {
                svg.setAttribute('fill', 'currentColor');
                svg.classList.remove('text-gray-600', 'dark:text-gray-400');
                svg.classList.add('text-red-500', 'fill-current');
            }

            try {
                const response = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({ product_id: productId }),
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.requires_auth) {
                        // Redirect to login immediately if not authenticated
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.message || 'Failed to update wishlist');
                }

                // Update UI based on response
                if (data.is_in_wishlist) {
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.remove('text-gray-600', 'dark:text-gray-400');
                    svg.classList.add('text-red-500', 'fill-current');
                    button.setAttribute('aria-label', 'Remove from wishlist');
                    showToast(data.message || 'Added to wishlist', 'success');
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.classList.remove('text-red-500', 'fill-current');
                    svg.classList.add('text-gray-600', 'dark:text-gray-400');
                    button.setAttribute('aria-label', 'Add to wishlist');
                    showToast(data.message || 'Removed from wishlist', 'info');
                }

                // Update wishlist count in header if it exists
                const wishlistCountBadge = document.querySelector('[href*="wishlist"] .absolute');
                if (wishlistCountBadge && data.wishlist_count !== undefined) {
                    if (data.wishlist_count > 0) {
                        wishlistCountBadge.textContent = data.wishlist_count;
                        wishlistCountBadge.parentElement.classList.remove('hidden');
                    } else {
                        wishlistCountBadge.parentElement.classList.add('hidden');
                    }
                }
            } catch (error) {
                // Revert optimistic update on error
                if (isCurrentlyFilled) {
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.remove('text-gray-600', 'dark:text-gray-400');
                    svg.classList.add('text-red-500', 'fill-current');
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.classList.remove('text-red-500', 'fill-current');
                    svg.classList.add('text-gray-600', 'dark:text-gray-400');
                }

                console.error('Wishlist toggle error:', error);
                showToast(error.message || 'Failed to update wishlist. Please try again.', 'error');
            } finally {
                button.disabled = false;
            }
        });
    });
};

const setupProductDetailAddToCart = () => {
    const form = document.getElementById('product-detail-add-to-cart-form');
    if (!form) {
        return;
    }

    // Get add to cart behavior setting from meta tag or default to 'page'
    const addToCartBehavior = document.querySelector('meta[name="add-to-cart-behavior"]')?.getAttribute('content') || 'page';

    const button = document.getElementById('product-detail-add-to-cart-btn');
    const quantityInput = document.getElementById('product-quantity');
    const buttonContent = button.querySelector('span');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const productId = form.querySelector('input[name="product_id"]').value;
        const quantity = parseInt(quantityInput.value) || 1;

        if (!productId) {
            showToast('Product ID is missing', 'error');
            return;
        }

        const originalContent = buttonContent.innerHTML;
        button.disabled = true;
        buttonContent.innerHTML = `
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Adding...</span>
        `;

        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ 
                    product_id: productId,
                    quantity: quantity 
                }),
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If not authenticated, redirect to login immediately
                if (response.status === 401 || response.status === 403) {
                    window.location.href = '/login';
                    return;
                }
                // If HTML response (likely validation error or redirect), show error
                const text = await response.text();
                throw new Error('Invalid response from server. Please try again.');
            }

            const data = await response.json();

            if (!response.ok) {
                // If authentication required, redirect immediately
                if (data.requires_auth || response.status === 401 || response.status === 403) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error(data.message || 'Failed to add item to cart');
            }

            // Success!
            showToast(data.message || 'Item added to cart!', 'success');

            // Update cart count in header
            const cartCountBadge = document.querySelector('[href*="cart"] .absolute, button[onclick*="openCartDrawer"] .absolute');
            if (cartCountBadge && data.cart_count !== undefined) {
                if (data.cart_count > 0) {
                    cartCountBadge.textContent = data.cart_count;
                    cartCountBadge.parentElement.classList.remove('hidden');
                } else {
                    cartCountBadge.parentElement.classList.add('hidden');
                }
            }

            // Handle behavior based on setting
            if (addToCartBehavior === 'drawer' && window.openCartDrawer) {
                // Open cart drawer
                window.openCartDrawer();
            } else if (addToCartBehavior === 'page') {
                // Navigate to cart page
                window.location.href = '/shop/cart';
                return; // Don't restore button since we're navigating away
            }

            // Brief success animation
            button.classList.add('bg-green-500');
            setTimeout(() => {
                button.classList.remove('bg-green-500');
            }, 500);

            // Reset quantity to 1 after successful add
            quantityInput.value = 1;

        } catch (error) {
            console.error('Add to cart error:', error);
            showToast(error.message || 'Failed to add item to cart. Please try again.', 'error');
        } finally {
            button.disabled = false;
            buttonContent.innerHTML = originalContent;
        }
    });
};

const setupAddToCart = () => {
    // Get add to cart behavior setting from meta tag or default to 'page'
    const addToCartBehavior = document.querySelector('meta[name="add-to-cart-behavior"]')?.getAttribute('content') || 'page';

    document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const productId = button.getAttribute('data-product-id');
            if (!productId) {
                return;
            }

            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({ 
                        product_id: productId,
                        quantity: 1 
                    }),
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // If not authenticated, redirect to login immediately
                    if (response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }
                    // If HTML response (likely validation error or redirect), show error
                    const text = await response.text();
                    throw new Error('Invalid response from server. Please try again.');
                }

                const data = await response.json();

                if (!response.ok) {
                    // If authentication required, redirect immediately
                    if (data.requires_auth || response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.message || 'Failed to add item to cart');
                }

                // Success!
                showToast(data.message || 'Item added to cart!', 'success');

                // Update cart count in header
                const cartCountBadge = document.querySelector('[href*="cart"] .absolute, button[onclick*="openCartDrawer"] .absolute');
                if (cartCountBadge && data.cart_count !== undefined) {
                    if (data.cart_count > 0) {
                        cartCountBadge.textContent = data.cart_count;
                        cartCountBadge.parentElement.classList.remove('hidden');
                    } else {
                        cartCountBadge.parentElement.classList.add('hidden');
                    }
                }

                // Handle behavior based on setting
                if (addToCartBehavior === 'drawer' && window.openCartDrawer) {
                    // Open cart drawer
                    window.openCartDrawer();
                } else if (addToCartBehavior === 'page') {
                    // Navigate to cart page
                    window.location.href = '/shop/cart';
                    return; // Don't restore button since we're navigating away
                }

                // Brief success animation
                button.classList.add('bg-green-500');
                setTimeout(() => {
                    button.classList.remove('bg-green-500');
                }, 500);

            } catch (error) {
                console.error('Add to cart error:', error);
                showToast(error.message || 'Failed to add item to cart. Please try again.', 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });
};

// Theme will be initialized by inline script in <head> and again on DOMContentLoaded

// Make toast functions globally available
window.showToast = showToast;
window.removeToast = removeToast;

// Cart Drawer Functions
let isCartDrawerOpen = false;

function openCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-drawer-backdrop');
    const panel = document.getElementById('cart-drawer-panel');
    
    if (!drawer || !backdrop || !panel) return;

    drawer.classList.remove('hidden');
    isCartDrawerOpen = true;
    
    // Trigger animation
    setTimeout(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('translate-x-full');
    }, 10);

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Load cart data (will be available after cart-drawer component loads)
    if (typeof window.loadCartDrawer === 'function') {
        window.loadCartDrawer();
    } else {
        // Wait a bit for the function to be available
        setTimeout(() => {
            if (typeof window.loadCartDrawer === 'function') {
                window.loadCartDrawer();
            }
        }, 100);
    }
}

function closeCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-drawer-backdrop');
    const panel = document.getElementById('cart-drawer-panel');
    
    if (!drawer || !backdrop || !panel) return;

    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.add('translate-x-full');
    
    setTimeout(() => {
        drawer.classList.add('hidden');
        isCartDrawerOpen = false;
        document.body.style.overflow = '';
    }, 300);
}

// Make cart drawer functions globally available
window.openCartDrawer = openCartDrawer;
window.closeCartDrawer = closeCartDrawer;

// Close drawer on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isCartDrawerOpen) {
        closeCartDrawer();
    }
});

// Quick View Modal functionality
const setupQuickView = () => {
    const modal = document.getElementById('quick-view-modal');
    const closeBtn = document.getElementById('close-quick-view');
    const loadingDiv = document.getElementById('quick-view-loading');
    const imageDiv = document.getElementById('quick-view-image');
    const contentDiv = document.getElementById('quick-view-content');
    const imageSrc = document.getElementById('quick-view-image-src');
    const title = document.getElementById('quick-view-title');
    const price = document.getElementById('quick-view-price');
    const description = document.getElementById('quick-view-description');
    const quantitySpan = document.getElementById('quick-view-quantity');
    const decreaseBtn = document.getElementById('quick-view-decrease');
    const increaseBtn = document.getElementById('quick-view-increase');
    const addToCartBtn = document.getElementById('quick-view-add-to-cart');
    const fullDetailsLink = document.getElementById('quick-view-full-details');

    if (!modal) return;

    let currentQuantity = 1;
    let currentProductId = null;
    let currentProductSlug = null;

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        // Reset state
        loadingDiv.classList.remove('hidden');
        imageDiv.classList.add('hidden');
        contentDiv.classList.add('hidden');
        currentQuantity = 1;
        currentProductId = null;
        currentProductSlug = null;
    };

    const loadProduct = async (productId) => {
        openModal();
        loadingDiv.classList.remove('hidden');
        imageDiv.classList.add('hidden');
        contentDiv.classList.add('hidden');

        try {
            const response = await fetch(`/api/v1/products/${productId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load product');
            }

            const product = await response.json();

            // Update modal content
            const productImageUrl = product.image_url || (product.images && product.images[0]?.image_url) || (product.image_urls && product.image_urls[0]) || 'https://picsum.photos/seed/cluckngo/600/600';
            imageSrc.src = productImageUrl;
            imageSrc.alt = product.name;
            title.textContent = product.name;
            price.textContent = `PKR ${parseFloat(product.price || 0).toFixed(2)}`;
            description.textContent = product.description || '';
            currentProductId = product.id;
            currentProductSlug = product.slug || product.id;
            fullDetailsLink.href = `/product/${currentProductSlug}`;

            // Show content
            loadingDiv.classList.add('hidden');
            imageDiv.classList.remove('hidden');
            contentDiv.classList.remove('hidden');
        } catch (error) {
            console.error('Quick view error:', error);
            showToast('Failed to load product details', 'error');
            closeModal();
        }
    };

    // Quantity controls
    if (decreaseBtn && increaseBtn && quantitySpan) {
        decreaseBtn.addEventListener('click', () => {
            if (currentQuantity > 1) {
                currentQuantity--;
                quantitySpan.textContent = currentQuantity;
            }
        });

        increaseBtn.addEventListener('click', () => {
            currentQuantity++;
            quantitySpan.textContent = currentQuantity;
        });
    }

    // Add to cart from quick view
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async () => {
            if (!currentProductId) return;

            const originalText = addToCartBtn.innerHTML;
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Adding...
            `;

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        product_id: currentProductId,
                        quantity: currentQuantity,
                    }),
                });

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    if (response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error('Invalid response from server');
                }

                const data = await response.json();

                if (!response.ok) {
                    if (data.requires_auth || response.status === 401 || response.status === 403) {
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(data.message || 'Failed to add item to cart');
                }

                showToast(data.message || `${currentQuantity} x ${title.textContent} added to cart!`, 'success');

                // Update cart count
                const cartCountBadge = document.querySelector('[href*="cart"] .absolute, button[onclick*="openCartDrawer"] .absolute');
                if (cartCountBadge && data.cart_count !== undefined) {
                    if (data.cart_count > 0) {
                        cartCountBadge.textContent = data.cart_count;
                        cartCountBadge.parentElement.classList.remove('hidden');
                    } else {
                        cartCountBadge.parentElement.classList.add('hidden');
                    }
                }

                // Handle behavior based on setting
                const addToCartBehavior = document.querySelector('meta[name="add-to-cart-behavior"]')?.getAttribute('content') || 'page';
                if (addToCartBehavior === 'drawer' && window.openCartDrawer) {
                    closeModal();
                    setTimeout(() => window.openCartDrawer(), 300);
                } else if (addToCartBehavior === 'page') {
                    window.location.href = '/shop/cart';
                    return;
                } else {
                    closeModal();
                }

                // Reset quantity
                currentQuantity = 1;
                quantitySpan.textContent = '1';
            } catch (error) {
                console.error('Quick view add to cart error:', error);
                showToast(error.message || 'Failed to add item to cart', 'error');
            } finally {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = originalText;
            }
        });
    }

    // Close modal handlers
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Quick view button handlers (using event delegation for dynamically added cards)
    document.addEventListener('click', (e) => {
        // Check if the clicked element or its parent is a quick view button
        const button = e.target.closest('[data-quick-view]');
        if (button) {
            e.preventDefault();
            e.stopPropagation();
            const productId = button.getAttribute('data-product-id');
            if (productId) {
                loadProduct(productId);
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setupThemeToggle();
    observeSystemTheme();
    setupMobileMenus();
    setupCarousels();
    setupTabGroups();
    setupMealIdeaForms();
    setupWishlistToggle();
    setupAddToCart();
    setupProductDetailAddToCart();
    setupQuickView();
});
