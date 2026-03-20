@echo off
cd /d "%~dp0api"
call nvm use 20
node --version
echo.
soketi start --config=soketi.config.json
