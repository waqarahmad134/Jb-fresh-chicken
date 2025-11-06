@extends('layouts.app')

@section('title', 'Login - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Login to your account to access your orders, wishlist, and more.')

@section('content')
    <div class="mx-auto max-w-md py-10">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <h1 class="mb-6 text-center text-3xl font-extrabold text-secondary">Login</h1>
            
            <p class="mb-4 text-center text-sm text-gray-500 dark:text-gray-400">
                Hint: <code class="rounded bg-gray-200 p-1 dark:bg-gray-700">user@example.com</code> / 
                <code class="rounded bg-gray-200 p-1 dark:bg-gray-700">password</code>
            </p>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('password') border-red-500 @enderror"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Remember me
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full rounded-md bg-primary px-4 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-secondary"
                >
                    Log In
                </button>
            </form>

            <div class="mt-4 text-center">
                <a 
                    href="{{ route('password.request') }}" 
                    class="text-sm font-medium text-primary hover:underline"
                >
                    Forgot Password?
                </a>
            </div>

            <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-medium text-primary hover:underline">
                    Sign up
                </a>
            </p>
        </div>
    </div>
@endsection

