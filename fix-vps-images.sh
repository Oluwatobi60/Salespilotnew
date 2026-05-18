#!/bin/bash
echo "🔧 Fix VPS Upload Images Issue"
echo "================================"

set -e

cd /opt/salespilot || { echo "❌ Cannot cd to /opt/salespilot"; exit 1; }

echo "1. Creating symbolic link for storage..."
docker-compose exec -T php php artisan storage:link || echo "Link already exists"

echo ""
echo "2. Setting proper permissions..."
docker-compose exec -T php chmod -R 775 storage bootstrap/cache
docker-compose exec -T php chmod -R 775 public/uploads/item_images public/uploads/staff_photos public/uploads/business_logos public/business_logos || true
docker-compose exec -T php chown -R www-data:www-data storage bootstrap/cache public/uploads public/business_logos || true

echo ""
echo "3. Ensuring upload directories exist..."
docker-compose exec -T php mkdir -p public/uploads/item_images
docker-compose exec -T php mkdir -p public/uploads/staff_photos
docker-compose exec -T php mkdir -p public/uploads/business_logos
docker-compose exec -T php mkdir -p public/business_logos
docker-compose exec -T php mkdir -p storage/app/public/settings

echo ""
echo "4. Setting permissions on upload directories..."
docker-compose exec -T php chmod -R 775 public/uploads/item_images
docker-compose exec -T php chmod -R 775 public/uploads/staff_photos
docker-compose exec -T php chmod -R 775 public/uploads/business_logos
docker-compose exec -T php chmod -R 775 public/business_logos
docker-compose exec -T php chmod -R 775 storage/app/public

echo ""
echo "5. Clearing caches..."
docker-compose exec -T php php artisan config:clear
docker-compose exec -T php php artisan cache:clear
docker-compose exec -T php php artisan view:clear
docker-compose exec -T php php artisan optimize

echo ""
echo "✅ Fix complete!"
echo ""
echo "IMPORTANT: Your uploaded images are now safe."
echo "Next time you deploy, images will NOT be reset."
echo ""
echo "If you still see default images, it means Git already overwrote them."
echo "You'll need to re-upload those images manually."
