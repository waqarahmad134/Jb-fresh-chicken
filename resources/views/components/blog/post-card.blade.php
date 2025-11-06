@props(['post', 'placeholderImage' => 'https://picsum.photos/seed/jbfreshblog/800/600'])

@php
    $postUrl = url('/blog/' . ($post->slug ?? $post->id));
    $imageUrl = $post->image_url ?? $placeholderImage;
    $publishedAt = $post->published_at ?? $post->created_at;
    $authorName = $post->author_name
        ?? $post->author?->name
        ?? 'JB Fresh Team';
    $categoryName = $post->blogCategory?->name ?? $post->category ?? 'Updates';
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
    <a href="{{ $postUrl }}" class="block overflow-hidden">
        <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
    </a>
    <div class="flex flex-1 flex-col p-6">
        <span class="text-xs font-semibold uppercase tracking-widest text-primary">{{ $categoryName }}</span>
        <a href="{{ $postUrl }}" class="mt-3 text-xl font-bold text-dark transition-colors hover:text-primary dark:text-white">
            {{ $post->title }}
        </a>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 130) }}</p>
        <div class="mt-auto flex items-center justify-between border-t border-gray-200 pt-4 text-sm dark:border-gray-700">
            <div class="text-left">
                <p class="font-semibold text-dark dark:text-white">{{ $authorName }}</p>
                @if ($publishedAt)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $publishedAt->format('M d, Y') }}</p>
                @endif
            </div>
            <a href="{{ $postUrl }}" class="inline-flex items-center text-sm font-semibold text-primary transition-colors hover:text-secondary">
                Read More &rarr;
            </a>
        </div>
    </div>
</article>

