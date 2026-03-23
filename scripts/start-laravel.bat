@echo off
cd /d "%~dp0..\api"
start "Laravel Queue Worker" php artisan queue:work --tries=3
php artisan serve --host=0.0.0.0
