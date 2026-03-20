@echo off
cd /d "%~dp0"
call nvm use 18
echo.
npx expo start -c
