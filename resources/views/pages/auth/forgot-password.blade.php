@extends('layouts.app')

@section('title', 'Forgot Password - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Reset your password')

@section('content')
    <div class="mx-auto max-w-md py-10">
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <h1 class="mb-6 text-center text-3xl font-extrabold text-secondary">Forgot Password</h1>
            
            <p class="mb-6 text-center text-gray-600 dark:text-gray-400">
                Enter your email address and we'll send you a link to reset your password.
            </p>

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
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
                        placeholder="you@example.com"
                        class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full rounded-md bg-primary px-4 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-secondary"
                >
                    Send Reset Link
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Remember your password? 
                <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">
                    Log in
                </a>
            </p>
        </div>
    </div>
@endsection

