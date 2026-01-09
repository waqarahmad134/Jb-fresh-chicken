<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\NewsletterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

// DEBUG: Test if routes are even being loaded
Route::get('/test-route', function () {
    return 'ROUTES ARE WORKING!';
});

// Development route: Run migrate:fresh and db:seed
// WARNING: Only use in development! This will wipe your database.
Route::get('/dev/reset-db', function () {
    if (!app()->environment('local')) {
        abort(403, 'This route is only available in local environment');
    }
    
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh');
    \Illuminate\Support\Facades\Artisan::call('db:seed');
    
    return response()->json([
        'success' => true,
        'message' => 'Database has been reset and seeded successfully',
        'migrate' => 'migrate:fresh completed',
        'seed' => 'db:seed completed'
    ]);
})->name('dev.reset-db');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/meal-ideas', [HomeController::class, 'generateMealIdea'])->name('meal-ideas.generate');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Account Routes (Protected)
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Account\AccountController::class, 'profile'])->name('profile');
    Route::get('/details', [\App\Http\Controllers\Account\AccountController::class, 'details'])->name('details');
    Route::get('/orders', [\App\Http\Controllers\Account\AccountController::class, 'orders'])->name('orders');
    Route::get('/wishlist', [\App\Http\Controllers\Account\AccountController::class, 'wishlist'])->name('wishlist');
});

// Shop Routes
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Shop\ShopController::class, 'index'])->name('index');
    Route::get('/cart', [\App\Http\Controllers\Shop\CartController::class, 'index'])->name('cart');
    
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [\App\Http\Controllers\Shop\CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/order-success', [\App\Http\Controllers\Shop\CheckoutController::class, 'success'])->name('order-success');
    });
});

// Product detail route (used by product cards and links)
Route::get('/product/{slug}', [\App\Http\Controllers\Shop\ShopController::class, 'show'])->name('product.show');

// Review route (protected)
Route::post('/product/{slug}/review', [\App\Http\Controllers\Shop\ReviewController::class, 'store'])->name('product.review')->middleware('auth');

// Wishlist toggle (AJAX)
Route::post('/wishlist/toggle', [\App\Http\Controllers\Shop\WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');

// Cart actions (AJAX)
Route::post('/cart/add', [\App\Http\Controllers\Shop\CartController::class, 'addItem'])->name('cart.add')->middleware('auth');
Route::put('/cart/items/{itemId}', [\App\Http\Controllers\Shop\CartController::class, 'updateItem'])->name('cart.update')->middleware('auth');
Route::delete('/cart/items/{itemId}', [\App\Http\Controllers\Shop\CartController::class, 'removeItem'])->name('cart.remove')->middleware('auth');
Route::get('/cart/data', [\App\Http\Controllers\Shop\CartController::class, 'getCartData'])->name('shop.cart.data')->middleware('auth');

// Content Pages Routes
// Static pages (must come before dynamic page route)
Route::get('/about', [\App\Http\Controllers\Content\PageController::class, 'about'])->name('about');
Route::get('/contact', [\App\Http\Controllers\Content\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Content\PageController::class, 'contactStore'])->name('contact.store');
Route::get('/privacy', [\App\Http\Controllers\Content\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [\App\Http\Controllers\Content\PageController::class, 'terms'])->name('terms');

// Blog routes (must come before dynamic page route)
Route::get('/blog', [\App\Http\Controllers\Content\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\Content\BlogController::class, 'show'])->name('blog.show');

// Admin Routes (restricted to admins) - MUST come before dynamic page route
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('product-categories', \App\Http\Controllers\Admin\ProductCategoryController::class)->parameters(['product-categories' => 'category']);
    Route::post('product-categories/update-sort-order', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'updateSortOrder'])->name('product-categories.update-sort-order');
    
    // Orders
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    
    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Blog
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);
    Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->parameters(['blog-categories' => 'blogCategory']);
    
    // Home Banners
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);

    // Tags
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
    
    // Pages
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);

    // Media Library (filesystem-based, no database)
    Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
    Route::get('/media/all', [\App\Http\Controllers\Admin\MediaController::class, 'getAll'])->name('media.all');
    Route::get('/media/{filename}', [\App\Http\Controllers\Admin\MediaController::class, 'show'])->name('media.show')->where('filename', '[^/]+');
    Route::put('/media/{filename}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('media.update')->where('filename', '[^/]+');
    Route::post('/media/{filename}/delete', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy.post')->where('filename', '[^/]+');
    Route::delete('/media/{filename}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy')->where('filename', '[^/]+');

    // Database Management
    Route::get('/database', [\App\Http\Controllers\Admin\DatabaseController::class, 'index'])->name('database.index');
    Route::get('/database/download', [\App\Http\Controllers\Admin\DatabaseController::class, 'download'])->name('database.download');
    Route::get('/database/clear', [\App\Http\Controllers\Admin\DatabaseController::class, 'clear'])->name('database.clear');
    Route::post('/database/clear', [\App\Http\Controllers\Admin\DatabaseController::class, 'clear'])->name('database.clear.store');
    Route::get('/database/{table}', [\App\Http\Controllers\Admin\DatabaseController::class, 'show'])->name('database.show')->where('table', '[a-z0-9_]+');

    // Backup Management
    Route::get('/backup', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/files', [\App\Http\Controllers\Admin\BackupController::class, 'createFilesBackup'])->name('backup.files.create');
    Route::get('/backup/download/complete', [\App\Http\Controllers\Admin\BackupController::class, 'downloadCompleteProject'])->name('backup.download.complete');
    Route::get('/backup/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'downloadFilesBackup'])->name('backup.download.files')->where('filename', '[a-z0-9._-]+');
    Route::delete('/backup/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'deleteBackup'])->name('backup.delete')->where('filename', '[a-z0-9._-]+');

    // Payment Gateways
    Route::get('/payment-gateways', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::put('/payment-gateways/{gateway}', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'update'])->name('payment-gateways.update')->where('gateway', '[a-z_]+');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [AdminSettingsController::class, 'general'])->name('general');
        Route::post('/general', [AdminSettingsController::class, 'updateGeneral'])->name('general.update');
        
        
        Route::get('/product', [AdminSettingsController::class, 'product'])->name('product');
        Route::post('/product', [AdminSettingsController::class, 'updateProduct'])->name('product.update');
        
        Route::get('/robots', [AdminSettingsController::class, 'robots'])->name('robots');
        Route::post('/robots', [AdminSettingsController::class, 'updateRobots'])->name('robots.update');
    });
});

// API Routes (must come before dynamic page route)
Route::get('/api/health', function () {
    return response()->json(['status' => 'ok']);
});
Route::get('/api/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show'])->name('api.products.show');

// Dynamic pages (created from admin panel) - MUST be last to catch all other slugs
Route::get('/{slug}', [\App\Http\Controllers\Content\PageController::class, 'show'])->name('page.show')
    ->where('slug', '[a-z0-9-]+');
