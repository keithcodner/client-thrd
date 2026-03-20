@echo off
echo Switching to Node 18...
call nvm use 18
if errorlevel 1 (
    echo Failed to switch to Node 18
    pause
    exit /b 1
)

echo Starting Soketi WebSocket Server...
cd /d "%~dp0"
call npm run soketi
pause
