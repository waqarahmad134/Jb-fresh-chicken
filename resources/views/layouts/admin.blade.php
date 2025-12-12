<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel - ' . ($siteSettings['site_name'] ?? config('app.name')))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <link rel="preload" as="style" href="{{ asset('public/build/assets/app-Ch1-MkTq.css') }}" />
    <link rel="modulepreload" as="script" href="{{ asset('public/build/assets/app-CU1NkAvt.js') }}" />
    <link rel="stylesheet" href="{{ asset('public/build/assets/app-Ch1-MkTq.css') }}" />
    <script type="module" src="{{ asset('public/build/assets/app-CU1NkAvt.js') }}"></script>

    @stack('head')
</head>
<body class="antialiased font-sans bg-gray-50 text-dark dark:bg-gray-900 dark:text-light">
    {{-- Admin Header --}}
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <button id="admin-sidebar-toggle" type="button" class="lg:hidden rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('logo.svg') }}" alt="Logo" class="h-8 w-auto">
                    <span class="text-lg font-bold text-primary">Admin Panel</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                {{-- Theme Toggle --}}
                <button id="admin-theme-toggle" type="button" class="rounded p-2 hover:bg-gray-100 dark:hover:bg-gray-700" aria-label="Toggle theme">
                    <svg class="theme-icon-light hidden h-5 w-5 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="theme-icon-dark block h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
                
                {{-- Back to Site --}}
                <a href="{{ route('home') }}" class="rounded px-3 py-2 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700">
                    View Site
                </a>
                
                {{-- User Menu --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium">{{ $user->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    
    <div class="flex min-h-[calc(100vh-4rem)]">
        {{-- Sidebar --}}
        <aside id="admin-sidebar" class="fixed inset-y-16 left-0 z-40 w-64 -translate-x-full border-r border-gray-200 bg-white transition-transform dark:border-gray-700 dark:bg-gray-800 lg:static lg:translate-x-0">
            <nav class="h-full overflow-y-auto p-4">
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    
                    <div class="pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-gray-500">Manage</div>
                    
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Products
                    </a>
                    <a href="{{ route('admin.product-categories.index') }}" class="ml-8 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.product-categories.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        Product Categories
                    </a>
                    
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Orders
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Users
                    </a>
                    
                    <a href="{{ route('admin.blog.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.blog.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        Blog
                    </a>
                    <a href="{{ route('admin.blog-categories.index') }}" class="ml-8 block rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.blog-categories.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        Blog Categories
                    </a>
                    
                    <a href="{{ route('admin.tags.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.tags.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Tags
                    </a>
                    
                    <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.media.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Media Library
                    </a>
                    
                    <a href="{{ route('admin.database.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.database.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                        Database
                    </a>
                    
                    <a href="{{ route('admin.backup.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.backup.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Backup
                    </a>
                    
                    <a href="{{ route('admin.payment-gateways.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.payment-gateways.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Payment Gateways
                    </a>
                    
                    <div class="pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-gray-500">Settings</div>
                    
                    <a href="{{ route('admin.settings.general') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.settings.general') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        General
                    </a>
                    
                    <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Pages
                    </a>
                    
                    <a href="{{ route('admin.settings.product') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.settings.product') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Product Settings
                    </a>
                    
                    <a href="{{ route('admin.settings.robots') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition-colors {{ request()->routeIs('admin.settings.robots') ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Robots.txt
                    </a>
                </div>
            </nav>
        </aside>
        
        {{-- Main Content --}}
        <main class="flex-1 p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:border-green-800 dark:bg-green-900/40 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/40 dark:text-red-300">
                    <p class="font-semibold">Please fix the following errors:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    @include('components.toast')
    
    @stack('modals')
    
    @stack('scripts')
    
    <script>
        // Mobile sidebar toggle
        document.getElementById('admin-sidebar-toggle')?.addEventListener('click', () => {
            document.getElementById('admin-sidebar').classList.toggle('-translate-x-full');
        });
        
        // Admin theme toggle
        document.getElementById('admin-theme-toggle')?.addEventListener('click', () => {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
</body>
</html>

