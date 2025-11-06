@extends('layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'JB Fresh Chicken and Frozen Food') . ' | Home')

@section('full-width')
    @php
        $tabs = collect([
            ['id' => 'all', 'label' => 'All'],
        ])->merge(
            $categoryTabs->map(fn ($category) => [
                'id' => $category->slug,
                'label' => $category->name,
            ])
        );
    @endphp

    <div class="space-y-24 py-10 md:py-10">
        <section class="relative" data-carousel data-carousel-autoplay="false">
            <div class="relative mx-auto max-w-6xl h-[22rem] md:h-[30rem] overflow-hidden rounded-3xl border border-gray-200 bg-gray-900 shadow-xl dark:border-gray-700">
                @foreach ($slides as $index => $slide)
                    <article
                        data-carousel-slide
                        class="absolute inset-0 h-full w-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 -z-10' }}"
                    >
                        <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/30"></div>
                        <div class="relative z-10 flex h-full flex-col justify-center gap-4 p-8 text-white sm:p-20">
                            <span class="inline-flex items-center gap-2 self-start rounded-full bg-white/20 px-4 py-1 text-xs font-semibold uppercase tracking-wider">
                                <span class="h-2 w-2 rounded-full bg-primary"></span> Fresh From The Kitchen
                            </span>
                            <h2 class="text-3xl font-extrabold sm:text-5xl">{{ $slide['title'] }}</h2>
                            <p class="max-w-xl text-base text-white/80 sm:text-lg">{{ $slide['subtitle'] }}</p>
                            <div>
                                <a href="{{ $slide['button_url'] }}" class="inline-flex items-center rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                                    {{ $slide['button_label'] }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.25 8.75L21 12m0 0l-3.75 3.25M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach

                <button type="button" data-carousel-prev class="hidden md:block absolute left-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/90 p-3 text-gray-800 shadow-lg transition hover:bg-white hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary">
                    <span class="sr-only">Previous</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 010 1.06L9.06 10l3.72 3.71a.75.75 0 11-1.06 1.06l-4.25-4.25a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" data-carousel-next class="hidden md:block absolute right-4 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/90 p-3 text-gray-800 shadow-lg transition hover:bg-white hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary">
                    <span class="sr-only">Next</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 010-1.06L10.94 10 7.2 6.29a.75.75 0 111.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2">
                    @foreach ($slides as $index => $slide)
                        <button type="button" data-carousel-indicator class="h-2 w-6 rounded-full transition-all duration-300 hover:w-8 cursor-pointer {{ $index === 0 ? 'bg-primary' : 'bg-white/60' }}">
                            <span class="sr-only">Go to slide {{ $index + 1 }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="space-y-20">
            <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Taste the Variety</p>
                        <h3 class="mt-2 text-3xl font-extrabold text-dark dark:text-white">Handpicked Hot Sellers</h3>
                    </div>
                    <a href="{{ url('/shop') }}" class="inline-flex items-center text-sm font-semibold text-primary transition hover:text-secondary">
                        View All Products &rarr;
                    </a>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                    @forelse ($featuredCategories as $category)
                        <a href="{{ url('/shop?category=' . $category['slug']) }}" class="group relative flex h-64 items-end overflow-hidden rounded-2xl shadow-lg">
                            <img src="{{ $category['image'] ?? $placeholderImage }}" alt="{{ $category['name'] }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <div class="relative z-10 p-6 text-white">
                                <h4 class="text-2xl font-bold">{{ $category['name'] }}</h4>
                                <span class="mt-2 inline-flex items-center text-sm font-semibold">Shop Now &rarr;</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">New categories coming soon. Stay tuned!</p>
                    @endforelse
                </div>
            </section>

            <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">Explore the Menu</p>
                    <h3 class="mt-2 text-3xl font-extrabold text-dark dark:text-white">Our Crowd Favourites</h3>
                </div>
                <div class="mt-10" data-tab-group data-tab-initial="all">
                    <div class="flex flex-wrap justify-center gap-3">
                        @foreach ($tabs as $tab)
                            <button
                                type="button"
                                data-tab-trigger
                                data-tab-target="{{ $tab['id'] }}"
                                class="rounded-full border border-gray-200 px-6 py-2 text-sm font-semibold text-gray-700 transition hover:border-primary hover:text-primary dark:border-gray-700 dark:text-gray-300"
                            >
                                {{ $tab['label'] }}
                            </button>
                        @endforeach
                    </div>

                    @foreach ($tabs as $tab)
                        @php
                            $products = $tabbedProducts[$tab['id']] ?? collect();
                        @endphp
                        <div
                            data-tab-panel
                            data-tab-id="{{ $tab['id'] }}"
                            class="mt-10 {{ $loop->first ? '' : 'hidden' }}"
                        >
                            @if ($products->isNotEmpty())
                                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    @foreach ($products as $product)
                                        @include('components.product.card', ['product' => $product, 'placeholderImage' => $placeholderImage])
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">We are adding delicious new items for this category. Check back soon!</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            @if ($dealProduct)
                <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="overflow-hidden rounded-3xl bg-amber-50 shadow-xl dark:bg-gray-900/70">
                        <div class="grid grid-cols-1 gap-0 md:grid-cols-2">
                            <div class="order-2 flex flex-col justify-center p-8 md:order-1 md:p-14">
                                <span class="text-sm font-semibold uppercase tracking-widest text-secondary">Deal of the Day</span>
                                <h3 class="mt-3 text-3xl font-extrabold text-dark dark:text-white">{{ $dealProduct->name }}</h3>
                                <p class="mt-4 text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($dealProduct->description, 160) }}</p>
                                <div class="mt-6 flex items-end gap-3">
                                    <span class="text-4xl font-bold text-primary">PKR {{ number_format((float) $dealProduct->price, 2) }}</span>
                                    @if ($dealProduct->compare_at_price)
                                        <span class="text-lg text-gray-500 line-through">PKR {{ number_format((float) $dealProduct->compare_at_price, 2) }}</span>
                                    @endif
                                </div>
                                <a href="{{ url('/product/' . ($dealProduct->slug ?? $dealProduct->id)) }}" class="mt-8 inline-flex items-center self-start rounded-full bg-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-secondary">
                                    Order Now
                                </a>
                            </div>
                            <div class="order-1 md:order-2">
                                <img src="{{ $dealProduct->image_url ?? $placeholderImage }}" alt="{{ $dealProduct->name }}" class="h-64 w-full object-cover md:h-full" loading="lazy">
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if ($hotProducts->isNotEmpty())
                <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 text-center">
                        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Feel the Heat</p>
                        <h3 class="mt-2 text-3xl font-extrabold text-dark dark:text-white">Hot &amp; Spicy Picks</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ($hotProducts as $product)
                            @include('components.product.card', ['product' => $product, 'placeholderImage' => $placeholderImage])
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 md:grid-cols-3">
                    <article class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                <path fill-rule="evenodd" d="M11.54 2.47a.75.75 0 01.92 0l7.5 5.5a.75.75 0 01-.44 1.35H4.48a.75.75 0 01-.44-1.35l7.5-5.5zM3.75 10.25a.75.75 0 01.75-.75h15a.75.75 0 01.75.75v8a3 3 0 01-3 3H6.75a3 3 0 01-3-3v-8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-xl font-bold text-dark dark:text-white">Quality Ingredients</h4>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Only the freshest all-white meat chicken, seasoned to perfection and cooked crispy every time.</p>
                    </article>
                    <article class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-xl font-bold text-dark dark:text-white">Fast &amp; Friendly</h4>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Lightning-fast prep and delivery so your cravings are satisfied while they’re hot.</p>
                    </article>
                    <article class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                <path fill-rule="evenodd" d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.21 5.21 0 0112 5.052 5.21 5.21 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.011-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.002z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h4 class="mt-5 text-xl font-bold text-dark dark:text-white">Loved by Families</h4>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Shareable meals, family bundles, and flavor combos crafted to please every palate.</p>
                    </article>
                </div>
            </section>

            <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-secondary p-10 text-center text-white shadow-xl">
                    <h3 class="text-3xl font-extrabold">Need a Dinner Idea?</h3>
                    <p class="mt-3 text-white/80">Let our kitchen muse inspire tonight’s feast. Tell us your vibe and we’ll suggest the perfect combo.</p>
                    <form data-meal-idea-form action="{{ route('meal-ideas.generate') }}" method="POST" class="mt-6 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                        @csrf
                        <label for="meal-mood" class="sr-only">What mood are you in?</label>
                        <input type="text" id="meal-mood" name="mood" placeholder="Chill movie night, game day, cozy in..." class="w-full rounded-full border border-white/30 bg-white/10 px-5 py-3 text-sm text-white placeholder-white/70 focus:border-white focus:outline-none focus:ring-2 focus:ring-white sm:max-w-sm">
                        <button type="submit" data-meal-idea-button class="inline-flex items-center rounded-full bg-dark px-6 py-3 text-sm font-semibold uppercase tracking-widest text-white transition hover:bg-gray-900">
                            Get Meal Idea
                        </button>
                    </form>
                    <div data-meal-idea-result class="mt-6 text-sm font-medium text-white/90"></div>
                </div>
            </section>

            @if ($testimonials->isNotEmpty())
                <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-8 text-center">
                        <p class="text-sm font-semibold uppercase tracking-widest text-primary">What Customers Say</p>
                        <h3 class="mt-2 text-3xl font-extrabold text-dark dark:text-white">Real People. Real Crunch.</h3>
                    </div>
                    <div class="grid gap-6 md:grid-cols-3">
                        @foreach ($testimonials as $testimonial)
                            @include('components.review.testimonial-card', ['testimonial' => $testimonial])
                        @endforeach
                    </div>
                </section>
            @endif

            @if (($siteSettings['blog_enabled'] ?? false) && $latestPosts->isNotEmpty())
                <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-10 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-primary">From the Blog</p>
                            <h3 class="mt-2 text-3xl font-extrabold text-dark dark:text-white">Fresh Off the Press</h3>
                        </div>
                        <a href="{{ url('/blog') }}" class="inline-flex items-center text-sm font-semibold text-primary transition hover:text-secondary">
                            Read All Stories &rarr;
                        </a>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($latestPosts as $post)
                            @include('components.blog.post-card', ['post' => $post])
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($siteSettings['newsletter_enabled'] ?? false)
                <section class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white p-10 text-center shadow-lg dark:border-gray-700 dark:bg-gray-800 sm:p-16">
                        <div class="mx-auto max-w-2xl">
                            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-primary">
                                Insider Perks
                            </span>
                            <h3 class="mt-4 text-3xl font-extrabold text-dark dark:text-white">Stay in the Crispy Loop</h3>
                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Subscribe for exclusive deals, limited-time drops, and mouth-watering menu additions.</p>
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                                @csrf
                                <label for="newsletter-email" class="sr-only">Email address</label>
                                <input type="email" id="newsletter-email" name="email" placeholder="you@example.com" required class="w-full rounded-full border border-gray-300 px-5 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary dark:border-gray-600 dark:bg-gray-900 dark:text-white sm:max-w-sm">
                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-sm font-semibold uppercase tracking-widest text-white transition hover:bg-secondary">
                                    Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

