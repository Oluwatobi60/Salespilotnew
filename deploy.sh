#!/bin/bash
set -e

echo "🚀 Deploying SalesPilot..."
echo "========================="
echo "Start time: $(date)"

cd /opt/salespilot

# Configure git
git config pull.rebase false 2>/dev/null || true

# Update code safely
if [ -d .git ]; then
    echo "📦 Updating code..."
    
    # Backup critical files
    cp docker-compose.yml /tmp/docker-compose-backup.yml 2>/dev/null || true
    cp .env /tmp/env-backup 2>/dev/null || true
    
    # Stash local changes to critical files
    git stash push -m "deploy-backup" -- docker-compose.yml .env deploy.sh 2>/dev/null || true
    
    # Pull changes
    git pull origin master || echo "⚠️  Git pull failed, continuing with existing code"
    
    # Restore stashed files if needed
    git stash pop 2>/dev/null || true
    
    # Restore backups if files were deleted
    if [ ! -f docker-compose.yml ] && [ -f /tmp/docker-compose-backup.yml ]; then
        cp /tmp/docker-compose-backup.yml docker-compose.yml
    fi
    if [ ! -f .env ] && [ -f /tmp/env-backup ]; then
        cp /tmp/env-backup .env
    fi
fi

# Ensure staff_id column exists
echo "🔧 Ensuring database structure..."
docker-compose exec -T app php artisan tinker --execute="
try {
    // Check if staff_id column exists
    \$result = DB::select(\"SHOW COLUMNS FROM staffs LIKE 'staff_id'\");
    
    if (empty(\$result)) {
        echo 'Adding staff_id column...\\n';
        DB::statement('ALTER TABLE staffs ADD COLUMN staff_id VARCHAR(255) NULL AFTER id');
        DB::statement('UPDATE staffs SET staff_id = id WHERE staff_id IS NULL');
        DB::statement('ALTER TABLE staffs ADD UNIQUE(staff_id)');
        echo '✅ staff_id column added.\\n';
    }
    
    // Ensure TIM702 exists
    \$exists = DB::table('staffs')->where('staff_id', 'TIM702')->exists();
    if (!\$exists) {
        DB::table('staffs')->insert([
            'staff_id' => 'TIM702',
            'name' => 'System User',
            'email' => 'system@example.com',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo '✅ Created TIM702 staff record.\\n';
    }
} catch (Exception \$e) {
    echo 'Database check: ' . \$e->getMessage() . '\\n';
}
"

# Restart services
echo "🐳 Restarting services..."
docker-compose down 2>/dev/null || true
sleep 2
docker-compose up -d --build

# Wait for services
echo "⏳ Waiting for services to start..."
sleep 30

# Check services
echo "🏥 Checking services..."
if docker ps | grep -q "salespilot-app" && docker ps | grep -q "salespilot-mysql"; then
    echo "✅ Services are running"
else
    echo "❌ Services failed to start"
    docker-compose ps
    docker-compose logs --tail=50
    exit 1
fi

# Install dependencies
echo "📦 Installing dependencies..."
docker-compose exec -T app composer install --no-interaction --optimize-autoloader

# Run migrations
echo "🔄 Running migrations..."
docker-compose exec -T app php artisan migrate --force

# Clear cache
echo "⚡ Optimizing application..."
docker-compose exec -T app php artisan optimize:clear
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan view:cache
docker-compose exec -T app php artisan route:cache

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage/ /var/www/html/bootstrap/cache/ /var/www/html/public/uploads/
docker-compose exec -T app chmod -R 775 /var/www/html/storage/ /var/www/html/bootstrap/cache/ /var/www/html/public/uploads/

# Health check
echo "🔍 Health check..."
sleep 10

if curl -s -f http://localhost:8787 > /dev/null; then
    echo "✅ Application is healthy!"
    echo "🌐 Access: http://89.117.59.206:8787"
else
    echo "⚠️  Health check failed"
    echo "Checking logs..."
    docker-compose logs app --tail=20
fi

echo ""
echo "🎉 Deployment completed at: $(date)"
