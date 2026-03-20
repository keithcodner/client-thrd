@echo off
SETLOCAL
cd /d "%~dp0..\api"

REM Set Node 18 path explicitly
SET "PATH=C:\ProgramData\nvm\v18.20.8;%PATH%"

echo Using Node version:
node --version
echo.
soketi start --config=soketi.config.json
