#!/bin/bash

# Laravel Absensi Deployment Script
# Jalankan script ini setelah upload ke hosting

echo "🚀 Starting Laravel Absensi Deployment..."

# 1. Install composer dependencies (production only)
echo "📦 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# 2. Copy environment file
echo "⚙️ Setting up environment..."
cp .env.production .env

# 3. Generate application key (if needed)
echo "🔑 Generating application key..."
php artisan key:generate --force

# 4. Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# 5. Cache configurations
echo "🚄 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set proper permissions
echo "🔒 Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/uploads

# 7. Clear any existing caches
echo "🧹 Clearing old caches..."
php artisan cache:clear
php artisan queue:restart

echo "✅ Deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "1. Update .env with correct database credentials"
echo "2. Update APP_URL with your domain"
echo "3. Configure mail settings if needed"
echo "4. Test all major functionalities"
echo "5. Set up cron job for scheduled tasks (if any)"
echo ""
echo "🎉 Your Laravel Absensi application is ready!"
