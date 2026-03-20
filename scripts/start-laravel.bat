@echo off
cd /d "%~dp0..\api"
php artisan serve --host=0.0.0.0
