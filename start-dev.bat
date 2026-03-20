@echo off
echo Starting Development Environment...
echo.

REM Get the directory where this script is located
set SCRIPT_DIR=%~dp0

REM Start Soketi with Node 18
echo Starting Soketi (Node 18) in new window...
start "Soketi Server" "%SCRIPT_DIR%api\start-soketi.bat"

REM Wait
timeout /t 2 /nobreak > nul

REM Start Laravel API
echo Starting Laravel API in new window...
start "Laravel API" "%SCRIPT_DIR%api\start-laravel.bat"

REM Wait
timeout /t 2 /nobreak > nul

REM Start Expo with Node 20
echo Starting Expo (Node 20) in new window...
start "Expo Dev Server" "%SCRIPT_DIR%mobile\start-expo.bat"

echo.
echo All services started!
echo - Soketi: http://localhost:6001
echo - Laravel API: http://localhost:8000
echo - Expo: Check the terminal for QR code
echo.
pause
