@echo off
echo.
echo ========================================
echo   Starting THRD Development Environment
echo ========================================
echo.

REM Start Soketi with Node 18
echo [1/3] Starting Soketi Server (Node 18)...
start "Soketi Server" cmd /k "%~dp0start-soketi.bat"

timeout /t 1 /nobreak > nul

REM Start Laravel API
echo [2/3] Starting Laravel API...
start "Laravel API" cmd /k "%~dp0start-laravel.bat"

timeout /t 1 /nobreak > nul

REM Start Expo with Node 18
echo [3/3] Starting Expo Dev Server (Node 18)...
start "Expo Dev Server" cmd /k "%~dp0start-expo.bat"

echo.
echo ========================================
echo   All services started!
echo ========================================
echo.
echo   Soketi:      http://localhost:6001
echo   Laravel API: http://localhost:8000
echo   Expo:        Check terminal for QR code
echo.
pause
