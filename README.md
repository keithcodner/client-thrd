# THRD Application

## Quick Start

To start the development environment:

```bash
.\scripts\start-dev.bat
```

Or with PowerShell:

```powershell
.\scripts\start-dev.ps1
```

This will start:
- Soketi (WebSocket server) on Node 18 - http://localhost:6001
- Laravel API on http://localhost:8000
- Expo (React Native) on Node 22

Press `Ctrl+C` to stop all services.

## Documentation

### Quick Links
- **Development Setup**: [scripts/README.md](scripts/README.md)
- **Real-Time Messaging** (Frontend): [mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md](mobile/docs/WEBSOCKETS_REALTIME_MESSAGING.md)
- **Broadcasting Setup** (Backend): [api/docs/TECH_DOCS/realtime_broadcasting_setup.md](api/docs/TECH_DOCS/realtime_broadcasting_setup.md)
- **Navigation Architecture**: [mobile/docs/NAVIGATION_ARCHITECTURE.md](mobile/docs/NAVIGATION_ARCHITECTURE.md)
- **Chat Management**: [mobile/docs/CHAT_MESSAGING.md](mobile/docs/CHAT_MESSAGING.md)

### Technical Documentation
- **Circles & Conversations**: [api/docs/TECH_DOCS/circles_and_conversations.md](api/docs/TECH_DOCS/circles_and_conversations.md)
- **Notifications System**: [api/docs/TECH_DOCS/notifications_system.md](api/docs/TECH_DOCS/notifications_system.md)
