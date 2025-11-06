@props(['testimonial'])

@php
    $rating = max(0, min(5, (int) ($testimonial->rating ?? 0)));
    $authorName = $testimonial->user?->name
        ?? ($testimonial->user_name ?? 'Happy Customer');
    $comment = $testimonial->comment ?? 'Loved every bite!';
@endphp

<article class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="mb-3 flex items-center gap-1 text-primary">
        @for ($i = 1; $i <= 5; $i++)
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 {{ $i <= $rating ? 'text-primary' : 'text-gray-300 dark:text-gray-600' }}">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.033a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.033a1 1 0 00-1.175 0l-2.8 2.033c-.784.57-1.838-.197-1.539-1.118l1.069-3.292a1 1 0 00-.364-1.118l-2.8-2.033c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        @endfor
    </div>
    <blockquote class="flex-1 text-gray-600 dark:text-gray-300">“{{ $comment }}”</blockquote>
    <p class="mt-4 text-right text-sm font-semibold text-dark dark:text-white">— {{ $authorName }}</p>
</article>

