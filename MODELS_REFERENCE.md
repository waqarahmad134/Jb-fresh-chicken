# Laravel Models Quick Reference

All models are located in `backend/app/Models/`

## 📦 Product Management

### Category Model
**File**: `Category.php`  
**Table**: `categories`

```php
// Relationships
$category->products; // HasMany

// Fields
- id, name, slug, description, image_url
- sort_order, is_active
- created_at, updated_at, deleted_at
```

### Product Model
**File**: `Product.php`  
**Table**: `products`

```php
// Relationships
$product->category;         // BelongsTo
$product->images;           // HasMany
$product->tags;             // BelongsToMany
$product->reviews;          // HasMany
$product->wishlists;        // HasMany
$product->cartItems;        // HasMany

// Computed Attributes
$product->image_url;        // Primary image
$product->image_urls;       // Array of all images

// Methods
$product->averageRating();  // Calculate avg rating

// Fields
- id, name, slug, description, price
- compare_at_price, sku, stock_quantity
- track_inventory, category_id
- is_active, is_featured, sort_order
- created_at, updated_at, deleted_at
```

### ProductImage Model
**File**: `ProductImage.php`  
**Table**: `product_images`

```php
// Relationships
$image->product; // BelongsTo

// Fields
- id, product_id, image_url, alt_text
- sort_order, is_primary
- created_at, updated_at
```

### Tag Model
**File**: `Tag.php`  
**Table**: `tags`

```php
// Relationships
$tag->products;   // BelongsToMany
$tag->blogPosts;  // BelongsToMany

// Fields
- id, name, slug
- created_at, updated_at
```

---

## 👤 User Management

### User Model (Enhanced)
**File**: `User.php`  
**Table**: `users`

```php
// Relationships
$user->orders;     // HasMany
$user->reviews;    // HasMany
$user->blogPosts;  // HasMany (as author)
$user->wishlists;  // HasMany
$user->cart;       // HasOne

// Fields
- id, name, email, email_verified_at
- password, phone, is_admin, is_active
- profile_image
- shipping_address, shipping_city, shipping_state
- shipping_country, shipping_zip
- billing_address, billing_city, billing_state
- billing_country, billing_zip
- remember_token
- created_at, updated_at, deleted_at
```

---

## 🛒 Cart & Orders

### Cart Model
**File**: `Cart.php`  
**Table**: `carts`

```php
// Relationships
$cart->user;   // BelongsTo
$cart->items;  // HasMany

// Computed Attributes
$cart->total;  // Calculate total price

// Fields
- id, user_id, session_id
- created_at, updated_at
```

### CartItem Model
**File**: `CartItem.php`  
**Table**: `cart_items`

```php
// Relationships
$cartItem->cart;    // BelongsTo
$cartItem->product; // BelongsTo

// Fields
- id, cart_id, product_id
- quantity, price
- created_at, updated_at
```

### Order Model
**File**: `Order.php`  
**Table**: `orders`

```php
// Relationships
$order->user;  // BelongsTo
$order->items; // HasMany

// Computed Attributes
$order->date;  // Format created_at

// Static Methods
Order::generateOrderNumber(); // Generate unique order number

// Fields
- id, order_number, user_id
- subtotal, tax, shipping, discount, total
- status (pending, processing, shipped, delivered, cancelled, refunded)
- payment_status (pending, paid, failed, refunded)
- payment_method, payment_id
- shipping_name, shipping_email, shipping_phone
- shipping_address, shipping_city, shipping_state
- shipping_country, shipping_zip
- billing_name, billing_address, billing_city
- billing_state, billing_country, billing_zip
- customer_notes, admin_notes
- tracking_number, shipping_carrier
- shipped_at, delivered_at
- created_at, updated_at, deleted_at
```

### OrderItem Model
**File**: `OrderItem.php`  
**Table**: `order_items`

```php
// Relationships
$orderItem->order;   // BelongsTo
$orderItem->product; // BelongsTo

// Computed Attributes
$orderItem->name;    // Alias for product_name

// Fields
- id, order_id, product_id
- product_name, product_sku
- product_description, product_image
- price, quantity, total
- created_at, updated_at
```

### Wishlist Model
**File**: `Wishlist.php`  
**Table**: `wishlists`

```php
// Relationships
$wishlist->user;    // BelongsTo
$wishlist->product; // BelongsTo

// Fields
- id, user_id, product_id
- created_at, updated_at
```

---

## ⭐ Reviews

### Review Model
**File**: `Review.php`  
**Table**: `reviews`

```php
// Relationships
$review->product; // BelongsTo
$review->user;    // BelongsTo

// Fields
- id, product_id, user_id
- rating (1-5), comment
- is_verified_purchase, is_approved
- created_at, updated_at, deleted_at
```

---

## 📝 Blog

### BlogCategory Model
**File**: `BlogCategory.php`  
**Table**: `blog_categories`

```php
// Relationships
$blogCategory->blogPosts; // HasMany

// Fields
- id, name, slug, description
- created_at, updated_at
```

### BlogPost Model
**File**: `BlogPost.php`  
**Table**: `blog_posts`

```php
// Relationships
$blogPost->blogCategory; // BelongsTo
$blogPost->author;       // BelongsTo (User)
$blogPost->tags;         // BelongsToMany

// Computed Attributes
$blogPost->category;     // Category name

// Methods
$blogPost->incrementViews(); // Increase view count

// Fields
- id, slug, title, excerpt, content
- image_url, blog_category_id, author_id
- author_name, author_image_url
- is_published, published_at, views_count
- created_at, updated_at, deleted_at
```

---

## 📄 Static Pages

### Page Model
**File**: `Page.php`  
**Table**: `pages`

```php
// Computed Attributes
$page->last_updated; // Format updated_at

// Fields
- id, slug, title, content
- meta_title, meta_description
- is_published
- created_at, updated_at, deleted_at
```

---

## ⚙️ Settings & Marketing

### Setting Model
**File**: `Setting.php`  
**Table**: `settings`

```php
// Static Methods
Setting::get('key', 'default');     // Get setting value
Setting::set('key', 'value', 'type'); // Set setting value

// Fields
- id, key, value, type, description, group
- created_at, updated_at
```

### Coupon Model
**File**: `Coupon.php`  
**Table**: `coupons`

```php
// Methods
$coupon->isValid();                    // Check if coupon is valid
$coupon->calculateDiscount($subtotal); // Calculate discount amount

// Fields
- id, code, description
- type (percentage, fixed), value
- minimum_order_amount
- usage_limit, usage_limit_per_user, used_count
- starts_at, expires_at, is_active
- created_at, updated_at, deleted_at
```

### NewsletterSubscriber Model
**File**: `NewsletterSubscriber.php`  
**Table**: `newsletter_subscribers`

```php
// Fields
- id, email, name, is_subscribed
- subscribed_at, unsubscribed_at, ip_address
- created_at, updated_at
```

### ContactMessage Model
**File**: `ContactMessage.php`  
**Table**: `contact_messages`

```php
// Fields
- id, name, email, phone, subject, message
- is_read, is_replied
- admin_reply, replied_at
- created_at, updated_at
```

---

## 🔧 Usage Examples

### Create a Product
```php
$product = Product::create([
    'name' => 'Spicy Wings',
    'slug' => 'spicy-wings',
    'description' => 'Hot and spicy chicken wings',
    'price' => 12.99,
    'category_id' => 1,
    'is_active' => true,
]);

// Add images
$product->images()->create([
    'image_url' => 'https://example.com/image.jpg',
    'is_primary' => true,
]);

// Attach tags
$product->tags()->attach([1, 2, 3]);
```

### Create an Order
```php
$order = Order::create([
    'order_number' => Order::generateOrderNumber(),
    'user_id' => auth()->id(),
    'subtotal' => 100.00,
    'tax' => 10.00,
    'shipping' => 5.00,
    'total' => 115.00,
    'status' => 'pending',
    'shipping_name' => 'John Doe',
    'shipping_email' => 'john@example.com',
    'shipping_address' => '123 Main St',
    // ... other fields
]);

// Add order items
$order->items()->create([
    'product_id' => 1,
    'product_name' => 'Spicy Wings',
    'price' => 12.99,
    'quantity' => 2,
    'total' => 25.98,
]);
```

### Query Products with Relationships
```php
// Get products with category and images
$products = Product::with(['category', 'images', 'tags'])
    ->where('is_active', true)
    ->orderBy('sort_order')
    ->get();

// Get featured products
$featured = Product::where('is_featured', true)
    ->with('images')
    ->get();

// Get products by category
$products = Product::whereHas('category', function($q) {
    $q->where('slug', 'wings');
})->get();
```

### Get User Orders
```php
$user = auth()->user();
$orders = $user->orders()
    ->with('items.product')
    ->orderBy('created_at', 'desc')
    ->get();
```

### Blog Posts with Tags
```php
$posts = BlogPost::where('is_published', true)
    ->with(['blogCategory', 'author', 'tags'])
    ->orderBy('published_at', 'desc')
    ->paginate(10);
```

---

## 🎯 All Models Summary

| Model | Table | Key Features |
|-------|-------|--------------|
| Category | categories | Product categories, soft deletes |
| Product | products | Full e-commerce product, multiple images, tags |
| ProductImage | product_images | Multiple images per product |
| Tag | tags | Shared by products and blog posts |
| User | users | Enhanced with addresses, admin flag |
| Cart | carts | Guest and user carts |
| CartItem | cart_items | Cart line items |
| Order | orders | Complete order management |
| OrderItem | order_items | Order line items |
| Wishlist | wishlists | User favorites |
| Review | reviews | Product reviews with approval |
| BlogCategory | blog_categories | Blog organization |
| BlogPost | blog_posts | Full blog with views tracking |
| Page | pages | Static pages (About, Terms, etc) |
| Setting | settings | Site-wide configuration |
| Coupon | coupons | Discount codes with validation |
| NewsletterSubscriber | newsletter_subscribers | Email list |
| ContactMessage | contact_messages | Contact form submissions |

**Total: 18 Models - All Ready to Use!** ✅

