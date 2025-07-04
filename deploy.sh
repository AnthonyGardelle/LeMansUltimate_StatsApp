#!/bin/bash
set -e

cd /var/www/le-mans-ultimate-stats-app

echo "🔄 Pulling latest code..."
git pull

echo "📦 Installing PHP dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader

echo "📦 Installing JS dependencies..."
npm ci

echo "🛡️ Fixing known JS vulnerabilities..."
npm audit fix

echo "🏗️ Building frontend..."
npm run build

echo "🗃️ Running migrations..."
php artisan migrate --force

echo "♻️ Caching config/routes/views..."
php artisan optimize:clear

echo "🚦 Restarting Laravel queues (if used)..."
php artisan queue:restart

echo "✅ Deployment complete."
