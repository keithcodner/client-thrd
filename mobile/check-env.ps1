# Environment Check Script
# Verifies your development environment is correctly configured

Write-Host "=== THRD Development Environment Check ===" -ForegroundColor Cyan
Write-Host ""

# Check NVM
Write-Host "Checking NVM..." -ForegroundColor Yellow
try {
    $nvmVersion = nvm version
    Write-Host "[OK] NVM installed: $nvmVersion" -ForegroundColor Green
} catch {
    Write-Host "[FAIL] NVM not found. Install from: https://github.com/coreybutler/nvm-windows" -ForegroundColor Red
}

Write-Host ""

# Check Node 18
Write-Host "Checking Node 18..." -ForegroundColor Yellow
try {
    nvm use 18 | Out-Null
    $node18 = node --version
    Write-Host "[OK] Node 18 available: $node18" -ForegroundColor Green
    
    # Check if soketi is installed
    try {
        $null = Get-Command soketi -ErrorAction Stop
        Write-Host "[OK] Soketi installed globally" -ForegroundColor Green
    } catch {
        Write-Host "[FAIL] Soketi not installed. Run: nvm use 18; npm install -g @soketi/soketi" -ForegroundColor Red
    }
} catch {
    Write-Host "[FAIL] Node 18 not installed. Run: nvm install 18" -ForegroundColor Red
}

Write-Host ""

# Check Node 20
Write-Host "Checking Node 20..." -ForegroundColor Yellow
try {
    nvm use 20 | Out-Null
    $node20 = node --version
    Write-Host "[OK] Node 20 available: $node20" -ForegroundColor Green
} catch {
    Write-Host "[FAIL] Node 20 not installed. Run: nvm install 20" -ForegroundColor Red
}

Write-Host ""

# Check PHP
Write-Host "Checking PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php --version | Select-Object -First 1
    Write-Host "[OK] PHP installed: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "[FAIL] PHP not found. Install XAMPP or PHP manually" -ForegroundColor Red
}

Write-Host ""

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>$null | Select-Object -First 1
    Write-Host "[OK] Composer installed: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "[FAIL] Composer not found. Install from: https://getcomposer.org/" -ForegroundColor Red
}

Write-Host ""

# Check .nvmrc files
Write-Host "Checking .nvmrc files..." -ForegroundColor Yellow
if (Test-Path "api\.nvmrc") {
    $apiNodeVer = Get-Content "api\.nvmrc" -Raw
    Write-Host "[OK] api/.nvmrc exists (Node $($apiNodeVer.Trim()))" -ForegroundColor Green
} else {
    Write-Host "[FAIL] api/.nvmrc missing" -ForegroundColor Red
}

if (Test-Path "mobile\.nvmrc") {
    $mobileNodeVer = Get-Content "mobile\.nvmrc" -Raw
    Write-Host "[OK] mobile/.nvmrc exists (Node $($mobileNodeVer.Trim()))" -ForegroundColor Green
} else {
    Write-Host "[FAIL] mobile/.nvmrc missing" -ForegroundColor Red
}

Write-Host ""

# Check package installations
Write-Host "Checking project dependencies..." -ForegroundColor Yellow

if (Test-Path "api\node_modules") {
    Write-Host "[OK] API node_modules exists" -ForegroundColor Green
} else {
    Write-Host "[FAIL] API dependencies not installed. Run: cd api; npm install" -ForegroundColor Red
}

if (Test-Path "api\vendor") {
    Write-Host "[OK] API vendor (Composer) exists" -ForegroundColor Green
} else {
    Write-Host "[FAIL] API Composer dependencies not installed. Run: cd api; composer install" -ForegroundColor Red
}

if (Test-Path "mobile\node_modules") {
    Write-Host "[OK] Mobile node_modules exists" -ForegroundColor Green
} else {
    Write-Host "[FAIL] Mobile dependencies not installed. Run: cd mobile; nvm use 20; npm install" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Check Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "If all checks passed, you are ready to run: .\start-dev.ps1" -ForegroundColor Green
Write-Host "For detailed setup instructions, see: DEV_SETUP.md" -ForegroundColor Cyan
Write-Host ""
pause
