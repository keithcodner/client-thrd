@echo off
echo Starting Laravel API Server...
cd /d "%~dp0"
php artisan serve
pause
