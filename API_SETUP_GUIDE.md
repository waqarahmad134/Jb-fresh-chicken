# API Setup Guide - Cluck 'n' Go

## ✅ What's Been Created

### 🎯 10 API Controllers

All controllers are in `app/Http/Controllers/Api/`:

1. **AuthController** - Registration, login, logout, profile management
2. **ProductController** - CRUD operations, filtering, search
3. **CategoryController** - Categories and their products
4. **CartController** - Cart management (guest & user)
5. **OrderController** - Order creation and management
6. **ReviewController** - Product reviews
7. **BlogController** - Blog posts management
8. **WishlistController** - User wishlists
9. **PageController** - Static pages
10. **SettingController** - Site settings

### 🛣️ Complete API Routes

Location: `routes/api.php`

- ✅ Public routes (no auth required)
- ✅ Protected routes (auth required)
- ✅ Admin routes (auth + admin role)
- ✅ RESTful design
- ✅ Version prefix (`/api/v1`)

### 🔐 Security Setup

- ✅ Laravel Sanctum integration
- ✅ CORS configuration for React
- ✅ Admin middleware
- ✅ Session-based cart for guests

---

## 🚀 Installation Steps

### Step 1: Install Laravel Sanctum

```bash
cd backend
composer require laravel/sanctum
php artisan install:api
```

This will:
- Publish Sanctum config
- Create personal_access_tokens migration
- Configure API routes

### Step 2: Configure MySQL Database

Edit `backend/.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cluckngo
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create database:
```bash
mysql -u root -p
CREATE DATABASE cluckngo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

This creates all 24 tables.

### Step 4: Seed Database

```bash
php artisan db:seed
```

This imports:
- 10 products
- 4 blog posts
- 3 pages
- 6 reviews
- Admin + 6 users
- Categories, tags, settings

### Step 5: Start Server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Step 6: Test API

```bash
# Health check
curl http://127.0.0.1:8000/api/health

# Get products
curl http://127.0.0.1:8000/api/v1/products

# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@cluckngo.com","password":"password"}'
```

---

## 🔌 Connect React Frontend

### Option 1: Update Existing Services

If you have existing service files, update them:

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

// Add token to all requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

### Option 2: Update Context Providers

Update your React contexts to use the API:

**ProductContext Example:**
```javascript
import api from '../services/api';

const fetchProducts = async () => {
  try {
    const { data } = await api.get('/products');
    setProducts(data);
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};
```

**AuthContext Example:**
```javascript
const login = async (email, password) => {
  try {
    const { data } = await api.post('/auth/login', { email, password });
    localStorage.setItem('auth_token', data.token);
    setUser(data.user);
    return data;
  } catch (error) {
    throw error;
  }
};
```

**CartContext Example:**
```javascript
const addToCart = async (productId, quantity) => {
  try {
    await api.post('/cart/items', {
      product_id: productId,
      quantity,
    });
    // Refresh cart
    await fetchCart();
  } catch (error) {
    console.error('Error adding to cart:', error);
  }
};
```

---

## 📡 API Endpoints Summary

### Authentication (No prefix)
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout (protected)
GET    /api/auth/me (protected)
PUT    /api/auth/profile (protected)
PUT    /api/auth/change-password (protected)
```

### Public Routes (`/api/v1`)
```
GET    /products
GET    /products/{id}
GET    /categories
GET    /categories/{slug}
GET    /blog
GET    /blog/{slug}
GET    /blog/categories
GET    /pages
GET    /pages/{slug}
GET    /settings
GET    /products/{id}/reviews
```

### Cart (Public/Protected)
```
GET    /cart
POST   /cart/items
PUT    /cart/items/{id}
DELETE /cart/items/{id}
DELETE /cart/clear
```

### Protected Routes (`/api/v1`)
```
GET    /orders
GET    /orders/{id}
POST   /orders
POST   /products/{id}/reviews
PUT    /products/{id}/reviews/{reviewId}
DELETE /products/{id}/reviews/{reviewId}
GET    /wishlist
POST   /wishlist
DELETE /wishlist/{productId}
```

### Admin Routes (`/api/v1/admin`)
```
POST   /products
PUT    /products/{id}
DELETE /products/{id}
POST   /blog
PUT    /blog/{id}
DELETE /blog/{id}
PUT    /orders/{id}/status
```

---

## 🔧 Environment Variables

Make sure your React `.env` has:

```env
VITE_API_BASE_URL=http://127.0.0.1:8000/api/v1
VITE_API_AUTH_URL=http://127.0.0.1:8000/api/auth
```

---

## 🧪 Testing Endpoints

### Test Login
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@cluckngo.com",
    "password": "password"
  }'
```

Save the token from response.

### Test Protected Endpoint
```bash
curl http://127.0.0.1:8000/api/v1/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Test Cart (Guest)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/cart/items \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Test Admin Endpoint
```bash
curl -X POST http://127.0.0.1:8000/api/v1/admin/products \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Product",
    "description": "Test product",
    "price": 12.99,
    "category_id": 1
  }'
```

---

## 🎯 Default Test Users

All passwords are: `password`

### Admin User
- Email: `admin@cluckngo.com`
- Role: Admin
- Access: All endpoints

### Regular Users
- `chris@example.com`
- `fry@example.com`
- `spice@example.com`
- `mike@example.com`
- `sam@example.com`
- `chef@example.com`

---

## 🐛 Troubleshooting

### CORS Issues
If you get CORS errors:

1. Check `config/cors.php`:
```php
'allowed_origins' => [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
],
```

2. Clear config cache:
```bash
php artisan config:clear
```

### 401 Unauthorized on Protected Routes
- Make sure token is in Authorization header
- Check token hasn't expired
- Verify user exists and is active

### 403 Forbidden on Admin Routes
- User must have `is_admin = true`
- Check admin middleware is working

### Route Not Found
```bash
# Check routes
php artisan route:list

# Clear route cache
php artisan route:clear
```

### Database Connection Issues
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();
```

---

## 📊 Database Status

Check your data:

```bash
php artisan tinker
```

Then run:
```php
\App\Models\Product::count();   // Should be 10
\App\Models\User::count();      // Should be 7
\App\Models\BlogPost::count();  // Should be 4
\App\Models\Order::count();     // Should be 0 (created by users)
exit
```

---

## 🚀 Next Steps

1. **Update React contexts** to use the API
2. **Replace mock data** with API calls
3. **Implement authentication** flow
4. **Test cart functionality** with API
5. **Test order creation** flow
6. **Add error handling** for API calls
7. **Implement loading states**

---

## 📝 API Features

✅ **Authentication & Authorization**
- JWT tokens with Sanctum
- User registration & login
- Password management
- Profile updates

✅ **E-commerce**
- Product catalog with filters
- Category navigation
- Shopping cart (guest & user)
- Order management
- Product reviews
- Wishlist

✅ **Content Management**
- Blog posts
- Static pages
- Site settings

✅ **Admin Panel**
- Product management
- Blog management
- Order status updates

✅ **Data Validation**
- All inputs validated
- Proper error messages
- Type casting

✅ **Security**
- CORS configured
- Admin middleware
- Protected routes
- SQL injection prevention

---

## 🎉 You're All Set!

Your Laravel API is fully configured and ready to power your React frontend!

**Start the server:**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**API Documentation:**
See `API_DOCUMENTATION.md` for complete endpoint reference.

**Test it:**
```
http://127.0.0.1:8000/api/health
http://127.0.0.1:8000/api/v1/products
```

Happy coding! 🚀🍗

