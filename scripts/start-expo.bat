@echo off
SETLOCAL
cd /d "%~dp0..\mobile"

REM Set Node 22 path explicitly
SET "PATH=C:\ProgramData\nvm\v22.22.0;%PATH%"

echo Using Node version:
node --version
echo.
npx expo start -c
