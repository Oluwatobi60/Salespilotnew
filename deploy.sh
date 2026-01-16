#!/bin/bash
echo "🚀 Salespilot Deployment Script"
echo "================================"

set -e  # Exit on error

cd /opt/salespilot

echo "1. Creating backup..."
if [ -f "backup_enhanced.sh" ]; then
    ./backup_enhanced.sh
else
    echo "Using quick backup..."
    BACKUP_DIR="/backup"
    mkdir -p $BACKUP_DIR
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    docker exec salespilot-mysql mysqldump -u root -pOluwaTobi60 --all-databases > $BACKUP_DIR/deploy_$TIMESTAMP.sql
    gzip $BACKUP_DIR/deploy_$TIMESTAMP.sql
fi

echo ""
echo "2. Pulling latest code..."
git fetch origin
git pull origin master

echo ""
echo "3. Deploying containers..."

# Try to stop and remove containers, but don't fail if network can't be removed
echo "Stopping containers..."
docker-compose stop || echo "Warning: Some containers already stopped"

echo "Removing containers..."
docker-compose rm -f || echo "Warning: Could not remove some containers"

# Try to remove network, but don't fail if it's in use
echo "Removing network (if possible)..."
docker network rm salespilot_salespilot-network 2>/dev/null || echo "Network in use by monitoring containers, skipping..."

echo ""
echo "4. Building and starting..."
docker-compose up -d --build

echo ""
echo "5. Waiting for startup..."
sleep 15

echo ""
echo "6. Verifying deployment..."
echo "Containers:"
docker-compose ps

echo ""
echo "Application check:"
if curl -s -f http://localhost:8787 > /dev/null; then
    echo "✅ Application is running on port 8787"
else
    echo "❌ Application check failed"
    exit 1
fi

echo ""
echo "✅ Deployment completed successfully!"
echo "Version: $(git log --oneline -1)"
