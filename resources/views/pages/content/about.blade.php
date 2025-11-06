@extends('layouts.app')

@section('title', 'About Us - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Learn more about ' . ($siteSettings['site_name'] ?? config('app.name')))

@section('content')
    <div class="mx-auto max-w-4xl rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800">
        @if ($page)
            <h1 class="mb-8 text-center text-4xl font-extrabold text-secondary">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none dark:prose-invert">
                {!! $page->content !!}
            </div>
        @else
            <h1 class="mb-8 text-center text-4xl font-extrabold text-secondary">About Us</h1>
            <div class="prose prose-lg max-w-none dark:prose-invert">
                <p>
                    Welcome to {{ $siteSettings['site_name'] ?? config('app.name') }}! We're passionate about serving the 
                    crispiest, most delicious chicken you've ever tasted.
                </p>
                <p>
                    Our journey began with a simple mission: to bring joy to every meal with our signature recipes 
                    and quality ingredients.
                </p>
                <p>
                    Thank you for choosing us for your chicken cravings!
                </p>
            </div>
        @endif
    </div>
@endsection

