#!/bin/bash
echo "🚀 Salespilot Deployment Script"
echo "================================"

set -e  # Exit on error

cd /opt/salespilot || { echo "❌ Cannot cd to /opt/salespilot"; exit 1; }

echo "1. Checking if containers are running..."
if ! docker-compose ps | grep -q "Up"; then
    echo "Containers not running, starting them first..."
    docker-compose up -d
    sleep 10
fi

echo ""
echo "2. Creating backup..."
if [ -f "backup_enhanced.sh" ]; then
    ./backup_enhanced.sh
else
    echo "Using quick backup..."
    BACKUP_DIR="/backup"
    mkdir -p $BACKUP_DIR
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    # Only backup if MySQL container exists
    if docker ps --filter "name=salespilot-mysql" | grep -q "salespilot-mysql"; then
        docker exec salespilot-mysql mysqldump -u root -pOluwaTobi60 --all-databases > $BACKUP_DIR/deploy_$TIMESTAMP.sql 2>/dev/null
        gzip $BACKUP_DIR/deploy_$TIMESTAMP.sql 2>/dev/null || echo "Backup compression failed"
    else
        echo "MySQL container not found, skipping database backup"
    fi
fi

echo ""
echo "3. Pulling latest code..."
git fetch origin
git pull origin master

echo ""
echo "4. Deploying containers..."
echo "Restarting containers..."
docker-compose restart 2>/dev/null || {
    echo "Restart failed, trying stop/start..."
    docker-compose stop 2>/dev/null
    docker-compose up -d --build
}

echo ""
echo "5. Waiting for startup..."
sleep 15

echo ""
echo "6. Running Laravel commands..."
docker-compose exec -T php php artisan storage:link 2>/dev/null || echo "Storage link already exists"
docker-compose exec -T php php artisan migrate --force
docker-compose exec -T php php artisan config:clear
docker-compose exec -T php php artisan cache:clear
docker-compose exec -T php php artisan route:clear
docker-compose exec -T php php artisan view:clear
docker-compose exec -T php php artisan optimize

echo ""
echo "7. Setting permissions..."
docker-compose exec -T php chmod -R 775 storage bootstrap/cache
docker-compose exec -T php chmod -R 775 public/uploads/item_images public/uploads/staff_photos public/uploads/business_logos public/business_logos 2>/dev/null || echo "Upload folders already writable"

echo ""
echo "6. Verifying deployment..."
echo "Containers:"
docker-compose ps

echo ""
echo "Application check:"
if curl -s -f --max-time 10 http://localhost:8787 > /dev/null; then
    echo "✅ Application is running on port 8787"
    echo ""
    echo "✅ Deployment completed successfully!"
    echo "Version: $(git log --oneline -1)"
else
    echo "❌ Application check failed"
    echo "Trying alternative check..."
    docker-compose logs --tail=20
    exit 1
fi
