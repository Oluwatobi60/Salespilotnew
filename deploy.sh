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
# Use -v to remove volumes, --remove-orphans to clean up
docker-compose down --remove-orphans

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
