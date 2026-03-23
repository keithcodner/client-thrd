# Real-Time Broadcasting Setup & Troubleshooting

## Overview

This document covers the Laravel backend configuration for real-time messaging using Soketi WebSocket server.

**Related Documentation:**
- Frontend implementation: `mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md`
- Soketi startup guide: `SOKETI_STARTUP_GUIDE.md` (if exists)

---

## Quick Start

### 1. Environment Configuration

**File: `api/.env`**

```env
# Broadcasting
BROADCAST_CONNECTION=pusher

# Queue (must run worker if set to database)
QUEUE_CONNECTION=database  # or 'sync' for development

# Soketi WebSocket Server
PUSHER_APP_ID=thrd-app
PUSHER_APP_KEY=thrd-app-key
PUSHER_APP_SECRET=thrd-app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

### 2. Start Required Services

```bash
# 1. Start Soketi WebSocket Server
cd api
node_modules\.bin\soketi start --config=soketi.config.json

# 2. Start Laravel API Server
cd api
php artisan serve --host=0.0.0.0

# 3. Start Queue Worker (if QUEUE_CONNECTION=database)
cd api
php artisan queue:work --tries=3
```

**Automated Start:**
Using `api/start-laravel.bat` automatically starts both Laravel server and queue worker in separate windows.

### 3. Verify Setup

```bash
# Check Soketi is running
curl http://127.0.0.1:6001/
# Expected: {"soketi":"Welcome!"}

# Check Laravel is running
curl http://localhost:8000/api
# Should return API response

# Check active connections
netstat -ano | findstr :6001
netstat -ano | findstr :8000
```

---

## Architecture

### Broadcasting Flow

```
User sends message
    ↓
POST /api/post-chat (ChatController)
    ↓
Save to database (conversation_chats table)
    ↓
Dispatch NewChatMessage event
    ↓
Queue job (if QUEUE_CONNECTION=database)
    ↓
Queue worker processes job
    ↓
Broadcast to Soketi (Pusher protocol)
    ↓
Soketi broadcasts to subscribed WebSocket clients
    ↓
Frontend receives message via pusher-js
```

### Key Components

**1. Event: `NewChatMessage`**
- File: `api/app/Events/NewChatMessage.php`
- Implements `ShouldBroadcast`
- Channel: `private-sitePrivateChat.{conversation_id}`
- Event name: `newMessage`

**2. Controller: `ChatController::postChat`**
- Saves message to database
- Broadcasts event: `broadcast(new NewChatMessage($message))->toOthers()`
- `toOthers()` excludes sender from receiving their own message via WS

**3. Channel Authorization**
- File: `api/routes/channels.php`
- Verifies user has access to conversation
- Checks circle membership or 1-to-1 conversation participants

---

## Broadcasting Configuration

### Config File: `api/config/broadcasting.php`

```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'host' => env('PUSHER_HOST', '127.0.0.1'),
            'port' => env('PUSHER_PORT', 6001),
            'scheme' => env('PUSHER_SCHEME', 'http'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME') === 'https',
        ],
    ],
],
```

### Enable Broadcasting Provider

**File: `api/bootstrap/providers.php`**

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,  // ✅ Must be enabled
];
```

---

## Queue Configuration

### Why Queue Workers Are Needed

Broadcasts implementing `ShouldBroadcast` are **queued by default** to prevent blocking the HTTP response. If `QUEUE_CONNECTION=database`, jobs are stored in the `jobs` table and processed by a worker.

### Option 1: Sync Queue (Development)

```env
QUEUE_CONNECTION=sync
```

**Pros:**
- No queue worker needed
- Broadcasts fire immediately
- Simple development setup

**Cons:**
- Blocks HTTP response while broadcasting
- Not suitable for production

**Apply changes:**
```bash
php artisan config:clear
```

### Option 2: Database Queue + Worker (Production)

```env
QUEUE_CONNECTION=database
```

**Start worker:**
```bash
php artisan queue:work --tries=3 --timeout=30
```

**Pros:**
- Non-blocking broadcasts
- Failed jobs can be retried
- Production-ready

**Monitor queues:**
```bash
# View failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Flush failed jobs
php artisan queue:flush

# Real-time monitoring (requires Laravel Horizon)
php artisan horizon
```

---

## Soketi Configuration

### Config File: `api/soketi.config.json`

```json
{
  "debug": false,
  "port": 6001,
  "appManager.array.apps": [
    {
      "id": "thrd-app",
      "key": "thrd-app-key",
      "secret": "thrd-app-secret",
      "enableClientMessages": true,
      "enabled": true
    }
  ],
  "cors.credentials": true,
  "cors.origin": ["*"]
}
```

**Important:** Use dot-notation format `"appManager.array.apps"` to prevent crashes.

### Start Soketi

```bash
# Development
node_modules\.bin\soketi start --config=soketi.config.json

# Production with PM2
pm2 start soketi --name thrd-websocket -- start --config=soketi.config.json

# Docker
docker run -p 6001:6001 -v $(pwd)/soketi.config.json:/app/config.json \
  quay.io/soketi/soketi:latest-16-alpine start --config=/app/config.json
```

---

## CORS Configuration

### For Web & Mobile Access

**File: `api/config/cors.php`**

```php
'allowed_origins' => [
    'http://localhost:8081',   // Expo web (development)
    'http://10.0.0.12:8081',   // Expo mobile (development - your IP)
    // Add production domains here
],

'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

**Apply changes:**
```bash
php artisan config:clear
```

---

## Troubleshooting

### Issue: Messages Don't Appear in Real-Time

**Symptoms:**
- Message saves to database
- No broadcast events in Soketi logs
- Works after page refresh

**Diagnosis:**
```bash
# Check if queue worker is running
ps aux | grep queue:work

# Check pending jobs
php artisan queue:work --once  # Process one job manually
```

**Solutions:**

1. **If using database queue:** Start worker
   ```bash
   php artisan queue:work --tries=3
   ```

2. **Switch to sync queue (dev only):**
   ```env
   QUEUE_CONNECTION=sync
   ```
   Then: `php artisan config:clear`

3. **Check failed jobs:**
   ```bash
   php artisan queue:failed
   php artisan queue:retry all
   ```

---

### Issue: Soketi Crashes on Startup

**Symptoms:**
```
TypeError: Cannot read properties of undefined (reading 'enabled')
```

**Cause:** Incorrect config format

**Solution:** Use dot-notation in `soketi.config.json`:
```json
{
  "appManager.array.apps": [...]  // ✅ Correct
}
```

NOT:
```json
{
  "appManager": {
    "array": {
      "apps": [...]  // ❌ Wrong
    }
  }
}
```

---

### Issue: "CORS policy" Errors from Frontend

**Symptoms:**
```
Access to fetch at 'http://localhost:8000/broadcasting/auth' from origin 
'http://localhost:8081' has been blocked by CORS policy
```

**Solution:**

1. Add origin to `api/config/cors.php`:
   ```php
   'allowed_origins' => [
       'http://localhost:8081',
       'http://10.0.0.12:8081',  // Your local network IP
   ],
   ```

2. Clear config:
   ```bash
   php artisan config:clear
   ```

3. Verify CORS headers:
   ```bash
   curl -H "Origin: http://localhost:8081" \
        -H "Access-Control-Request-Method: POST" \
        -X OPTIONS \
        http://localhost:8000/broadcasting/auth -v
   ```

---

### Issue: Channel Authorization Fails

**Symptoms:**
- WebSocket subscribes but immediately fails
- Frontend logs: `pusher:subscription_error`

**Diagnosis:**
Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

**Common Causes:**

1. **User not authenticated:**
   - Check `Authorization: Bearer {token}` header
   - Verify token is valid in database

2. **User not authorized for channel:**
   - Check `routes/channels.php` authorization logic
   - Verify user is circle member or conversation participant

3. **Function redeclaration:**
   ```php
   // In routes/channels.php
   if (!function_exists('authorizeConversationAccess')) {
       function authorizeConversationAccess($user, $conversationId) {
           // ...
       }
   }
   ```

---

### Issue: Port 6001 Already in Use

**Symptoms:**
```
Error: listen EADDRINUSE: address already in use :::6001
```

**Find process using port:**
```bash
# Windows
netstat -ano | findstr :6001
taskkill /PID <pid> /F

# Linux/Mac
lsof -i :6001
kill -9 <pid>
```

---

### Debugging Commands

```bash
# Clear all Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Check broadcasting routes
php artisan route:list --path=broadcasting

# Test broadcasting manually (Tinker)
php artisan tinker
>>> broadcast(new App\Events\NewChatMessage($message));

# Monitor queue in real-time
php artisan queue:work --verbose

# Check database jobs table
php artisan tinker
>>> DB::table('jobs')->count();
>>> DB::table('jobs')->get();

# Check failed jobs
php artisan queue:failed
php artisan queue:retry all
```

---

## Production Deployment

### Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Use SSL/TLS for Soketi (`PUSHER_SCHEME=https`)
- [ ] Restrict CORS origins to production domains
- [ ] Use supervised queue worker (Supervisor/PM2)
- [ ] Monitor queue with Laravel Horizon
- [ ] Set up Soketi monitoring/alerts
- [ ] Configure proper logging
- [ ] Secure `.env` file (not in git)
- [ ] Use environment-specific credentials

### Supervisor Config for Queue Worker

**File: `/etc/supervisor/conf.d/laravel-worker.conf`**

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/api/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/api/storage/logs/worker.log
stopwaitsecs=3600
```

Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### PM2 Config for Soketi

```bash
pm2 start node_modules/.bin/soketi \
  --name thrd-websocket \
  -- start --config=soketi.config.json

pm2 save
pm2 startup
```

---

## Testing

### Manual Broadcast Test

```php
// In routes/web.php or Tinker
use App\Events\NewChatMessage;
use App\Models\Conversation\ConversationChat;

$message = ConversationChat::find(1);
broadcast(new NewChatMessage($message));
```

### Test with Curl

```bash
# Broadcast directly to Soketi (bypass Laravel)
curl -X POST http://127.0.0.1:6001/apps/thrd-app/events \
  -H "Content-Type: application/json" \
  -d '{
    "name": "test-event",
    "channel": "private-sitePrivateChat.12",
    "data": "{\"message\":\"test\"}"
  }'
```

### Test Channel Authorization

```bash
# Get auth token
TOKEN="your-bearer-token"

# Test broadcasting auth endpoint
curl -X POST http://localhost:8000/broadcasting/auth \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "socket_id": "123.456",
    "channel_name": "private-sitePrivateChat.12"
  }'
```

---

## Performance Tips

### Optimize Queue Processing

```env
# Process multiple jobs per worker
QUEUE_CONNECTION=database
```

```bash
# Run multiple workers
php artisan queue:work --queue=default --tries=3 &
php artisan queue:work --queue=default --tries=3 &
php artisan queue:work --queue=default --tries=3 &
```

### Use Redis for Better Performance

```env
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=pusher  # Still use Soketi
CACHE_DRIVER=redis
```

### Soketi Clustering

For high-traffic apps, run multiple Soketi instances behind a load balancer with Redis adapter.

---

## Monitoring

### Laravel Telescope (Development)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

View broadcasts: http://localhost:8000/telescope/broadcasts

### Laravel Horizon (Production)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

Dashboard: http://localhost:8000/horizon

---

## Related Files

- **Event:** `api/app/Events/NewChatMessage.php`
- **Controller:** `api/app/Http/Controllers/Chat/ChatController.php`
- **Channels:** `api/routes/channels.php`
- **Config:** `api/config/broadcasting.php`
- **Soketi:** `api/soketi.config.json`
- **Startup:** `api/start-laravel.bat`
- **Frontend:** `mobile/services/websocketService.ts`

---

**Last Updated:** March 23, 2026  
**Status:** ✅ Production-ready real-time broadcasting with Soketi
