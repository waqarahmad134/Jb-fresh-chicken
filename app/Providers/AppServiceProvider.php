<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only load database-dependent data if not in console and tables exist
        if (! $this->app->runningInConsole() && $this->tablesExist()) {
            try {
                $siteSettings = Cache::remember('site.settings', 3600, function () {
                    $defaults = $this->getDefaultSettings();
                    try {
                        $dbSettings = Setting::query()
                            ->get()
                            ->mapWithKeys(function ($setting) {
                                try {
                                    $value = match ($setting->type) {
                                        'boolean' => (bool) $setting->value,
                                        'integer' => (int) $setting->value,
                                        'json' => json_decode($setting->value, true) ?? $setting->value,
                                        default => $setting->value,
                                    };

                                    return [$setting->key => $value];
                                } catch (\Exception $e) {
                                    // Skip invalid settings
                                    return [];
                                }
                            })
                            ->filter()
                            ->toArray();
                        
                        // Merge defaults with database settings (DB settings take precedence)
                        return array_merge($defaults, $dbSettings);
                    } catch (\Exception $e) {
                        // If settings query fails, return defaults only
                        return $defaults;
                    }
                });

                View::share('siteSettings', $siteSettings);
            } catch (\Exception $e) {
                // Log error for debugging but don't break the app
                \Log::error('Failed to load site settings: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);
                // Fallback to default settings if database is not available
                View::share('siteSettings', $this->getDefaultSettings());
            }

            View::composer('partials.header', function ($view) {
                try {
                    $user = Auth::user();

                    $cartItemCount = 0;
                    $wishlistCount = 0;

                    if ($user) {
                        $cartItemCount = CartItem::query()
                            ->whereHas('cart', fn ($query) => $query->where('user_id', $user->id))
                            ->sum('quantity');

                        $wishlistCount = Wishlist::query()
                            ->where('user_id', $user->id)
                            ->count();
                    }

                    $view->with([
                        'cartItemCount' => $cartItemCount,
                        'wishlistCount' => $wishlistCount,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to load cart/wishlist counts: ' . $e->getMessage());
                    $view->with([
                        'cartItemCount' => 0,
                        'wishlistCount' => 0,
                    ]);
                }
            });

            View::composer('partials.footer', function ($view) {
                try {
                    $footerPages = Page::query()
                        ->whereIn('slug', ['about', 'contact', 'privacy', 'terms'])
                        ->where('is_published', true)
                        ->get(['slug', 'title'])
                        ->keyBy('slug');

                    $view->with('footerPages', $footerPages);
                } catch (\Exception $e) {
                    \Log::error('Failed to load footer pages: ' . $e->getMessage());
                    $view->with('footerPages', collect());
                }
            });
        } else {
            // Provide default values when database is not available
            View::share('siteSettings', $this->getDefaultSettings());
        }
    }

    /**
     * Check if required database tables exist.
     */
    private function tablesExist(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('settings');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get default site settings.
     */
    private function getDefaultSettings(): array
    {
        return [
            'site_name' => config('app.name', "JB Fresh Chicken"),
            'site_logo' => '/images/logo.png',
            'site_logo_dark' => '/images/logo-dark.png',
            'site_tagline' => 'Fresh Chicken and Frozen Food',
            'meta_description' => 'Order fresh chicken and frozen food from JB Fresh Chicken. Quality products delivered to your door.',
            'contact_email' => 'info@jbfreshchicken.com',
            'contact_phone' => '+1 (555) 123-4567',
            'facebook_url' => '#',
            'twitter_url' => '#',
            'instagram_url' => '#',
            'wishlist_enabled' => true,
            'reviews_enabled' => true,
            'newsletter_enabled' => true,
            'quick_view_enabled' => false,
            'product_card_style' => 'style1',
            'blog_enabled' => false,
            'add_to_cart_behavior' => 'page',
        ];
    }
}
