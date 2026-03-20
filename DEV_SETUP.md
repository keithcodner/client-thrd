# Development Setup Guide

## Node Version Requirements

This project uses **different Node versions** for different parts:

- **API (Soketi)**: Requires Node 18 (dependency limitation)
- **Mobile (Expo)**: Requires Node 20+ (Expo SDK 54 requirement)

## Quick Start

### Option 1: Automated Startup (Recommended)

Run the PowerShell script from the project root:

```powershell
.\start-dev.ps1
```

Or double-click: `start-dev.bat`

This automatically opens 3 terminals with the correct Node versions and services running.

### Option 2: Individual Services

You can also start each service individually by double-clicking:

- **Soketi**: `api/start-soketi.bat`
- **Laravel API**: `api/start-laravel.bat`  
- **Expo**: `mobile/start-expo.bat`

Each script automatically switches to the correct Node version.

### Option 3: Manual Startup

Open **3 separate terminals**:

**Terminal 1 - Soketi (WebSocket Server)**
```powershell
cd api
nvm use 18
npm run soketi
# Runs on http://localhost:6001
```

**Terminal 2 - Laravel API Backend**
```powershell
cd api
php artisan serve
# Runs on http://localhost:8000
```

**Terminal 3 - Expo Mobile App**
```powershell
cd mobile
nvm use 20
npm run start:clean
# Scan QR code with Expo Go app
```

## .nvmrc Files

Each project has a `.nvmrc` file that specifies the required Node version:

- `api/.nvmrc` → Node 18
- `mobile/.nvmrc` → Node 20

When you `cd` into a directory, manually run:
```powershell
nvm use
```

This reads the `.nvmrc` file and switches to the correct version.

## NPM Scripts Available

### API (`/api`)
- `npm run soketi` - Start Soketi WebSocket server
- `npm run laravel` - Start Laravel API server
- `npm run dev` - Start Vite dev server (if using)
- `npm run dev:all` - Start all API services with concurrently (Note: requires Node 18)

### Mobile (`/mobile`)
- `npm start` - Start Expo normally
- `npm run start:clean` - Start Expo with cleared cache
- `npm run android` - Run on Android device
- `npm run ios` - Run on iOS device

## Troubleshooting

### "soketi: command not found"
Make sure soketi is installed globally on Node 18:
```powershell
nvm use 18
npm install -g @soketi/soketi
```

### Expo ESM URL Error
Make sure you're using Node 20 or higher:
```powershell
nvm use 20
node --version  # Should show v20.x.x
```

### Wrong Node Version
Check your current version:
```powershell
node --version
```

Switch to the correct version:
```powershell
nvm use 18  # for API/Soketi
nvm use 20  # for Mobile/Expo
```

## Development Workflow

1. Start all services using the automated script
2. Make changes to your code
3. Expo and Vite will auto-reload
4. Laravel changes may need manual refresh
5. Soketi runs as a persistent WebSocket server

## Port Configuration

- **Laravel API**: http://localhost:8000
- **Soketi WebSocket**: http://localhost:6001
- **Expo Metro**: Various (shown in terminal)
- **Vite**: http://localhost:5173 (if used)
