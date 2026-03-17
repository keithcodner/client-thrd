# Deployment Guide

This document outlines the steps to deploy the API and mobile app for production.

## API Deployment

### 1. Set Up the Server
1. Choose a hosting provider (e.g., AWS, DigitalOcean, Heroku, etc.).
2. Install the required software on the server:
   - PHP (>= 8.0)
   - Composer
   - A web server (e.g., Apache or Nginx)
   - MySQL or another supported database

### 2. Clone the Repository
1. SSH into your server.
2. Clone the repository:
   ```bash
   git clone <repository-url>
   ```
3. Navigate to the `api` directory:
   ```bash
   cd api
   ```

### 3. Install Dependencies
1. Install PHP dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. Install Node.js dependencies (if needed):
   ```bash
   npm install --production
   ```

### 4. Configure Environment Variables
1. Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```
2. Update the `.env` file with production values:
   - Database credentials
   - API keys
   - Other environment-specific settings

### 5. Set Up the Database
1. Run migrations:
   ```bash
   php artisan migrate --force
   ```
2. (Optional) Seed the database:
   ```bash
   php artisan db:seed --force
   ```

### 6. Set Permissions
Ensure the `storage` and `bootstrap/cache` directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

### 7. Start the Server
1. Configure your web server to serve the `public` directory.
2. Restart the web server to apply changes.

## Mobile App Deployment

### 1. Build the App
1. Navigate to the `mobile` directory:
   ```bash
   cd ../mobile
   ```
2. Build the app for production:
   ```bash
   npx expo build:android
   npx expo build:ios
   ```
   - Follow the Expo instructions to generate the APK (Android) or IPA (iOS).

### 2. Configure API URL
1. Open the `mobile/config/env.ts` file.
2. Update the `API_URL` to point to the production API server:
   ```typescript
   export const API_URL = 'https://your-production-api-url.com';
   ```

### 3. Distribute the App
- For Android:
  - Upload the APK to the Google Play Console.
- For iOS:
  - Upload the IPA to the Apple App Store using Transporter or Xcode.

## Notes
- Use HTTPS for secure communication.
- Monitor the server and app performance using tools like New Relic or Sentry.
- Regularly back up the database and server files.