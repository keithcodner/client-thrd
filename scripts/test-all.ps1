# PowerShell equivalent of test-all.sh for Windows development
# ---------------------------------------------------------------------------
# scripts/test-all.ps1
#
# Usage:
#   .\scripts\test-all.ps1
#   .\scripts\test-all.ps1 -TestEmail "me@example.com" -TestPassword "mypass"
# ---------------------------------------------------------------------------

param(
    [string]$TestEmail    = $env:TEST_EMAIL    ?? "test@example.com",
    [string]$TestPassword = $env:TEST_PASSWORD ?? "password",
    [int]   $ApiPort      = $env:API_PORT      ?? 8001
)

$ErrorActionPreference = "Stop"

$RootDir   = Split-Path $PSScriptRoot -Parent
$ApiDir    = Join-Path $RootDir "api"
$MobileDir = Join-Path $RootDir "mobile"
$MaestroDir = Join-Path $MobileDir "test\maestro"

$ApiProcess = $null

function Cleanup {
    if ($null -ne $ApiProcess -and -not $ApiProcess.HasExited) {
        Write-Host "[INFO] Stopping Laravel test server (PID $($ApiProcess.Id))..." -ForegroundColor Green
        Stop-Process -Id $ApiProcess.Id -Force -ErrorAction SilentlyContinue
    }
}

try {
    # =========================================================================
    # STEP 1 — Pest
    # =========================================================================
    Write-Host "[INFO] Running Pest tests..." -ForegroundColor Green
    Push-Location $ApiDir
    php artisan config:clear --env=testing 2>$null
    & .\vendor\bin\pest --colors=always
    if ($LASTEXITCODE -ne 0) { throw "Pest tests failed." }
    Pop-Location
    Write-Host "[INFO] Pest tests passed." -ForegroundColor Green

    # =========================================================================
    # STEP 2 — Boot Laravel test server
    # =========================================================================
    Write-Host "[INFO] Starting Laravel test server on port $ApiPort..." -ForegroundColor Green
    $ApiProcess = Start-Process php `
        -ArgumentList "artisan", "serve", "--host=127.0.0.1", "--port=$ApiPort", "--env=testing" `
        -WorkingDirectory $ApiDir `
        -PassThru `
        -RedirectStandardOutput "$env:TEMP\laravel-test-server.log" `
        -RedirectStandardError  "$env:TEMP\laravel-test-server-err.log"

    # Wait for server to be ready (up to 15 s)
    $ready = $false
    for ($i = 1; $i -le 15; $i++) {
        Start-Sleep -Seconds 1
        try {
            $r = Invoke-WebRequest -Uri "http://127.0.0.1:$ApiPort/api/user" -UseBasicParsing -ErrorAction SilentlyContinue
            $ready = $true; break
        } catch { }
    }
    if (-not $ready) { throw "Laravel test server did not start in 15s. Check $env:TEMP\laravel-test-server.log" }
    Write-Host "[INFO] Laravel test server is up." -ForegroundColor Green

    # =========================================================================
    # STEP 3 — Maestro E2E flows
    # =========================================================================
    Write-Host "[INFO] Running Maestro flows..." -ForegroundColor Green
    Push-Location $MaestroDir
    maestro test `
        --env TEST_EMAIL="$TestEmail" `
        --env TEST_PASSWORD="$TestPassword" `
        flows\login_success.yaml `
        flows\login_failure.yaml
    if ($LASTEXITCODE -ne 0) { throw "Maestro tests failed." }
    Pop-Location

    Write-Host "[INFO] All tests passed. ✓" -ForegroundColor Green

} finally {
    Cleanup
}
