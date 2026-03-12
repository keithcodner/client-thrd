# THRD: Social Scheduling & Cultural Discovery Platform

## Product Overview

**THRD** is a social scheduling and cultural discovery platform designed for multi-hyphenates, creatives, entrepreneurs, artists, and hobbyists with complex schedules who want to maintain meaningful social lives.

The platform combines AI-powered scheduling, cultural discovery, community hubs, analytics, and mindful design to create intentional, non-overstimulating digital social spaces.

## Project Structure

This monorepo contains three main components:

### [`api/`](api/)
Backend Laravel application providing REST APIs for the THRD platform.

**Key Features:**
- User authentication and authorization
- Product/circle item management
- Transaction and order processing
- Calendar synchronization
- Messaging and conversations
- Professional networking
- Ranking and reputation system
- Content moderation and reporting
- Credit/payment system with Stripe integration

**Setup:** See [api/README.md](api/README.md)

### [`mobile/`](mobile/)
React Native Expo application for iOS and Android.

**Key Features:**
- Welcome and onboarding flow
- Theme selection (light/dark mode)
- User registration wizard with profile setup
- Credit package purchases
- Home dashboard with feature cards
- Calendar integration UI
- Responsive design with NativeWind

**Setup:** See [mobile/README.md](mobile/README.md)

## Technology Stack

**Backend:**
- Laravel (PHP framework)
- MySQL database
- Stripe for payments
- Mailpit for email testing

**Mobile:**
- React Native with Expo
- TypeScript
- NativeWind (Tailwind CSS for React Native)
- Lucide icons
- Linear Gradient animations

## Getting Started

### Prerequisites
- Node.js 18+ (for mobile)
- PHP 8+ and Composer (for API)
- MySQL 8.0+
- Expo CLI (`npm install -g expo-cli`)

### Quick Start

**Mobile Development:**
```bash
cd mobile
npm install
npm start
# or run on iOS/Android
npm run ios
npm run android
```

**API Development:**
```bash
cd api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Key Features

### 🗓️ AI-Powered Scheduling
- Smart calendar analysis to find optimal meeting times
- Integration with popular calendar services
- Respects complex, multi-hyphenate schedules

### 🎨 Cultural Discovery
- Explore events, products, and circle items
- Community hubs and spaces
- Personalized recommendations

### 👥 Social Connectivity
- Meaningful connections with like-minded people
- Professional networking
- Subscription and follow system
- Direct messaging and conversations

### 💳 Transaction System
- Credit-based economy
- Stripe payment integration
- Product trading and wishlist features
- Order management and history

### 🎯 Community & Mindfulness
- Non-overstimulating design
- Ranking and reputation system
- Content moderation tools
- User feedback and reporting mechanisms

## Database

The system uses MySQL with multiple schema versions available in [`api/database/core/`](api/database/core/).

## Development Workflow

1. **Authentication:** Both mobile and API use session-based auth with personal access tokens
2. **Theming:** Mobile supports light/dark/system themes via context and custom hooks
3. **Types:** TypeScript types for mobile, PHP types/validation for API
4. **Styling:** NativeWind (mobile) and Tailwind classes; custom theme colors

## Configuration

### Mobile
- Theme context: [`mobile/context/ThemeContext.tsx`](mobile/context/ThemeContext.tsx)
- Colors hook: [`mobile/hooks/useThemeColours.tsx`](mobile/hooks/useThemeColours.tsx)
- Environment: [`mobile/config/env.ts`](mobile/config/env.ts)

### API
- Database config: Set in `.env` file
- Email testing: Mailpit available in [`api/tools/`](api/tools/)

## Documentation

- [API Documentation](api/README.md)
- [Mobile Documentation](mobile/README.md)

---

**THRD** — *Find time. Build community. Live intentionally.* 🎨✨
