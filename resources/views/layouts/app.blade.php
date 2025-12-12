<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $siteSettings['site_name'] ?? config('app.name', 'JB Fresh Chicken and Frozen Food'))</title>

    @php
        $defaultMetaDescription = $siteSettings['meta_description'] ?? 'Order fresh chicken and frozen food from JB Fresh Chicken. Quality products delivered to your door.';
    @endphp

    <meta name="description" content="@yield('meta_description', $defaultMetaDescription)">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="add-to-cart-behavior" content="{{ $siteSettings['add_to_cart_behavior'] ?? 'page' }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <script>
        // Initialize theme immediately to prevent flash of wrong theme
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <link rel="preload" as="style" href="{{ asset('public/build/assets/app-Ch1-MkTq.css') }}" />
    <link rel="modulepreload" as="script" href="{{ asset('public/build/assets/app-CU1NkAvt.js') }}" />
    <link rel="stylesheet" href="{{ asset('public/build/assets/app-Ch1-MkTq.css') }}" />
    <script type="module" src="{{ asset('public/build/assets/app-CU1NkAvt.js') }}"></script>

    @stack('head')
</head>
<body class="antialiased font-sans bg-light text-dark dark:bg-dark dark:text-light">
    <div class="flex min-h-screen flex-col">
        @include('partials.header')

        <main class="flex-1">
            @if (session('success'))
                <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <div class="rounded-full border border-green-200 bg-green-50 px-6 py-3 text-sm font-semibold text-green-700 shadow-sm dark:border-green-800 dark:bg-green-900/40 dark:text-green-300">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
                    <div class="rounded-lg border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700 shadow-sm dark:border-red-800 dark:bg-red-900/40 dark:text-red-300">
                        <p class="font-semibold">Please fix the following:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @hasSection('full-width')
                @yield('full-width')
            @else
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
                    @yield('content')
                </div>
            @endif
        </main>

        @include('partials.footer')
    </div>

    @include('components.toast')
    @include('components.cart-drawer')
    @include('components.quick-view-modal')

    @stack('modals')
    @stack('scripts')
</body>
</html>

