@echo off
echo ============================================
echo  Cluck 'n' Go API Setup
echo ============================================
echo.

echo [1/5] Installing Laravel Sanctum...
call composer require laravel/sanctum
call php artisan install:api
echo.

echo [2/5] Running migrations...
call php artisan migrate
echo.

echo [3/5] Seeding database...
call php artisan db:seed
echo.

echo [4/5] Clearing caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
echo.

echo [5/5] Testing API...
curl http://127.0.0.1:8000/api/health
echo.

echo ============================================
echo  Setup Complete!
echo ============================================
echo.
echo Default Admin Login:
echo   Email: admin@cluckngo.com
echo   Password: password
echo.
echo Start the server with:
echo   php artisan serve --host=127.0.0.1 --port=8000
echo.
echo View API documentation:
echo   API_DOCUMENTATION.md
echo.
pause

