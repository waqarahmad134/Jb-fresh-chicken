@extends('layouts.app')

@section('title', 'My Profile - ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="mb-4 text-4xl font-extrabold text-secondary">Welcome, {{ $user->name }}!</h1>
        <p class="mb-10 text-lg text-gray-600 dark:text-gray-400">Manage your account and orders from here.</p>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <a 
                href="{{ route('account.orders') }}" 
                class="flex flex-col items-center rounded-lg border border-gray-200 bg-white p-6 text-center shadow-md transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-3 h-12 w-12 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                <h2 class="text-xl font-bold text-dark dark:text-light">Order History</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">View your past orders</p>
            </a>

            <a 
                href="{{ route('account.wishlist') }}" 
                class="flex flex-col items-center rounded-lg border border-gray-200 bg-white p-6 text-center shadow-md transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-3 h-12 w-12 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C4.099 3.75 2 5.765 2 8.25c0 7.22 9 12 10 12s10-4.78 10-12z" />
                </svg>
                <h2 class="text-xl font-bold text-dark dark:text-light">My Wishlist</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">See your saved items</p>
            </a>

            <a 
                href="{{ route('account.details') }}" 
                class="flex flex-col items-center rounded-lg border border-gray-200 bg-white p-6 text-center shadow-md transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-3 h-12 w-12 text-primary">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <h2 class="text-xl font-bold text-dark dark:text-light">Account Details</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Update your information</p>
            </a>
        </div>
    </div>
@endsection

