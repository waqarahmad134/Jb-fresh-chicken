<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Display the user's profile dashboard.
     */
    public function profile(): View
    {
        $user = Auth::user();
        return view('pages.account.profile', compact('user'));
    }

    /**
     * Display the user's account details.
     */
    public function details(): View
    {
        $user = Auth::user();
        return view('pages.account.details', compact('user'));
    }

    /**
     * Display the user's order history.
     */
    public function orders(): View
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->get();

        return view('pages.account.orders', compact('orders'));
    }

    /**
     * Display the user's wishlist.
     */
    public function wishlist(): View
    {
        $wishlistItems = Wishlist::query()
            ->where('user_id', Auth::id())
            ->with(['product.images', 'product.category'])
            ->get()
            ->pluck('product')
            ->filter();

        return view('pages.account.wishlist', compact('wishlistItems'));
    }
}

