<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the storefront home page.
     */
    public function index()
    {
        $placeholderImage = 'https://picsum.photos/seed/jbfreshchicken/800/600';

        $slides = [
            [
                'image' => 'https://picsum.photos/id/1060/1600/900',
                'title' => 'Crispy, Juicy, Irresistible',
                'subtitle' => 'Taste the crunch that started a revolution. Order your favorites now!',
                'button_label' => 'Shop All Products',
                'button_url' => url('/shop'),
            ],
            [
                'image' => 'https://picsum.photos/id/219/1600/900',
                'title' => 'Family Feasts Made Easy',
                'subtitle' => 'Get a bucket to share with your loved ones. Perfect for any occasion.',
                'button_label' => 'See Family Meals',
                'button_url' => url('/shop'),
            ],
            [
                'image' => 'https://picsum.photos/id/103/1600/900',
                'title' => 'Feeling Spicy?',
                'subtitle' => 'Turn up the heat with our famous Buffalo Wings and Spicy Strips.',
                'button_label' => 'Bring the Heat',
                'button_url' => url('/shop'),
            ],
        ];

        $featuredCategories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->with('images')
                    ->orderBy('sort_order')
                    ->take(1);
            }])
            ->take(3)
            ->get()
            ->map(function (Category $category) use ($placeholderImage) {
                $heroProduct = $category->products->first();

                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'image' => $category->image_url
                        ?: ($heroProduct?->image_url ?? $heroProduct?->image_urls[0] ?? $placeholderImage),
                ];
            });

        $bestSellerProducts = Product::query()
            ->where('is_active', true)
            ->with(['images', 'category', 'reviews' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->whereHas('tags', function ($query) {
                $query->whereIn('slug', ['best-seller', 'best-sellers', 'bestseller']);
            })
            ->take(8)
            ->get();

        if ($bestSellerProducts->count() < 8) {
            $bestSellerProducts = $bestSellerProducts->merge(
                Product::query()
                    ->where('is_active', true)
                    ->whereNotIn('id', $bestSellerProducts->pluck('id'))
                    ->with(['images', 'category', 'reviews' => function ($q) {
                        $q->where('is_approved', true);
                    }])
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('sort_order')
                    ->take(8 - $bestSellerProducts->count())
                    ->get()
            );
        }

        $categoryTabs = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderByDesc('products_count')
            ->take(4)
            ->get();

        $tabbedProducts = [
            'all' => $bestSellerProducts->take(8),
        ];

        foreach ($categoryTabs as $category) {
            $tabbedProducts[$category->slug] = Product::query()
                ->where('is_active', true)
                ->where('category_id', $category->id)
                ->with(['images', 'category', 'reviews' => function ($q) {
                    $q->where('is_approved', true);
                }])
                ->orderBy('sort_order')
                ->take(8)
                ->get();
        }

        $dealProduct = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['images', 'category', 'reviews' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->orderBy('sort_order')
            ->first();

        if (! $dealProduct) {
            $dealProduct = Product::query()
                ->where('is_active', true)
                ->with(['images', 'category', 'reviews' => function ($q) {
                    $q->where('is_approved', true);
                }])
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $hotProducts = Product::query()
            ->where('is_active', true)
            ->with(['images', 'category', 'reviews' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->whereHas('tags', function ($query) {
                $query->whereIn('slug', ['spicy', 'hot']);
            })
            ->take(4)
            ->get();

        $testimonials = Review::query()
            ->with(['user', 'product'])
            ->where('is_approved', true)
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $latestPosts = BlogPost::query()
            ->with(['author', 'blogCategory'])
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('pages.home.index', [
            'slides' => $slides,
            'featuredCategories' => $featuredCategories,
            'categoryTabs' => $categoryTabs,
            'tabbedProducts' => $tabbedProducts,
            'dealProduct' => $dealProduct,
            'hotProducts' => $hotProducts,
            'testimonials' => $testimonials,
            'latestPosts' => $latestPosts,
            'placeholderImage' => $placeholderImage,
        ]);
    }

    /**
     * Provide a simple AI-inspired meal idea.
     */
    public function generateMealIdea(Request $request): JsonResponse
    {
        $request->validate([
            'mood' => ['nullable', 'string', 'max:50'],
        ]);

        $mains = [
            'Crispy chicken sandwich with spicy mayo',
            'Buttermilk fried chicken tenders',
            'Zesty buffalo wings platter',
            'Honey-garlic glazed chicken bites',
            'Smoky grilled chicken skewers',
        ];

        $sides = [
            'truffle parmesan fries',
            'garlic-butter sweet corn',
            'loaded mac and cheese',
            'buttermilk biscuits with honey butter',
            'sweet potato wedges with chipotle aioli',
        ];

        $extras = [
            'house-made coleslaw for crunch',
            'a refreshing cucumber-lime salad',
            'pickled jalapeños for an extra kick',
            'warm cornbread drizzled with maple butter',
            'a chilled peach iced tea to balance the heat',
        ];

        $main = $mains[array_rand($mains)];
        $side = $sides[array_rand($sides)];
        $extra = $extras[array_rand($extras)];

        $mood = $request->string('mood')->trim();

        $idea = "How about {$main}, paired with {$side}, and {$extra}?";

        if ($mood !== '') {
            $idea .= " It’s a perfect match for a {$mood} kind of evening.";
        }

        return response()->json([
            'idea' => $idea,
        ]);
    }
}

