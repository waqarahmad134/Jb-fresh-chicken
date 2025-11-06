@extends('layouts.app')

@section('title', $post->title . ' - Blog - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', \Illuminate\Support\Str::limit($post->excerpt, 150))

@section('content')
    <article class="mx-auto max-w-4xl rounded-lg bg-white p-8 shadow-lg dark:bg-gray-800">
        <header class="mb-8 border-b pb-8 text-center dark:border-gray-700">
            <p class="font-semibold text-primary">{{ $post->blogCategory->name ?? 'Uncategorized' }}</p>
            <h1 class="mt-2 text-4xl font-extrabold leading-tight text-secondary md:text-5xl">
                {{ $post->title }}
            </h1>
            <div class="mt-6 flex items-center justify-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                    <img 
                        src="{{ $post->author_image_url ?? 'https://picsum.photos/id/64/50/50' }}" 
                        alt="{{ $post->author_name }}" 
                        class="mr-2 h-10 w-10 rounded-full"
                    >
                    <span>By {{ $post->author_name }}</span>
                </div>
                <span>•</span>
                <span>{{ $post->published_at->format('F d, Y') }}</span>
            </div>
        </header>

        <img 
            src="{{ $post->image_url }}" 
            alt="{{ $post->title }}" 
            class="mb-8 h-auto max-h-96 w-full rounded-lg object-cover"
        >

        <div class="prose prose-lg max-w-none dark:prose-invert">
            {!! $post->content !!}
        </div>

        <div class="mt-12 border-t pt-8 text-center dark:border-gray-700">
            <a href="{{ route('blog.index') }}" class="font-semibold text-primary hover:underline">
                &larr; Back to all posts
            </a>
        </div>
    </article>
@endsection

