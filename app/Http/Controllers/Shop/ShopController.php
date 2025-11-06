<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display the shop listing page with filters.
     */
    public function index(Request $request): View
    {
        $query = Product::query()->where('is_active', true)->with(['images', 'category', 'tags']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categorySlug = $request->input('category')) {
            if ($categorySlug !== 'all') {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }
        }

        // Price filter
        if ($maxPrice = $request->input('price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Tags filter
        if ($tags = $request->input('tags')) {
            $tagSlugs = is_array($tags) ? $tags : explode(',', $tags);
            $query->whereHas('tags', function ($q) use ($tagSlugs) {
                $q->whereIn('slug', $tagSlugs);
            }, '=', count($tagSlugs));
        }

        // Sorting
        $sortBy = $request->input('sort', 'default');
        match ($sortBy) {
            'price-asc' => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'name-asc' => $query->orderBy('name', 'asc'),
            'name-desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('sort_order'),
        };

        $products = $query->with(['reviews' => function ($q) {
            $q->where('is_approved', true);
        }])->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $tags = Tag::whereHas('products')->get();
        $maxPriceAvailable = Product::where('is_active', true)->max('price') ?? 100;
        
        // Get total products count (before pagination)
        $totalProducts = Product::where('is_active', true)->count();

        return view('pages.shop.index', compact('products', 'categories', 'tags', 'maxPriceAvailable', 'totalProducts'));
    }

    /**
     * Display the product detail page.
     */
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'category', 'tags', 'reviews' => function ($query) {
                $query->where('is_approved', true)->with('user');
            }])
            ->firstOrFail();

        // Check if current user has already reviewed this product
        $userHasReviewed = false;
        if (auth()->check()) {
            $userHasReviewed = \App\Models\Review::where('product_id', $product->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        $relatedProducts = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['images', 'category', 'reviews' => function ($query) {
                $query->where('is_approved', true);
            }])
            ->take(4)
            ->get();

        return view('pages.shop.product-detail', compact('product', 'relatedProducts', 'userHasReviewed'));
    }
}

