@echo off
echo.
echo ========================================
echo   Starting THRD Development Environment
echo ========================================
echo.

cd /d "%~dp0"

REM Check if node_modules exists, if not install
if not exist "node_modules" (
    echo Installing dependencies...
    call npm install
    echo.
)

echo Starting all services with concurrently...
echo.
echo   Soketi:      http://localhost:6001 (Node 18)
echo   Laravel API: http://localhost:8000
echo   Expo:        Check output for QR code (Node 22)
echo.
echo Press Ctrl+C to stop all services
echo.

npm run dev
