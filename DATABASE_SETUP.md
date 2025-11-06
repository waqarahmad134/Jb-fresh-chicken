# Cluck 'n' Go Backend - Database Setup

## Overview
This Laravel backend has been created for the Cluck 'n' Go e-commerce website. It includes comprehensive migrations, models, and seeders based on your existing React frontend data.

## Database Structure

### Tables Created

1. **users** - Customer and admin accounts
2. **categories** - Product categories (Nuggets & Poppers, Wings, etc.)
3. **products** - Product catalog with pricing and inventory
4. **product_images** - Multiple images per product
5. **tags** - Tags for products and blog posts
6. **product_tag** - Many-to-many relationship between products and tags
7. **reviews** - Customer product reviews
8. **blog_categories** - Blog post categories
9. **blog_posts** - Blog articles
10. **blog_post_tag** - Many-to-many relationship between blog posts and tags
11. **pages** - Static pages (About, Privacy, Terms)
12. **orders** - Customer orders with complete details
13. **order_items** - Items within each order
14. **wishlists** - User wishlist items
15. **carts** - Shopping carts (guest and user)
16. **cart_items** - Items in shopping carts
17. **settings** - Site-wide settings
18. **coupons** - Discount coupons
19. **newsletter_subscribers** - Email subscribers
20. **contact_messages** - Contact form submissions

## Setup Instructions

### 1. Configure Database

Edit `backend/.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cluckngo
DB_USERNAME=root
DB_PASSWORD=your_password
```

For SQLite (easier for development):
```env
DB_CONNECTION=sqlite
# DB_DATABASE is already set to database/database.sqlite
```

### 2. Create Database

For MySQL:
```bash
mysql -u root -p
CREATE DATABASE cluckngo;
exit
```

For SQLite:
```bash
cd backend
touch database/database.sqlite
```

### 3. Run Migrations

```bash
cd backend
php artisan migrate
```

This will create all 20 tables with proper relationships and indexes.

### 4. Seed Database

```bash
php artisan db:seed
```

This will populate your database with:
- **Admin user**: admin@cluckngo.com / password
- **6 regular users** (password: password for all)
- **4 product categories**
- **10 products** from your existing data
- **6 product reviews**
- **3 blog categories**
- **4 blog posts** from your existing data
- **3 static pages** (About, Privacy, Terms)
- **Site settings**
- **22 tags**

### 5. Fresh Migration (Optional)

To reset and re-seed everything:
```bash
php artisan migrate:fresh --seed
```

## Models and Relationships

### Product Model
- Belongs to Category
- Has many ProductImages
- Has many Reviews
- Belongs to many Tags
- Has many Wishlists
- Has many CartItems

### User Model
- Has many Orders
- Has many Reviews
- Has many BlogPosts (as author)
- Has many Wishlists
- Has one Cart

### Order Model
- Belongs to User
- Has many OrderItems
- Generates unique order numbers automatically

### BlogPost Model
- Belongs to BlogCategory
- Belongs to User (author)
- Belongs to many Tags
- Tracks view counts

## API Endpoints (To Be Implemented)

Suggested REST API structure:

### Products
- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create product (admin)
- `PUT /api/products/{id}` - Update product (admin)
- `DELETE /api/products/{id}` - Delete product (admin)

### Categories
- `GET /api/categories` - List all categories
- `GET /api/categories/{id}/products` - Products by category

### Cart
- `GET /api/cart` - Get user cart
- `POST /api/cart/items` - Add item to cart
- `PUT /api/cart/items/{id}` - Update cart item
- `DELETE /api/cart/items/{id}` - Remove from cart

### Orders
- `GET /api/orders` - User's orders
- `POST /api/orders` - Create order
- `GET /api/orders/{id}` - Order details

### Blog
- `GET /api/blog` - List blog posts
- `GET /api/blog/{slug}` - Single blog post

### Pages
- `GET /api/pages/{slug}` - Get static page

### Reviews
- `GET /api/products/{id}/reviews` - Product reviews
- `POST /api/products/{id}/reviews` - Add review (auth)

## Next Steps

1. **Install Laravel Sanctum** for API authentication:
   ```bash
   php artisan install:api
   ```

2. **Create API Controllers**:
   ```bash
   php artisan make:controller Api/ProductController --api
   php artisan make:controller Api/CategoryController --api
   php artisan make:controller Api/OrderController --api
   php artisan make:controller Api/CartController
   php artisan make:controller Api/BlogController
   ```

3. **Set up CORS** for React frontend:
   ```bash
   php artisan vendor:publish --tag=cors
   ```
   
   Edit `config/cors.php`:
   ```php
   'paths' => ['api/*', 'sanctum/csrf-cookie'],
   'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
   ```

4. **Create API Routes** in `routes/api.php`

5. **Run Server**:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

## Testing

Access the health endpoint:
```
http://127.0.0.1:8000/api/health
```

Should return:
```json
{"status":"ok"}
```

## Database Backup

To export your data:
```bash
php artisan db:seed --class=YourSeeder > backup.sql
```

## Notes

- All prices are stored as DECIMAL(10,2)
- Soft deletes are enabled on most models
- Product images support multiple URLs with sort order
- Orders have complete shipping and billing information
- Reviews can be approved/disapproved by admin
- Blog posts track view counts
- Settings support different types (string, boolean, integer, json)

## Default Admin Credentials

**Email**: admin@cluckngo.com  
**Password**: password

⚠️ **Change these in production!**

