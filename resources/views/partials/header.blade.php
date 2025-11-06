@php
    $siteName = $siteSettings['site_name'] ?? config('app.name', 'JB Fresh Chicken and Frozen Food');
    $blogEnabled = (bool) ($siteSettings['blog_enabled'] ?? false);

    $navLinks = [
        [
            'label' => 'Home',
            'href' => Route::has('home') ? route('home') : url('/'),
            'is_active' => request()->routeIs('home') || request()->is('/'),
        ],
        [
            'label' => 'Shop',
            'href' => Route::has('shop.index') ? route('shop.index') : url('/shop'),
            'is_active' => request()->is('shop*') || request()->is('product*'),
        ],
        [
            'label' => 'Blog',
            'href' => Route::has('blog.index') ? route('blog.index') : url('/blog'),
            'is_active' => request()->is('blog*'),
            'visible' => $blogEnabled,
        ],
        [
            'label' => 'About',
            'href' => Route::has('about') ? route('about') : url('/about'),
            'is_active' => request()->is('about'),
        ],
        [
            'label' => 'Contact',
            'href' => Route::has('contact') ? route('contact') : url('/contact'),
            'is_active' => request()->is('contact'),
        ],
    ];

    $navLinks = array_filter($navLinks, fn ($link) => $link['visible'] ?? true);

    $cartCount = (int) ($cartItemCount ?? 0);
    $wishlistCount = (int) ($wishlistCount ?? 0);

    $navLinkClass = function (bool $isActive): string {
        $base = 'text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary transition-colors font-semibold';
        return trim($base . ($isActive ? ' text-primary dark:text-primary' : ''));
    };

    $mobileMenuId = 'primary-mobile-nav';
@endphp

<header class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm sticky top-0 z-90 shadow-sm">
    <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <div class="flex-shrink-0">
                <a href="{{ Route::has('home') ? route('home') : url('/') }}" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <img src="{{ asset('logo.svg') }}" alt="{{ $siteName }}" class="h-10 w-auto sm:h-12" loading="eager">
                    <!-- <span class="hidden text-xl font-extrabold text-primary sm:block md:text-2xl">{{ $siteName }}</span> -->
                </a>
            </div>

            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-6">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}" class="{{ $navLinkClass($link['is_active']) }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="button" data-theme-toggle aria-label="Toggle dark mode" class="p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <span aria-hidden="true" data-theme-icon="light">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </span>
                    <span aria-hidden="true" data-theme-icon="dark" class="hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </button>

                <a href="{{ Route::has('account.wishlist') ? route('account.wishlist') : url('/account/wishlist') }}" class="relative p-2 rounded-full hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    <span class="sr-only">View wishlist</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                    </svg>
                    @if ($wishlistCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-secondary px-1 text-[10px] font-bold text-white">
                            {{ $wishlistCount }}
                        </span>
                    @endif
                </a>

                @php
                    $addToCartBehavior = $siteSettings['add_to_cart_behavior'] ?? 'page';
                @endphp
                @if($addToCartBehavior === 'drawer')
                    <button 
                        type="button"
                        onclick="openCartDrawer()"
                        class="relative p-2 rounded-full hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        aria-label="Open cart drawer"
                    >
                        <span class="sr-only">View cart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </button>
                @else
                    <a href="{{ Route::has('shop.cart') ? route('shop.cart') : url('/shop/cart') }}" class="relative p-2 rounded-full hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="sr-only">View cart</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif

                <div class="hidden md:block">
                    @auth
                        <div class="relative group">
                            <a href="{{ Route::has('account.profile') ? route('account.profile') : url('/account/profile') }}" class="p-2 flex items-center space-x-2 rounded-full hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                                <span class="sr-only">Account</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <div class="absolute right-0 w-56 rounded-md bg-white py-2 text-sm shadow-lg ring-1 ring-black/10 dark:bg-gray-800 dark:ring-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none group-hover:pointer-events-auto">
                                <div class="px-4 py-2 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                    Signed in as<br>
                                    <span class="font-semibold">{{ Auth::user()->name ?? 'User' }}</span>
                                </div>
                                <a href="{{ Route::has('account.profile') ? route('account.profile') : url('/account/profile') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                <a href="{{ Route::has('account.orders') ? route('account.orders') : url('/account/orders') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">My Orders</a>
                                @if (Auth::user()?->is_admin)
                                    <a href="{{ url('/admin') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Admin Panel</a>
                                @endif
                                @php
                                    $logoutUrl = Route::has('logout') ? route('logout') : url('/logout');
                                @endphp
                                <form method="POST" action="{{ $logoutUrl }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="p-2 rounded-full hover:bg-amber-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <span class="sr-only">Sign in</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    @endauth
                </div>

                <div class="md:hidden">
                    <button type="button" data-mobile-menu-toggle data-mobile-menu-target="{{ $mobileMenuId }}" aria-controls="{{ $mobileMenuId }}" aria-expanded="false" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 dark:text-gray-300 hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span class="sr-only">Toggle navigation</span>
                        <svg data-menu-icon="closed" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        <svg data-menu-icon="open" class="h-6 w-6 hidden" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="{{ $mobileMenuId }}" class="md:hidden hidden pb-4">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 space-y-2">
                    @auth
                        <a href="{{ Route::has('account.profile') ? route('account.profile') : url('/account/profile') }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">Profile</a>
                        <a href="{{ Route::has('account.orders') ? route('account.orders') : url('/account/orders') }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">My Orders</a>
                        @if (Auth::user()?->is_admin)
                            <a href="{{ url('/admin') }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">Admin Panel</a>
                        @endif
                        @php
                            $mobileLogoutUrl = Route::has('logout') ? route('logout') : url('/logout');
                        @endphp
                        <form method="POST" action="{{ $mobileLogoutUrl }}">
                            @csrf
                            <button type="submit" data-mobile-menu-close class="w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" data-mobile-menu-close class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-gray-700">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

