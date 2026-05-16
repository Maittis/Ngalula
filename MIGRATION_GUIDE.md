# Ngalula Wellness Center - Tech Stack Migration Guide

## Overview

This guide outlines the migration of the Ngalula Wellness Center application from the current stack to a modern, scalable architecture.

## Current Stack → New Stack

### Backend

- **Current**: Laravel 10.x with MySQL
- **New**: Laravel 12.x with PostgreSQL

### Frontend (Web)

- **Current**: Blade templates with basic JavaScript
- **New**: Laravel Filament for admin dashboard

### Mobile App

- **Current**: None
- **New**: Flutter cross-platform mobile application

### Real-time Features

- **Current**: None
- **New**: Laravel WebSockets

### Notifications

- **Current**: Basic email notifications
- **New**: Firebase Cloud Messaging + WhatsApp API

### Database

- **Current**: MySQL
- **New**: PostgreSQL with advanced features

## Migration Steps

### 1. Environment Setup

#### Prerequisites

- PHP 8.2+
- PostgreSQL 14+
- Redis 6+
- Node.js 18+
- Flutter SDK
- Firebase project setup

#### Environment Variables

Update your `.env` file with the new configuration:

```env
# Database Configuration (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ngalula
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Broadcasting Configuration (Laravel WebSockets)
BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Firebase Configuration
FIREBASE_PROJECT_ID=ngalula-wellness
FIREBASE_CREDENTIALS_FILE=config/firebase-credentials.json

# WhatsApp API Configuration
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886

# Filament Configuration
FILAMENT_FILESYSTEM_DISK=public
```

### 2. Database Migration

#### Step 1: Backup Current Database

```bash
mysqldump -u root -p ngalula > backup_before_migration.sql
```

#### Step 2: Create PostgreSQL Database

```sql
CREATE DATABASE ngalula;
CREATE USER ngalula_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE ngalula TO ngalula_user;
```

#### Step 3: Install PostgreSQL Driver

```bash
# Ubuntu/Debian
sudo apt-get install php-pgsql

# macOS
brew install postgresql

# Windows
# Download and install PostgreSQL from official site
```

#### Step 4: Run Migration

```bash
php artisan migrate
```

#### Step 5: Run PostgreSQL Migration

```bash
php artisan migrate --path=database/migrations/2026_05_11_150000_migrate_to_postgresql.php
```

### 3. Laravel Upgrade

#### Step 1: Update Composer Dependencies

```bash
composer update
```

#### Step 2: Update Configuration Files

- Update `config/database.php` for PostgreSQL
- Update `config/broadcasting.php` for WebSockets
- Update `config/cache.php` for Redis
- Update `config/queue.php` for Redis

#### Step 3: Update Models

- Update model relationships for PostgreSQL compatibility
- Update query builder usage for PostgreSQL-specific features

### 4. Laravel Filament Setup

#### Step 1: Install Filament

```bash
composer require filament/filament
php artisan vendor:publish --tag=filament-config
php artisan migrate
```

#### Step 2: Create Admin User

```bash
php artisan make:filament-user
```

#### Step 3: Configure Filament

- Update `config/filament.php`
- Create custom resources
- Configure navigation

#### Step 4: Create Resources

```bash
php artisan make:filament-resource Product --generate
php artisan make:filament-resource Service --generate
php artisan make:filament-resource Booking --generate
php artisan make:filament-resource User --generate
```

### 5. Laravel WebSockets Setup

#### Step 1: Install WebSockets

```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
```

#### Step 2: Configure WebSockets

```bash
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

#### Step 3: Start WebSocket Server

```bash
php artisan websockets:serve
```

### 6. Firebase Integration

#### Step 1: Install Firebase Package

```bash
composer require kreait/laravel-firebase
```

#### Step 2: Configure Firebase

- Add Firebase credentials file
- Update `config/firebase.php`
- Configure FCM

#### Step 3: Set Up FCM

```bash
php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider"
```

### 7. WhatsApp API Integration

#### Step 1: Install Twilio Package

```bash
composer require twilio/sdk
```

#### Step 2: Configure WhatsApp

- Set up Twilio account
- Configure WhatsApp Business API
- Update `config/whatsapp.php`

### 8. Flutter App Setup

#### Step 1: Install Flutter

```bash
# Download Flutter SDK from https://flutter.dev/docs/get-started/install
flutter doctor
```

#### Step 2: Create Flutter Project

```bash
cd flutter_app
flutter pub get
```

#### Step 3: Configure Firebase

- Add Firebase configuration files
- Install Flutter Firebase packages
- Configure FCM

#### Step 4: Run Flutter App

```bash
flutter run
```

## Testing the Migration

### 1. Backend Testing

```bash
# Run tests
php artisan test

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### 2. Frontend Testing

- Access Filament admin panel at `/admin`
- Test all CRUD operations
- Verify data integrity

### 3. Mobile App Testing

- Run Flutter app on emulator/device
- Test API connectivity
- Verify real-time features

### 4. WebSocket Testing

- Test real-time notifications
- Verify WebSocket connection
- Test live updates

## Performance Optimizations

### 1. Database Optimizations

- Use PostgreSQL-specific indexes
- Implement materialized views
- Use stored procedures for complex queries

### 2. Caching Strategy

- Redis for session storage
- Redis for caching
- File caching for static content

### 3. API Optimizations

- Implement API rate limiting
- Use query optimization
- Implement pagination

## Security Considerations

### 1. Authentication

- Use Laravel Sanctum for API authentication
- Implement two-factor authentication
- Use secure password policies

### 2. Data Protection

- Encrypt sensitive data
- Use HTTPS everywhere
- Implement CORS policies

### 3. API Security

- Validate all inputs
- Use rate limiting
- Implement API versioning

## Monitoring and Logging

### 1. Application Monitoring

- Use Laravel Telescope for debugging
- Implement health checks
- Monitor performance metrics

### 2. Error Handling

- Implement comprehensive error logging
- Use error tracking services
- Set up alerting

### 3. Analytics

- Implement Firebase Analytics
- Track user behavior
- Monitor app performance

## Deployment

### 1. Backend Deployment

- Use Docker containers
- Implement CI/CD pipeline
- Configure load balancing

### 2. Database Deployment

- Use managed PostgreSQL service
- Implement backup strategy
- Configure replication

### 3. Mobile App Deployment

- Publish to App Store
- Publish to Google Play
- Implement OTA updates

## Rollback Plan

### 1. Database Rollback

```bash
# Restore from backup
psql -U ngalula_user -d ngalula < backup_before_migration.sql
```

### 2. Application Rollback

- Use Git to revert changes
- Restore previous environment configuration
- Rollback migrations

## Support and Maintenance

### 1. Documentation

- Keep API documentation updated
- Document custom configurations
- Maintain deployment guides

### 2. Regular Maintenance

- Update dependencies regularly
- Monitor security vulnerabilities
- Perform regular backups

### 3. User Support

- Provide training materials
- Set up support channels
- Create FAQ documentation

## Conclusion

This migration provides a modern, scalable foundation for the Ngalula Wellness Center application. The new tech stack offers:

- Better performance with PostgreSQL
- Modern admin interface with Filament
- Cross-platform mobile app with Flutter
- Real-time features with WebSockets
- Advanced notifications with Firebase
- Enhanced messaging with WhatsApp API

Follow this guide carefully and test each step thoroughly before proceeding to the next.
