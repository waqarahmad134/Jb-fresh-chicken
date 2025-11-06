<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the about page.
     */
    public function about(): View
    {
        $page = Page::where('slug', 'about')->where('is_published', true)->first();

        return view('pages.content.about', compact('page'));
    }

    /**
     * Display the contact page.
     */
    public function contact(): View
    {
        return view('pages.content.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // In a real app, you'd send an email or store in a database
        // For now, just return success

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        $page = Page::where('slug', 'privacy')->where('is_published', true)->first();

        return view('pages.content.privacy', compact('page'));
    }

    /**
     * Display the terms of service page.
     */
    public function terms(): View
    {
        $page = Page::where('slug', 'terms')->where('is_published', true)->first();

        return view('pages.content.terms', compact('page'));
    }

    /**
     * Display a dynamic page by slug.
     * This handles pages created from the admin panel.
     */
    public function show(string $slug): View
    {
        // Reserved slugs that should not be used as dynamic pages
        // These are handled by specific routes or should return 404
        $reservedSlugs = [
            'admin', 'api', 'shop', 'account', 'login', 'register', 'logout', 
            'forgot-password', 'product', 'blog', 'cart', 'checkout', 
            'order-success', 'wishlist', 'meal-ideas', 'newsletter',
            'about', 'contact', 'privacy', 'terms' // Static pages
        ];

        if (in_array($slug, $reservedSlugs)) {
            abort(404);
        }

        // Find the page in database
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('pages.content.show', compact('page'));
    }
}

