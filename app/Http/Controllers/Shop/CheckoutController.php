<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', Auth::id())->with(['items.product'])->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $cartItems = $cart->items;
        $cartTotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('pages.shop.checkout', compact('cartItems', 'cartTotal', 'user'));
    }

    /**
     * Process the checkout and create an order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:card,cod'],
            'card_number' => ['required_if:payment_method,card', 'nullable', 'string'],
            'expiry' => ['required_if:payment_method,card', 'nullable', 'string'],
            'cvc' => ['required_if:payment_method,card', 'nullable', 'string'],
        ]);

        $cart = Cart::where('user_id', Auth::id())->with(['items.product'])->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        DB::transaction(function () use ($cart, $validated) {
            $total = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'CG-'.strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $total,
                'tax' => 0,
                'shipping' => 0,
                'total' => $total,
                'shipping_name' => $validated['name'],
                'shipping_email' => $validated['email'],
                'shipping_phone' => $validated['phone'] ?? null,
                'shipping_address' => $validated['address'],
                'shipping_city' => $validated['city'],
                'shipping_state' => $validated['state'] ?? null,
                'shipping_country' => $validated['country'],
                'shipping_zip' => $validated['zip'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $product->name ?? 'Unknown Product',
                    'product_sku' => $product->sku ?? null,
                    'product_description' => $product->description ?? null,
                    'product_image' => $product->image_url ?? null,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->price * $cartItem->quantity,
                ]);
            }

            $cart->items()->delete();
        });

        return redirect()->route('shop.order-success')->with('success', 'Order placed successfully!');
    }

    /**
     * Display the order success page.
     */
    public function success(): View
    {
        return view('pages.shop.order-success');
    }
}

