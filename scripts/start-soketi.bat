@echo off
SETLOCAL

REM Set Node 18 path explicitly
SET "PATH=C:\ProgramData\nvm\v18.20.8;%PATH%"

echo Using Node version:
node --version
echo.
soketi start --config=%~dp0..\api\soketi.config.json