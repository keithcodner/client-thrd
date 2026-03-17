# Running the API Server

## Start the Development Server

To run the Laravel API server and access it from your physical phone on the same network:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### What this does:
- `--host=0.0.0.0` - Makes the server accessible from any device on your local network (not just localhost)
- `--port=8000` - Runs the server on port 8000

### Accessing from your phone:

1. Find your computer's local IP address:
   - **Windows**: Run `ipconfig` in terminal, look for "IPv4 Address" (e.g., `192.168.1.100`)
   - **Mac/Linux**: Run `ifconfig` or `ip addr`, look for your local IP

2. On your phone, access the API at:
   ```
   http://YOUR_COMPUTER_IP:8000
   ```
   Example: `http://192.168.1.100:8000`

3. Make sure your phone and computer are on the same WiFi network

### API Endpoints:

Base URL: `http://YOUR_COMPUTER_IP:8000/api`

**Authentication Endpoints:**
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)
- `GET /api/user` - Get authenticated user (requires auth)
- `POST /api/forgot-password` - Request password reset
- `POST /api/reset-password` - Reset password

### Troubleshooting:

- **Firewall**: Make sure Windows Firewall allows port 8000
- **Network**: Both devices must be on the same local network
- **Connection refused**: Try disabling your firewall temporarily or add an exception for port 8000

## Accessing the App Remotely via Expo

If you want to access the app remotely via Expo to demonstrate it to a client, follow these steps:

### 1. Start the API Server
1. Ensure you are in the `api` directory:
   ```bash
   cd api
   ```
2. Start the API server:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   - The `--host=0.0.0.0` option allows the server to be accessible from other devices on the same network.
   - Note the IP address of your machine (e.g., `192.168.x.x`). You will use this to configure the mobile app.

### 2. Start the Expo Bundler
1. Navigate to the `mobile` directory:
   ```bash
   cd ../mobile
   ```
2. Start the Expo development server:
   ```bash
   npx expo start --tunnel
   ```
   - The `--tunnel` option ensures the Expo bundler is accessible remotely, even if the devices are not on the same network.

### 3. Configure the Mobile App
1. Open the `mobile/config/env.ts` file.
2. Update the `API_URL` to point to your machine's IP address and port:
   ```typescript
   export const API_URL = 'http://192.168.x.x:8000';
   ```

### 4. Test the App
1. Open the Expo Go app on your mobile device.
2. Scan the QR code provided by the Expo bundler.
3. Verify that the app connects to the API and functions as expected.

### Notes
- Ensure both your development machine and the client device are connected to the internet.
- If using a firewall, allow connections to the specified port (e.g., `8000`).
- For a more stable demonstration, consider using a tool like [ngrok](https://ngrok.com/) to expose your local API server to the internet.
