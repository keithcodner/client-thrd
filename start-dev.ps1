# Development Startup Script
# This script starts all development services with the correct Node versions

Write-Host "Starting Development Environment..." -ForegroundColor Green
Write-Host ""

# Get the script directory
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path

# Start Soketi with Node 18 in a new terminal
Write-Host "Starting Soketi (Node 18) in new terminal..." -ForegroundColor Cyan
Start-Process cmd -ArgumentList "/k", "$scriptPath\api\start-soketi.bat"

# Wait a moment
Start-Sleep -Seconds 2

# Start Laravel API in a new terminal
Write-Host "Starting Laravel API in new terminal..." -ForegroundColor Magenta
Start-Process cmd -ArgumentList "/k", "$scriptPath\api\start-laravel.bat"

# Wait a moment
Start-Sleep -Seconds 2

# Start Expo with Node 20 in a new terminal
Write-Host "Starting Expo (Node 20) in new terminal..." -ForegroundColor Yellow
Start-Process cmd -ArgumentList "/k", "$scriptPath\mobile\start-expo.bat"

Write-Host ""
Write-Host "All services started!" -ForegroundColor Green
Write-Host "- Soketi: http://localhost:6001" -ForegroundColor Cyan
Write-Host "- Laravel API: http://localhost:8000" -ForegroundColor Magenta
Write-Host "- Expo: Check the terminal for QR code" -ForegroundColor Yellow
Write-Host ""
Write-Host "Press any key to close this window..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
