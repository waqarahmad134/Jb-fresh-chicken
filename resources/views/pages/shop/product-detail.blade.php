@extends('layouts.app')

@section('title', $product->name . ' - ' . ($siteSettings['site_name'] ?? config('app.name')))
@section('meta_description', \Illuminate\Support\Str::limit($product->description, 150))

@section('content')
    {{-- Breadcrumbs --}}
    <nav class="mb-6 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-primary">Shop</a>
        <span class="mx-2">/</span>
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-primary">{{ $product->category->name }}</a>
        <span class="mx-2">/</span>
        <span>{{ $product->name }}</span>
    </nav>

    {{-- Product Details --}}
    <div class="mb-16 grid grid-cols-1 gap-12 lg:grid-cols-2">
        {{-- Product Images --}}
        <div>
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <img 
                    id="product-main-image"
                    src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('public'.$product->image_url) }}" 
                    alt="{{ $product->name }}" 
                    class="h-96 w-full object-cover"
                >
            </div>
            @if ($product->image_urls && count($product->image_urls) > 1)
                <div class="mt-4 grid grid-cols-4 gap-2" id="product-thumbnails">
                    @foreach (array_slice($product->image_urls, 0, 8) as $index => $imageUrl)
                        @php 
                            $isActive = $imageUrl === $product->image_url;
                            $displayUrl = str_starts_with($imageUrl, 'http') ? $imageUrl : asset('public'.$imageUrl);
                        @endphp
                        <img 
                            src="{{ $displayUrl }}" 
                            alt="{{ $product->name }} thumbnail {{ $index + 1 }}" 
                            data-thumb
                            data-src="{{ $displayUrl }}"
                            class="h-20 w-full cursor-pointer rounded-md border object-cover transition {{ $isActive ? 'border-primary ring-2 ring-primary' : 'border-gray-200 dark:border-gray-700 hover:border-primary' }}"
                        >
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div>
            <h1 class="mb-4 text-4xl font-extrabold text-secondary">{{ $product->name }}</h1>
            
            <div class="mb-4 flex items-center gap-2">
                <div class="flex items-center">
                    @php
                        $rating = (float) $product->average_rating;
                        $fullStars = (int) round($rating); // Round to nearest integer for display
                    @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $fullStars)
                            {{-- Full star --}}
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z" />
                            </svg>
                        @else
                            {{-- Empty star --}}
                            <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z" />
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">
                    @if ($product->reviews_count > 0)
                        {{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} {{ $product->reviews_count === 1 ? 'review' : 'reviews' }})
                    @else
                        No reviews yet
                    @endif
                </span>
            </div>

            <p class="mb-6 text-3xl font-bold text-primary">PKR {{ number_format($product->price, 2) }}</p>

            <p class="mb-6 text-gray-700 dark:text-gray-300">{{ $product->description }}</p>

            <form id="product-detail-add-to-cart-form" class="mb-6 space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <div>
                    <label class="mb-2 block font-medium">Quantity:</label>
                    <input 
                        type="number" 
                        id="product-quantity"
                        name="quantity" 
                        value="1" 
                        min="1" 
                        max="10"
                        class="w-24 rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"
                    >
                </div>

                <button 
                    type="submit" 
                    id="product-detail-add-to-cart-btn"
                    class="w-full rounded-md bg-primary px-6 py-3 font-bold text-white transition hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed md:w-auto"
                >
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Add to Cart</span>
                    </span>
                </button>
            </form>

            {{-- Product Meta --}}
            <div class="space-y-2 border-t pt-6 text-sm dark:border-gray-700">
                <p><span class="font-medium">Category:</span> {{ $product->category->name }}</p>
                @if ($product->tags->count() > 0)
                    <p>
                        <span class="font-medium">Tags:</span> 
                        @foreach ($product->tags as $tag)
                            <span class="inline-block rounded-full bg-amber-100 px-2 py-1 text-xs dark:bg-amber-900">{{ $tag->name }}</span>
                        @endforeach
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div class="mb-16">
        <h2 class="mb-6 text-3xl font-bold text-secondary">Customer Reviews</h2>
        
        {{-- Review Form (Only for logged in users who haven't reviewed yet) --}}
        @auth
            @if (!$userHasReviewed)
                <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="mb-4 text-xl font-bold">Write a Review</h3>
                <form id="review-form" action="{{ route('product.review', $product->slug) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Rating --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rating</label>
                        <div class="flex items-center gap-2" id="rating-selector">
                            @for ($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    data-rating="{{ $i }}"
                                    class="rating-star h-8 w-8 text-gray-300 transition-colors hover:text-yellow-400 focus:outline-none focus:ring-2 focus:ring-primary"
                                    aria-label="Rate {{ $i }} star{{ $i !== 1 ? 's' : '' }}"
                                >
                                    <svg class="h-full w-full" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="rating-input" value="" required>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Comment --}}
                    <div>
                        <label for="comment" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Your Review</label>
                        <textarea
                            id="comment"
                            name="comment"
                            rows="5"
                            required
                            minlength="10"
                            maxlength="1000"
                            placeholder="Share your experience with this product..."
                            class="w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('comment') border-red-500 @enderror"
                        >{{ old('comment') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Minimum 10 characters required</p>
                        @error('comment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        id="submit-review-btn"
                        class="rounded-md bg-primary px-6 py-2 font-semibold text-white transition-colors hover:bg-secondary disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Submit Review
                    </button>
                </form>
                </div>
            @else
                <div class="mb-6 rounded-lg border border-gray-200 bg-green-50 p-4 dark:border-gray-700 dark:bg-green-900/20">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        You have already reviewed this product. Thank you for your feedback!
                    </p>
                </div>
            @endif
        @else
            <div class="mb-6 rounded-lg border border-gray-200 bg-amber-50 p-4 dark:border-gray-700 dark:bg-amber-900/20">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Login</a> to write a review.
                </p>
            </div>
        @endauth
        
        @if ($product->reviews->count() > 0)
            <div class="space-y-4">
                @foreach ($product->reviews as $review)
                    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="h-4 w-4 {{ $review->rating > $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.381-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="font-medium">{{ $review->user->name ?? 'Anonymous' }}</span>
                                @if ($review->is_verified_purchase)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Verified Purchase
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">No reviews yet. Be the first to review this product!</p>
        @endif
    </div>

    {{-- Related Products --}}
    @if ($relatedProducts->count() > 0)
        <div>
            <h2 class="mb-6 text-3xl font-bold text-secondary">You May Also Like</h2>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    @include('components.product.card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Product image gallery
    (function setupGallery() {
        const mainImage = document.getElementById('product-main-image');
        const thumbs = document.querySelectorAll('#product-thumbnails [data-thumb]');
        if (!mainImage || !thumbs.length) return;

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                const newSrc = thumb.getAttribute('data-src');
                if (!newSrc || mainImage.getAttribute('src') === newSrc) return;

                // Swap image with a small fade
                mainImage.style.opacity = '0.2';
                setTimeout(() => {
                    mainImage.setAttribute('src', newSrc);
                    mainImage.onload = () => {
                        mainImage.style.opacity = '1';
                    };
                }, 120);

                // Update active styles
                thumbs.forEach((t) => {
                    t.classList.remove('border-primary', 'ring-2', 'ring-primary');
                    t.classList.add('border-gray-200');
                });
                thumb.classList.add('border-primary', 'ring-2', 'ring-primary');
            });
        });
    })();

    // Rating selector
    const ratingStars = document.querySelectorAll('[data-rating]');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = 0;

    ratingStars.forEach((star, index) => {
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            ratingInput.value = selectedRating;
            updateStarDisplay();
        });

        star.addEventListener('mouseenter', () => {
            highlightStars(index + 1);
        });
    });

    const ratingSelector = document.getElementById('rating-selector');
    if (ratingSelector) {
        ratingSelector.addEventListener('mouseleave', () => {
            updateStarDisplay();
        });
    }

    function highlightStars(rating) {
        ratingStars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function updateStarDisplay() {
        if (selectedRating > 0) {
            highlightStars(selectedRating);
        } else {
            ratingStars.forEach((star) => {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            });
        }
    }

    // Review form submission
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!ratingInput.value) {
                showToast('Please select a rating', 'error');
                return;
            }

            const submitBtn = document.getElementById('submit-review-btn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const formData = new FormData(reviewForm);
                const response = await fetch(reviewForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.requires_auth) {
                        showToast('Please login to submit a review', 'info', 2000);
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 2000);
                        return;
                    }
                    throw new Error(data.message || 'Failed to submit review');
                }

                // Success!
                showToast(data.message || 'Review submitted successfully!', 'success');

                // Reset form
                reviewForm.reset();
                selectedRating = 0;
                ratingInput.value = '';
                updateStarDisplay();

                // Reload page after a short delay to show the new review
                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } catch (error) {
                console.error('Review submission error:', error);
                showToast(error.message || 'Failed to submit review. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
});
</script>
@endpush
