<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display user's orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, $id)
    {
        $order = $request->user()
            ->orders()
            ->with(['items.product.images'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Create a new order from cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'nullable|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'nullable|string',
            'shipping_country' => 'required|string',
            'shipping_zip' => 'required|string',
            'billing_name' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'billing_city' => 'nullable|string',
            'billing_state' => 'nullable|string',
            'billing_country' => 'nullable|string',
            'billing_zip' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        // Get user's cart
        $cart = Cart::where('user_id', $request->user()->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Calculate totals
        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $tax = $subtotal * 0.10; // 10% tax
        $shipping = 5.00; // Flat rate
        $total = $subtotal + $tax + $shipping;

        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $request->user()->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'discount' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'shipping_name' => $validated['shipping_name'],
            'shipping_email' => $validated['shipping_email'],
            'shipping_phone' => $validated['shipping_phone'] ?? null,
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_state' => $validated['shipping_state'] ?? null,
            'shipping_country' => $validated['shipping_country'],
            'shipping_zip' => $validated['shipping_zip'],
            'billing_name' => $validated['billing_name'] ?? null,
            'billing_address' => $validated['billing_address'] ?? null,
            'billing_city' => $validated['billing_city'] ?? null,
            'billing_state' => $validated['billing_state'] ?? null,
            'billing_country' => $validated['billing_country'] ?? null,
            'billing_zip' => $validated['billing_zip'] ?? null,
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);

        // Create order items from cart
        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'product_sku' => $cartItem->product->sku,
                'product_description' => $cartItem->product->description,
                'product_image' => $cartItem->product->image_url,
                'price' => $cartItem->price,
                'quantity' => $cartItem->quantity,
                'total' => $cartItem->price * $cartItem->quantity,
            ]);
        }

        // Clear cart after order
        $cart->items()->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('items'),
        ], 201);
    }

    /**
     * Update order status (Admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'shipped') {
            $order->update(['shipped_at' => now()]);
        } elseif ($validated['status'] === 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order,
        ]);
    }
}

