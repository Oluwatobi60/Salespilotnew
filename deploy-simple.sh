#!/bin/bash
echo "🚀 Salespilot Deployment (Simple)"
echo "================================="

set -e

cd /opt/salespilot

echo "1. Backup..."
[ -f "backup_enhanced.sh" ] && ./backup_enhanced.sh || echo "Skipping backup"

echo ""
echo "2. Pull code..."
git pull origin master

echo ""
echo "3. Restart containers..."
# Use docker-compose restart instead of down/up to avoid network issues
docker-compose restart

echo ""
echo "4. Laravel setup..."
docker-compose exec -T php php artisan storage:link 2>/dev/null || echo "Storage link exists"
docker-compose exec -T php php artisan migrate --force
docker-compose exec -T php php artisan optimize
docker-compose exec -T php chmod -R 775 storage bootstrap/cache public/uploads/item_images public/uploads/staff_photos public/uploads/business_logos public/business_logos 2>/dev/null

echo ""
echo "5. Wait and check..."
sleep 10

if docker-compose ps | grep -q "Up" && curl -s -f http://localhost:8787 > /dev/null; then
    echo "✅ Deployment successful"
    echo "Version: $(git log --oneline -1)"
else
    echo "❌ Deployment failed - trying full restart..."
    docker-compose stop
    docker-compose up -d
    sleep 15
    curl -s -f http://localhost:8787 && echo "✅ Fixed with full restart" || echo "❌ Still failing"
fi
