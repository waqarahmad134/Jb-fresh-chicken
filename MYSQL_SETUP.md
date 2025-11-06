# MySQL Setup for Cluck 'n' Go Backend

## All Models Are Already Created! ✅

Location: `backend/app/Models/`

### Complete List of Models:

1. **User.php** - Extended with e-commerce fields (addresses, admin status)
2. **Category.php** - Product categories
3. **Product.php** - Products with prices, inventory
4. **ProductImage.php** - Multiple images per product
5. **Tag.php** - Tags for products and blog posts
6. **Review.php** - Customer product reviews
7. **BlogCategory.php** - Blog post categories
8. **BlogPost.php** - Blog articles with views tracking
9. **Page.php** - Static pages (About, Privacy, Terms)
10. **Order.php** - Customer orders
11. **OrderItem.php** - Items in orders
12. **Cart.php** - Shopping carts
13. **CartItem.php** - Items in carts
14. **Wishlist.php** - User wishlist items
15. **Setting.php** - Site configuration
16. **Coupon.php** - Discount coupons
17. **NewsletterSubscriber.php** - Email subscribers
18. **ContactMessage.php** - Contact form messages

## MySQL Configuration

### Step 1: Configure .env File

Edit `backend/.env` and update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cluckngo
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### Step 2: Create MySQL Database

Open MySQL command line or phpMyAdmin:

```bash
# Using MySQL command line
mysql -u root -p
```

Then run:
```sql
CREATE DATABASE cluckngo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

Or if using XAMPP phpMyAdmin:
1. Open http://localhost/phpmyadmin
2. Click "New" in the left sidebar
3. Database name: `cluckngo`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 3: Run Migrations

Navigate to backend folder:
```bash
cd backend
```

Run migrations to create all tables:
```bash
php artisan migrate
```

You should see output like:
```
2014_10_12_000000_create_users_table ..................... DONE
2014_10_12_100000_create_password_reset_tokens_table ..... DONE
2019_08_19_000000_create_failed_jobs_table ............... DONE
2019_12_14_000001_create_personal_access_tokens_table .... DONE
2024_01_01_000001_create_categories_table ................ DONE
2024_01_01_000002_create_products_table .................. DONE
... (and 15 more)
```

### Step 4: Seed Database with Your Data

```bash
php artisan db:seed
```

This will populate:
- ✅ 10 products from your `data/products.ts`
- ✅ 4 blog posts from your `data/blogPosts.ts`
- ✅ 3 static pages from your `data/pages.ts`
- ✅ 6 reviews from your `data/reviews.ts`
- ✅ 4 categories
- ✅ 22 tags
- ✅ 1 admin user + 6 regular users
- ✅ Site settings

### Step 5: Verify Database

Check if everything was created:
```bash
php artisan tinker
```

Then run:
```php
\App\Models\Product::count();  // Should return 10
\App\Models\BlogPost::count(); // Should return 4
\App\Models\User::count();     // Should return 7
\App\Models\Category::count(); // Should return 4
exit
```

### Step 6: Start Laravel Server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Test the health endpoint:
```
http://127.0.0.1:8000/api/health
```

## Model Relationships Overview

### Product Model
```php
$product = Product::find(1);
$product->category;        // Get category
$product->images;          // Get all images
$product->tags;            // Get all tags
$product->reviews;         // Get all reviews
$product->averageRating(); // Calculate average rating
```

### User Model
```php
$user = User::find(1);
$user->orders;      // Get all orders
$user->reviews;     // Get all reviews
$user->wishlists;   // Get wishlist items
$user->blogPosts;   // Get authored blog posts
```

### Order Model
```php
$order = Order::find(1);
$order->user;       // Get customer
$order->items;      // Get order items
$order->generateOrderNumber(); // Auto-generates unique order number
```

### BlogPost Model
```php
$post = BlogPost::where('slug', 'some-slug')->first();
$post->blogCategory;    // Get category
$post->author;          // Get author (User)
$post->tags;            // Get all tags
$post->incrementViews(); // Increase view count
```

## Common Artisan Commands

### Reset and Re-seed Everything
```bash
php artisan migrate:fresh --seed
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

### Check Migration Status
```bash
php artisan migrate:status
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Default Users

All users have password: `password`

### Admin User:
- Email: `admin@cluckngo.com`
- Password: `password`
- Admin: Yes

### Regular Users:
- Chris P. Chicken: `chris@example.com`
- Fry Fanatic: `fry@example.com`
- SpiceQueen: `spice@example.com`
- Mild Mike: `mike@example.com`
- Sandwich Sam: `sam@example.com`
- Chef Cluckington: `chef@example.com`

## Troubleshooting

### Issue: "Access denied for user"
**Solution**: Check your MySQL username and password in `.env`

### Issue: "Database does not exist"
**Solution**: Create the database first using the SQL command above

### Issue: "SQLSTATE[HY000] [2002] Connection refused"
**Solution**: Make sure MySQL/XAMPP is running

### Issue: Migrations fail
**Solution**: 
```bash
# Drop all tables and start fresh
php artisan migrate:fresh
```

### Issue: Foreign key constraint fails
**Solution**: The seeders run in the correct order. If issues persist:
```bash
php artisan migrate:fresh
php artisan db:seed
```

## Next Steps: Creating API Endpoints

1. Install Laravel Sanctum for API authentication:
```bash
composer require laravel/sanctum
php artisan install:api
```

2. Create API controllers:
```bash
php artisan make:controller Api/ProductController --api
php artisan make:controller Api/CategoryController --api
php artisan make:controller Api/OrderController --api
php artisan make:controller Api/CartController
```

3. Configure CORS in `config/cors.php` to allow React frontend

4. Create API routes in `routes/api.php`

## Database Schema Summary

Total Tables: 24 (including Laravel defaults)

**Your Custom Tables**: 20
- categories
- products
- product_images
- tags
- product_tag (pivot)
- reviews
- blog_categories
- blog_posts
- blog_post_tag (pivot)
- pages
- orders
- order_items
- wishlists
- carts
- cart_items
- settings
- coupons
- newsletter_subscribers
- contact_messages
- users (enhanced)

**Laravel Default Tables**: 4
- users (base)
- password_reset_tokens
- failed_jobs
- personal_access_tokens

All models are **ready to use** with proper relationships, casts, and helper methods! 🚀

