@extends('layouts.app')

@section('title', 'Terms of Service - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Terms of Service')

@section('content')
    <div class="mx-auto max-w-4xl rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800">
        @if ($page)
            <h1 class="mb-8 text-center text-4xl font-extrabold text-secondary">{{ $page->title }}</h1>
            <div class="prose prose-lg max-w-none dark:prose-invert">
                {!! $page->content !!}
            </div>
        @else
            <h1 class="mb-8 text-center text-4xl font-extrabold text-secondary">Terms of Service</h1>
            <div class="prose prose-lg max-w-none dark:prose-invert">
                <p>Terms of service content will be added here.</p>
            </div>
        @endif
    </div>
@endsection

