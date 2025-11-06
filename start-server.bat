@echo off
echo Starting Laravel Server...
echo.
php artisan config:clear
php artisan cache:clear
php artisan route:clear
echo.
echo Server starting on http://127.0.0.1:8000
echo Press Ctrl+C to stop
echo.
php artisan serve --host=127.0.0.1 --port=8000

