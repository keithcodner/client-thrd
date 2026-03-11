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
