<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(): View
    {
        $cart = null;
        $cartItems = collect();
        $cartTotal = 0;
        $cartCount = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->with(['items.product.images'])->first();
            if ($cart) {
                $cartItems = $cart->items;
                $cartTotal = $cart->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                });
                $cartCount = $cart->items->sum('quantity');
            }
        }

        return view('pages.shop.cart', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Add item to cart (AJAX).
     */
    public function addItem(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['nullable', 'integer', 'min:1', 'max:10'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to cart.',
                'requires_auth' => true,
            ], 401);
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $product = Product::findOrFail($validated['product_id']);
        $quantity = $validated['quantity'] ?? 1;

        // Check if item already exists in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        $cartCount = $cart->items()->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => "{$product->name} added to cart!",
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Update cart item quantity (AJAX).
     */
    public function updateItem(Request $request, $itemId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update cart items.',
                'requires_auth' => true,
            ], 401);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $cartItem = CartItem::where('id', $itemId)
            ->whereHas('cart', fn ($query) => $query->where('user_id', Auth::id()))
            ->firstOrFail();

        $cartItem->update(['quantity' => $validated['quantity']]);

        $cart = Cart::where('user_id', Auth::id())->first();
        $cartTotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $cartCount = $cart->items->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'item_total' => $cartItem->price * $cartItem->quantity,
            'cart_total' => $cartTotal,
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Remove item from cart (AJAX).
     */
    public function removeItem($itemId): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to remove cart items.',
                'requires_auth' => true,
            ], 401);
        }

        $cartItem = CartItem::where('id', $itemId)
            ->whereHas('cart', fn ($query) => $query->where('user_id', Auth::id()))
            ->firstOrFail();

        $productName = $cartItem->product->name;
        $cartItem->delete();

        $cart = Cart::where('user_id', Auth::id())->first();
        $cartTotal = $cart ? $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        }) : 0;
        $cartCount = $cart ? $cart->items->sum('quantity') : 0;

        return response()->json([
            'success' => true,
            'message' => "{$productName} removed from cart",
            'cart_total' => $cartTotal,
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Get cart data for drawer (AJAX).
     */
    public function getCartData(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view cart.',
                'requires_auth' => true,
            ], 401);
        }

        $cart = Cart::where('user_id', Auth::id())->with(['items.product.images'])->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => true,
                'cart_items' => [],
                'cart_total' => 0,
                'cart_count' => 0,
                'is_empty' => true,
            ]);
        }

        $cartItems = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_image' => $item->product->image_url ?? ($item->product->images->first()->image_url ?? null),
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->price * $item->quantity,
            ];
        });

        $cartTotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $cartCount = $cart->items->sum('quantity');

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal,
            'cart_count' => $cartCount,
            'is_empty' => false,
        ]);
    }
}

