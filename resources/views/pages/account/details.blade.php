@extends('layouts.app')

@section('title', 'Account Details - ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    <div class="mx-auto max-w-lg rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h1 class="mb-6 border-b pb-4 text-3xl font-extrabold text-secondary dark:border-gray-600">Account Details</h1>
        
        <form class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    value="{{ $user->name }}" 
                    readonly
                    class="mt-1 block w-full cursor-not-allowed rounded-md border border-gray-300 bg-gray-100 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    value="{{ $user->email }}" 
                    readonly
                    class="mt-1 block w-full cursor-not-allowed rounded-md border border-gray-300 bg-gray-100 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                >
            </div>

            <button 
                type="button" 
                disabled
                class="w-full cursor-not-allowed rounded-md bg-gray-400 px-4 py-3 text-lg font-bold text-white shadow-md"
            >
                Save Changes (Disabled)
            </button>
        </form>

        <div class="mt-8 text-center">
            <a 
                href="{{ route('account.profile') }}" 
                class="font-semibold text-primary hover:underline"
            >
                &larr; Back to Profile
            </a>
        </div>
    </div>
@endsection

