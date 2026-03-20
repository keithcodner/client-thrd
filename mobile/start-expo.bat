@echo off
echo Switching to Node 20...
call nvm use 20
if errorlevel 1 (
    echo Failed to switch to Node 20
    pause
    exit /b 1
)

echo Starting Expo Development Server...
cd /d "%~dp0"
call npm run start:clean
pause
