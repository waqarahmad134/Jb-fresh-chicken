# JB Fresh Chicken API Documentation

Base URL: `http://127.0.0.1:8000/api/v1`

## Authentication

The API uses Laravel Sanctum for authentication. Include the auth token in the `Authorization` header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 🔐 Authentication Endpoints

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "message": "User registered successfully",
  "user": { ...user object },
  "token": "1|xxxxxxxxxxxxx"
}
```

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "user": { ...user object },
  "token": "2|xxxxxxxxxxxxx"
}
```

### Logout (Protected)
```http
POST /api/auth/logout
Authorization: Bearer YOUR_TOKEN
```

### Get Current User (Protected)
```http
GET /api/auth/me
Authorization: Bearer YOUR_TOKEN
```

### Update Profile (Protected)
```http
PUT /api/auth/profile
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "name": "John Doe",
  "phone": "1234567890",
  "shipping_address": "123 Main St",
  "shipping_city": "New York",
  "shipping_state": "NY",
  "shipping_country": "USA",
  "shipping_zip": "10001"
}
```

### Change Password (Protected)
```http
PUT /api/auth/change-password
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "current_password": "oldpassword",
  "new_password": "newpassword",
  "new_password_confirmation": "newpassword"
}
```

---

## 🛍️ Product Endpoints

### Get All Products
```http
GET /api/v1/products

Query Parameters:
  - category: string (filter by category slug)
  - tag: string (filter by tag slug)
  - search: string (search in name and description)
  - featured: boolean (get featured products only)
  - sort_by: string (default: sort_order)
  - sort_order: string (asc/desc, default: asc)
```

**Example:**
```http
GET /api/v1/products?category=wings&featured=true
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Classic Crispy Nuggets",
    "slug": "classic-crispy-nuggets",
    "description": "12 pieces of our signature golden-brown...",
    "price": "9.99",
    "category": {
      "id": 1,
      "name": "Nuggets & Poppers",
      "slug": "nuggets-poppers"
    },
    "image_url": "https://...",
    "image_urls": ["https://..."],
    "tags": [
      { "id": 1, "name": "classic", "slug": "classic" }
    ],
    "is_active": true,
    "is_featured": false
  }
]
```

### Get Single Product
```http
GET /api/v1/products/{id}
```

**Response:**
```json
{
  "id": 1,
  "name": "Classic Crispy Nuggets",
  "description": "...",
  "price": "9.99",
  "category": { ... },
  "images": [ ... ],
  "tags": [ ... ],
  "reviews": [ ... ],
  "average_rating": 4.5,
  "reviews_count": 10
}
```

### Create Product (Admin Only)
```http
POST /api/v1/admin/products
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "name": "New Product",
  "description": "Product description",
  "price": 12.99,
  "category_id": 1,
  "image_urls": ["https://..."],
  "tags": [1, 2, 3]
}
```

### Update Product (Admin Only)
```http
PUT /api/v1/admin/products/{id}
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "name": "Updated Product Name",
  "price": 14.99,
  "is_active": true,
  "is_featured": true
}
```

### Delete Product (Admin Only)
```http
DELETE /api/v1/admin/products/{id}
Authorization: Bearer ADMIN_TOKEN
```

---

## 📦 Category Endpoints

### Get All Categories
```http
GET /api/v1/categories
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Nuggets & Poppers",
    "slug": "nuggets-poppers",
    "description": "Delicious nuggets...",
    "image_url": "https://...",
    "is_active": true
  }
]
```

### Get Category with Products
```http
GET /api/v1/categories/{slug}
```

**Response:**
```json
{
  "id": 1,
  "name": "Nuggets & Poppers",
  "slug": "nuggets-poppers",
  "products": [ ...products array ]
}
```

---

## 🛒 Cart Endpoints

### Get Cart
```http
GET /api/v1/cart
```

Works for both guest (session-based) and authenticated users.

**Response:**
```json
{
  "id": 1,
  "items": [
    {
      "id": 1,
      "product": {
        "id": 1,
        "name": "Classic Crispy Nuggets",
        "price": "9.99",
        "image_url": "https://..."
      },
      "quantity": 2,
      "price": "9.99",
      "total": "19.98"
    }
  ],
  "subtotal": "19.98"
}
```

### Add Item to Cart
```http
POST /api/v1/cart/items
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

### Update Cart Item
```http
PUT /api/v1/cart/items/{itemId}
Content-Type: application/json

{
  "quantity": 3
}
```

### Remove Cart Item
```http
DELETE /api/v1/cart/items/{itemId}
```

### Clear Cart
```http
DELETE /api/v1/cart/clear
```

---

## 📦 Order Endpoints (Protected)

### Get User Orders
```http
GET /api/v1/orders
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
[
  {
    "id": 1,
    "order_number": "ORD-ABC123",
    "status": "delivered",
    "payment_status": "paid",
    "total": "115.00",
    "date": "2023-11-01",
    "items": [ ... ]
  }
]
```

### Get Single Order
```http
GET /api/v1/orders/{id}
Authorization: Bearer YOUR_TOKEN
```

### Create Order
```http
POST /api/v1/orders
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "shipping_name": "John Doe",
  "shipping_email": "john@example.com",
  "shipping_phone": "1234567890",
  "shipping_address": "123 Main St",
  "shipping_city": "New York",
  "shipping_state": "NY",
  "shipping_country": "USA",
  "shipping_zip": "10001",
  "payment_method": "credit_card",
  "customer_notes": "Please ring doorbell"
}
```

**Response:**
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 1,
    "order_number": "ORD-XYZ789",
    "total": "115.00",
    "items": [ ... ]
  }
}
```

### Update Order Status (Admin Only)
```http
PUT /api/v1/admin/orders/{id}/status
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "status": "shipped"
}
```

**Status options:** `pending`, `processing`, `shipped`, `delivered`, `cancelled`, `refunded`

---

## ⭐ Review Endpoints

### Get Product Reviews
```http
GET /api/v1/products/{productId}/reviews
```

**Response:**
```json
[
  {
    "id": 1,
    "rating": 5,
    "comment": "Absolutely the crispiest nuggets...",
    "user": {
      "id": 1,
      "name": "John Doe"
    },
    "created_at": "2023-10-20T..."
  }
]
```

### Create Review (Protected)
```http
POST /api/v1/products/{productId}/reviews
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "rating": 5,
  "comment": "Amazing product!"
}
```

### Update Review (Protected)
```http
PUT /api/v1/products/{productId}/reviews/{reviewId}
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "rating": 4,
  "comment": "Updated review"
}
```

### Delete Review (Protected)
```http
DELETE /api/v1/products/{productId}/reviews/{reviewId}
Authorization: Bearer YOUR_TOKEN
```

---

## 📝 Blog Endpoints

### Get All Blog Posts
```http
GET /api/v1/blog

Query Parameters:
  - category: string (filter by category slug)
  - tag: string (filter by tag slug)
  - search: string (search in title, excerpt, content)
```

**Response:**
```json
[
  {
    "id": 1,
    "slug": "ultimate-guide-to-dipping-sauces",
    "title": "The Ultimate Guide to Chicken Nugget Dipping Sauces",
    "excerpt": "Tired of the same old ketchup?...",
    "content": "Full content...",
    "image_url": "https://...",
    "category": "Recipes & Ideas",
    "tags": [ ... ],
    "author": {
      "id": 1,
      "name": "Chris P. Chicken"
    },
    "author_name": "Chris P. Chicken",
    "author_image_url": "https://...",
    "views_count": 150,
    "published_at": "2023-11-05T..."
  }
]
```

### Get Single Blog Post
```http
GET /api/v1/blog/{slug}
```

**Note:** Automatically increments view count.

### Get Blog Categories
```http
GET /api/v1/blog/categories
```

### Create Blog Post (Admin Only)
```http
POST /api/v1/admin/blog
Authorization: Bearer ADMIN_TOKEN
Content-Type: application/json

{
  "title": "New Blog Post",
  "excerpt": "Short description",
  "content": "Full content here...",
  "image_url": "https://...",
  "blog_category_id": 1,
  "tags": [1, 2, 3],
  "is_published": true
}
```

### Update Blog Post (Admin Only)
```http
PUT /api/v1/admin/blog/{id}
Authorization: Bearer ADMIN_TOKEN
```

### Delete Blog Post (Admin Only)
```http
DELETE /api/v1/admin/blog/{id}
Authorization: Bearer ADMIN_TOKEN
```

---

## 💖 Wishlist Endpoints (Protected)

### Get User Wishlist
```http
GET /api/v1/wishlist
Authorization: Bearer YOUR_TOKEN
```

**Response:**
```json
[
  {
    "id": 1,
    "product": {
      "id": 1,
      "name": "Classic Crispy Nuggets",
      "price": "9.99",
      "image_url": "https://..."
    },
    "created_at": "2023-11-01T..."
  }
]
```

### Add to Wishlist
```http
POST /api/v1/wishlist
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "product_id": 1
}
```

### Remove from Wishlist
```http
DELETE /api/v1/wishlist/{productId}
Authorization: Bearer YOUR_TOKEN
```

---

## 📄 Page Endpoints

### Get All Pages
```http
GET /api/v1/pages
```

### Get Single Page
```http
GET /api/v1/pages/{slug}
```

**Example:**
```http
GET /api/v1/pages/about
GET /api/v1/pages/privacy
GET /api/v1/pages/terms
```

**Response:**
```json
{
  "id": 1,
  "slug": "about",
  "title": "About JB Fresh Chicken",
  "content": "<p>Founded in 2023...</p>",
  "meta_title": "About Us - JB Fresh Chicken",
  "meta_description": "Learn about...",
  "last_updated": "2023-11-01"
}
```

---

## ⚙️ Settings Endpoints

### Get All Settings
```http
GET /api/v1/settings
```

**Response:**
```json
{
  "site_name": "JB Fresh Chicken",
  "logo_url": "/logo.png",
  "meta_description": "Order the crispiest...",
  "blog_enabled": true,
  "newsletter_enabled": true,
  "maintenance_mode": false,
  "product_card_style": "style1",
  "quick_view_enabled": true,
  "add_to_cart_behavior": "drawer"
}
```

### Get Settings by Group
```http
GET /api/v1/settings/group/{group}
```

**Example:**
```http
GET /api/v1/settings/group/products
GET /api/v1/settings/group/general
```

---

## 🔍 Health Check

### API Health
```http
GET /api/health
```

**Response:**
```json
{
  "status": "ok",
  "message": "API is running"
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "Validation error message",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized. Admin access required."
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 500 Server Error
```json
{
  "message": "Server Error",
  "error": "Error details"
}
```

---

## React Frontend Integration

### Setup Axios Client

```javascript
// src/services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/v1',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### Example Usage

```javascript
// Get products
const { data } = await api.get('/products');

// Add to cart
await api.post('/cart/items', {
  product_id: 1,
  quantity: 2
});

// Login
const { data } = await api.post('/auth/login', {
  email: 'user@example.com',
  password: 'password'
});
localStorage.setItem('auth_token', data.token);

// Get user orders
const { data } = await api.get('/orders');
```

---

## Testing the API

### Using cURL

```bash
# Get products
curl http://127.0.0.1:8000/api/v1/products

# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@jbfreshchicken.com","password":"password"}'

# Get orders (with auth)
curl http://127.0.0.1:8000/api/v1/orders \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Using Postman

1. Import the base URL: `http://127.0.0.1:8000/api/v1`
2. For protected routes, add Authorization header: `Bearer YOUR_TOKEN`
3. Set Content-Type to `application/json`

---

## Rate Limiting

Default Laravel rate limiting applies:
- **60 requests per minute** for authenticated users
- **30 requests per minute** for guests

---

## Notes

- All timestamps are in ISO 8601 format
- Decimal values (prices) have 2 decimal places
- All POST/PUT requests require `Content-Type: application/json`
- Admin routes require both authentication and `is_admin = true`
- Cart works for both guest sessions and authenticated users
- Products, categories, and blog posts support soft deletes

---

## Quick Start

1. **Setup Laravel Sanctum:**
```bash
php artisan install:api
```

2. **Run migrations and seeders:**
```bash
php artisan migrate
php artisan db:seed
```

3. **Start server:**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

4. **Test the API:**
```bash
curl http://127.0.0.1:8000/api/health
```

**Default Admin Credentials:**
- Email: `admin@jbfreshchicken.com`
- Password: `password`

Happy coding! 🚀

