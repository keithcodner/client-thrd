# Soketi WebSocket Server Startup Guide

## Prerequisites

⚠️ **Important: Node.js Version Requirement**

Soketi requires **Node.js version 14, 16, or 18**. It does NOT support Node.js 20+ yet.

**If using nvm (Node Version Manager):**
```powershell
# Check current version
node --version

# If running Node.js 20+, switch to Node.js 18
nvm install 18
nvm use 18

# Verify the version
node --version  # Should show v18.x.x
```

**If not using nvm:**
- Download Node.js 18 LTS from [nodejs.org](https://nodejs.org/)
- Or install nvm: [nvm-windows](https://github.com/coreybutler/nvm-windows) (Windows) or [nvm](https://github.com/nvm-sh/nvm) (Mac/Linux)

### Installing Soketi

Once you have Node.js 18 installed, install Soketi globally:

```powershell
# Install Soketi globally
npm install -g @soketi/soketi

# Verify installation
soketi --version
```

**Note:** If you get a permission error on Mac/Linux, use `sudo npm install -g @soketi/soketi`

---

## Starting Soketi

### Option 1: Using the config file (Recommended)
```bash
cd api
soketi start --config=soketi.config.json
```

### Option 2: Using command-line arguments
```bash
soketi start \
  --debug \
  --port=6001 \
  --app-id=thrd-app \
  --app-key=thrd-app-key \
  --app-secret=thrd-app-secret
```

### Option 3: Using Docker
```bash
docker run -p 6001:6001 \
  -e SOKETI_DEBUG=1 \
  -e SOKETI_DEFAULT_APP_ID=thrd-app \
  -e SOKETI_DEFAULT_APP_KEY=thrd-app-key \
  -e SOKETI_DEFAULT_APP_SECRET=thrd-app-secret \
  quay.io/soketi/soketi:latest-16-alpine
```

## Verifying Soketi is Running

1. **Check the console output** - You should see:
   ```
   📡 Soketi is now ready to accept connections on port 6001
   ```

2. **Test the connection** - Visit in browser:
   ```
   http://localhost:6001
   ```
   You should see Soketi's status page.

## Starting the Application

### 1. Start Soketi Server (Terminal 1)
```bash
cd api
soketi start --config=soketi.config.json
```

### 2. Start Laravel Backend (Terminal 2)
```bash
cd api
php artisan serve
```

### 3. Start React Native App (Terminal 3)
```bash
cd mobile
npx expo start
```

## Testing WebSockets

### 1. Test Broadcasting from Laravel
```bash
cd api
php artisan tinker
```

Then run:
```php
$user = App\Models\User::first();
$conversation = App\Models\Conversation\Conversation::first();

$chat = App\Models\Conversation\ConversationChat::create([
    'init_user_id' => $user->id,
    'conversation_id' => $conversation->id,
    'content' => 'Test WebSocket message',
    'type' => 'chat',
]);

broadcast(new App\Events\NewChatMessage($chat))->toOthers();
```

### 2. Check Soketi Logs
You should see activity in the Soketi console when:
- Users connect to the app
- Users subscribe to channels
- Messages are broadcast

### 3. Monitor in React Native
Check the Metro bundler console and React Native debugger for:
- ✅ WebSocket connected
- 📡 Subscribing to private-sitePrivateChat.{id}
- 📨 New message received

## Troubleshooting

### Issue: "soketi is not recognized as a command"
**Error:**
```
soketi : The term 'soketi' is not recognized as the name of a cmdlet, function, script file, or operable program.
```

**Solution:** Soketi is not installed. Install it globally:
```powershell
npm install -g @soketi/soketi

# Verify installation
soketi --version

# Now start Soketi
soketi start --config=soketi.config.json
```

### Issue: "This version of uWS.js supports only Node.js 14, 16 and 18"
**Error:**
```
Error: This version of uWS.js supports only Node.js 14, 16 and 18...
Cannot find module './uws_win32_x64_127.node'
```

**Solution:** You're running an incompatible Node.js version (20+). Switch to Node.js 18:
```powershell
# Check current version
node --version

# Using nvm (Node Version Manager)
nvm install 18
nvm use 18

# Verify
node --version  # Should show v18.x.x

# Now restart Soketi
soketi start --config=soketi.config.json
```

### Issue: Soketi won't start
**Solution:** Check if port 6001 is already in use:
```bash
# Windows
netstat -ano | findstr :6001

# macOS/Linux
lsof -i :6001
```

### Issue: "Connection refused" in mobile app
**Solutions:**
1. Make sure Soketi is running
2. Check firewall settings
3. Verify the IP address in `mobile/config/env.ts` matches your machine's IP
4. For local development, use your machine's local IP (e.g., 192.168.1.x or 10.0.0.x)

### Issue: "Unauthorized" when subscribing to private channels
**Solutions:**
1. Ensure user is authenticated (has valid Sanctum token)
2. Check `routes/channels.php` authorization logic
3. Verify `BroadcastServiceProvider` is enabled in `bootstrap/providers.php`
4. Check Laravel logs: `tail -f storage/logs/laravel.log`

### Issue: Messages not appearing in real-time
**Solutions:**
1. Check Soketi console for broadcast events
2. Verify WebSocket connection state in browser console
3. Ensure the event name matches: `newMessage`
4. Check that the channel name format is correct: `private-sitePrivateChat.{id}`

## Configuration Files

### Laravel (.env)
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=thrd-app
PUSHER_APP_KEY=thrd-app-key
PUSHER_APP_SECRET=thrd-app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

### React Native (mobile/config/env.ts)
```typescript
export const PUSHER_CONFIG = {
  key: 'thrd-app-key',
  wsHost: '10.0.0.12',  // Use your machine's IP
  wsPort: 6001,
  // ... other config
};
```

## Production Deployment

### 1. Use HTTPS/WSS
Update Soketi to use TLS:
```json
{
  "port": 6001,
  "ssl": {
    "certPath": "/path/to/cert.pem",
    "keyPath": "/path/to/key.pem"
  }
}
```

Update Laravel .env:
```env
PUSHER_SCHEME=https
```

### 2. Use a Process Manager
```bash
# Install PM2
npm install -g pm2

# Start Soketi with PM2
pm2 start soketi --name "soketi-server" -- start --config=soketi.config.json

# Save PM2 configuration
pm2 save

# Auto-start on boot
pm2 startup
```

### 3. Use Nginx as Reverse Proxy
```nginx
server {
    listen 443 ssl;
    server_name ws.thrd.app;

    location / {
        proxy_pass http://localhost:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

## Monitoring

### Check Soketi Stats
Soketi provides a metrics endpoint:
```bash
curl http://localhost:6001/metrics
```

### Laravel Telescope
Install Telescope to monitor events:
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Then visit: `http://localhost:8000/telescope`

## Next Steps

1. ✅ Implement typing indicators
2. ✅ Add online presence tracking
3. ✅ Create message history API
4. ✅ Add offline message queuing
5. ✅ Implement read receipts

See `mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md` for detailed implementation guides.
