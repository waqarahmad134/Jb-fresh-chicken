@extends('layouts.app')

@section('title', 'Blog - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', 'Tips, tricks, and tasty tales from our kitchen to yours.')

@section('content')
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-extrabold text-secondary">JB Fresh Blog</h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Fresh recipes, cooking tips, and news from JB Fresh Chicken.
        </p>
    </div>

    @if ($posts->count() > 0)
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                @include('components.blog.post-card', ['post' => $post])
            @endforeach
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    @else
        <p class="py-20 text-center text-gray-600 dark:text-gray-400">No blog posts available.</p>
    @endif
@endsection

