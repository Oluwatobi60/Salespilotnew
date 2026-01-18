#!/bin/bash
echo "🔧 Fixing Laravel Issues"
echo "========================"

cd /opt/salespilot

# 1. Fix permissions
echo "1. Fixing permissions..."
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 2. Install dependencies if missing
echo "2. Checking dependencies..."
if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    docker-compose run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 3. Clear caches
echo "3. Clearing Laravel caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear

# 4. Check .env
echo "4. Checking .env file..."
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo "Creating .env from .env.example"
        cp .env.example .env
        docker-compose exec app php artisan key:generate
    fi
fi

# 5. Restart services
echo "5. Restarting services..."
docker-compose restart app

# 6. Test
echo "6. Testing..."
sleep 5
curl -s http://localhost:8787 | head -c 100
echo ""
echo "✅ Fix completed"
