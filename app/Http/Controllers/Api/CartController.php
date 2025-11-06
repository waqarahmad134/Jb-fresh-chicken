<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart.
     */
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        
        $cart->load(['items.product.images']);

        $cartData = [
            'id' => $cart->id,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'image_url' => $item->product->image_url,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ];
            }),
            'subtotal' => $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            }),
        ];

        return response()->json($cartData);
    }

    /**
     * Add item to cart.
     */
    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request);
        $product = Product::findOrFail($validated['product_id']);

        // Check if item already exists in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart',
            'cart_item' => $cartItem->load('product.images'),
        ], 201);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request);
        $cartItem = $cart->items()->findOrFail($itemId);

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Cart item updated',
            'cart_item' => $cartItem->load('product.images'),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(Request $request, $itemId)
    {
        $cart = $this->getOrCreateCart($request);
        $cartItem = $cart->items()->findOrFail($itemId);
        
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Clear entire cart.
     */
    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();

        return response()->json(['message' => 'Cart cleared']);
    }

    /**
     * Get or create cart for user/session.
     */
    private function getOrCreateCart(Request $request)
    {
        if ($request->user()) {
            // Get or create cart for authenticated user
            $cart = Cart::firstOrCreate([
                'user_id' => $request->user()->id,
            ]);
        } else {
            // Get or create cart for guest WITHOUT using session middleware
            // Prefer a client-provided guest id header; otherwise derive a stable fingerprint
            $sessionId = $request->header('X-Guest-Id');
            if (!$sessionId) {
                $fingerprintSource = ($request->ip() ?? '0.0.0.0') . '|' . ($request->userAgent() ?? '');
                $sessionId = sha1($fingerprintSource);
            }
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }

        return $cart;
    }
}

