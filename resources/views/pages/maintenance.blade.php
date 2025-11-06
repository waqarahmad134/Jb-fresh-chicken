<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Maintenance - {{ config('app.name') }}</title>
    
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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-light text-dark dark:bg-dark dark:text-light">
    <div class="flex min-h-screen items-center justify-center">
        <div class="p-8 text-center">
            <h1 class="mb-4 text-4xl font-extrabold text-secondary">Under Maintenance</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                We're currently performing some scheduled maintenance.
            </p>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                We'll be back online shortly. Thanks for your patience!
            </p>
        </div>
    </div>
</body>
</html>

