# THRD Development Scripts

## Quick Start

From the **project root**, run:
```powershell
.\scripts\start-dev.bat
```

This starts all three services using concurrently:
- **Soketi** (Node 18) on http://localhost:6001
- **Laravel API** on http://localhost:8000
- **Expo** (Node 22) - Check terminal for QR code

Press `Ctrl+C` to stop all services.

## Prerequisites

- Node.js 18 and 22 installed via nvm
- Soketi installed globally: `nvm use 18; npm install -g @soketi/soketi`
- PHP and Composer
- Dependencies installed in `/api` and `/mobile` folders

## Files

- `package.json` - Contains concurrently setup
- `run-soketi.js` - Soketi launcher (Node 18)
- `run-laravel.js` - Laravel API launcher
- `run-expo.js` - Expo launcher (Node 22)
- `start-dev.bat` - Main entry point (batch)
- `start-dev.ps1` - Main entry point (PowerShell)

## Manual Scripts

Run individual services:
- `.\start-soketi.bat` - Start Soketi only
- `.\start-laravel.bat` - Start Laravel only
- `.\start-expo.bat` - Start Expo only
