@echo off
cd /d "%~dp0mobile"
call nvm use 22
echo.
npx expo start -c
