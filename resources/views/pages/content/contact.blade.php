@extends('layouts.app')

@section('title', 'Contact Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Get in touch with us')

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="mb-10 text-center text-4xl font-extrabold text-secondary">Contact Us</h1>
        
        <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
            {{-- Contact Form --}}
            <div class="rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-2xl font-bold">Send us a Message</h2>
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium">Full Name</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required 
                            class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-700 @error('name') border-red-500 @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-700 @error('email') border-red-500 @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium">Message</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            required 
                            rows="5" 
                            class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm dark:border-gray-600 dark:bg-gray-700 @error('message') border-red-500 @enderror"
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full rounded-md bg-primary px-4 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-secondary"
                    >
                        Send Message
                    </button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="rounded-lg bg-amber-50 p-8 dark:bg-gray-800">
                <h2 class="mb-4 text-2xl font-bold">Get in Touch</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Have a question about your order or want to learn more? We're here to help.
                </p>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-6 w-6 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <div>
                            <h3 class="font-semibold">Email</h3>
                            <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@jbfreshchicken.com' }}" class="text-gray-600 hover:text-primary dark:text-gray-400">
                                {{ $siteSettings['contact_email'] ?? 'contact@jbfreshchicken.com' }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-1 h-6 w-6 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <div>
                            <h3 class="font-semibold">Phone</h3>
                            <a href="tel:+923039345647" class="text-gray-600 hover:text-primary dark:text-gray-400">
                                {{ $siteSettings['contact_phone'] ?? '0303-9345647' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

