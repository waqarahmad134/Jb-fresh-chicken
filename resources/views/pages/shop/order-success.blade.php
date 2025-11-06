@extends('layouts.app')

@section('title', 'Order Success - ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border border-gray-200 bg-white p-12 py-20 text-center shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-20 w-20 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        
        <h1 class="mb-3 text-4xl font-extrabold text-secondary">Order Placed!</h1>
        
        <p class="mb-8 text-lg text-gray-600 dark:text-gray-300">
            Thank you for your order. We're getting your delicious chicken ready and it will be with you shortly!
        </p>
        
        <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
            <a 
                href="{{ route('account.orders') }}" 
                class="inline-block rounded-full border-2 border-primary px-8 py-3 font-bold text-primary transition-colors hover:bg-primary hover:text-white"
            >
                View Orders
            </a>
            <a 
                href="{{ route('shop.index') }}" 
                class="inline-block rounded-full bg-primary px-8 py-3 font-bold text-white transition-colors hover:bg-secondary"
            >
                Continue Shopping
            </a>
        </div>
    </div>
@endsection

