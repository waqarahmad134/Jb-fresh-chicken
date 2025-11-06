<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product.
     */
    public function store(Request $request, string $productSlug): JsonResponse|RedirectResponse
    {
        $product = Product::where('slug', $productSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to submit a review.',
                    'requires_auth' => true,
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Please login to submit a review.');
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product.',
                ], 422);
            }

            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check if user has purchased this product (for verified purchase badge)
        $hasPurchased = \App\Models\OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', Auth::id())
                ->where('payment_status', 'paid');
        })
        ->where('product_id', $product->id)
        ->exists();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => $hasPurchased,
            'is_approved' => true, // Auto-approve for now, can be changed to require moderation
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'review' => $review->load('user'),
            ]);
        }

        return back()->with('success', 'Review submitted successfully!');
    }
}

