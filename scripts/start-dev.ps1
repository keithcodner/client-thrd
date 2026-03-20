# Development Startup Script
# This script starts all development services using concurrently

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Starting THRD Development Environment" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Change to scripts directory
Set-Location $PSScriptRoot

# Check if node_modules exists
if (-not (Test-Path "node_modules")) {
    Write-Host "Installing dependencies..." -ForegroundColor Yellow
    npm install
    Write-Host ""
}

Write-Host "Starting all services with concurrently..." -ForegroundColor Green
Write-Host ""
Write-Host "  Soketi:      http://localhost:6001 (Node 18)" -ForegroundColor Cyan
Write-Host "  Laravel API: http://localhost:8000" -ForegroundColor Magenta
Write-Host "  Expo:        Check output for QR code (Node 22)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Press Ctrl+C to stop all services" -ForegroundColor Gray
Write-Host ""

# Run all services with concurrently
npm run dev
